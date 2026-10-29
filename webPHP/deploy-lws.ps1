<#
    Construit hotelio-lws.zip, prêt à être dézippé dans ~/hotelio sur l'hébergement LWS.

    - Copie le projet dans un dossier temporaire (le webPHP local n'est pas modifié)
    - Installe les dépendances Composer sans les paquets de dev
    - Exclut .env, fichiers Docker, tests, logs, caches et la base SQLite locale

    Avec -SansVendor : produit hotelio-lws-code.zip, sans vendor/ (bien plus léger).
    Il faut alors lancer `composer install --no-dev --optimize-autoloader` en SSH sur le serveur.

    Usage (depuis webPHP) :  powershell -ExecutionPolicy Bypass -File .\deploy-lws.ps1 [-SansVendor]
#>

param([switch]$SansVendor)

$ErrorActionPreference = 'Stop'

$source  = $PSScriptRoot
$staging = Join-Path $env:TEMP 'hotelio-lws-build'
$archive = Join-Path $source $(if ($SansVendor) { 'hotelio-lws-code.zip' } else { 'hotelio-lws.zip' })

Write-Host '==> Préparation du dossier temporaire'
if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
if (Test-Path $archive) { Remove-Item $archive -Force }

# Dossiers et fichiers qui ne partent jamais en production
$excludeDirs  = @('node_modules', '.git', '.phpunit.cache',
                  (Join-Path $source 'tests'), (Join-Path $source 'docker'),
                  (Join-Path $source 'public\storage'), (Join-Path $source 'public\hot'))
$excludeFiles = @('.env', '.env.backup', '.env.production', '.phpunit.result.cache',
                  'Dockerfile', 'docker-compose.yml', 'phpunit.xml', 'database.sqlite',
                  'deploy-lws.ps1', 'hotelio-lws.zip', 'hotelio-lws-code.zip', '*.log')
if ($SansVendor) { $excludeDirs += Join-Path $source 'vendor' }

robocopy $source $staging /E /NFL /NDL /NJH /NJS /NP /XD $excludeDirs /XF $excludeFiles | Out-Null
# robocopy : codes 0 à 7 = succès
if ($LASTEXITCODE -ge 8) { throw "robocopy a échoué (code $LASTEXITCODE)" }

# Caches générés en local (chemins Windows, providers de dev) : régénérés plus bas ou sur le serveur
Get-ChildItem (Join-Path $staging 'bootstrap\cache') -Filter '*.php' | Remove-Item -Force
foreach ($dir in 'framework\cache\data', 'framework\sessions', 'framework\views', 'pail') {
    $path = Join-Path $staging "storage\$dir"
    if (Test-Path $path) {
        Get-ChildItem $path -Force | Where-Object Name -ne '.gitignore' | Remove-Item -Recurse -Force
    }
}

if (-not $SansVendor) {
    Write-Host '==> composer install --no-dev'
    Push-Location $staging
    try {
        composer install --no-dev --optimize-autoloader --no-interaction --no-progress
        if ($LASTEXITCODE -ne 0) { throw 'composer install a échoué' }
    } finally {
        Pop-Location
    }
}

Write-Host '==> Création de l''archive'
# tar (bsdtar de Windows) écrit des chemins avec "/" : l'archive se dézippe correctement sous Linux,
# contrairement à Compress-Archive de PowerShell 5.1.
tar -a -c -f $archive -C $staging .
if ($LASTEXITCODE -ne 0) { throw 'La création du zip a échoué' }

Remove-Item $staging -Recurse -Force

$size = [math]::Round((Get-Item $archive).Length / 1MB, 1)
Write-Host ''
Write-Host "OK : $archive ($size Mo)"
Write-Host 'Étapes suivantes : envoyer le zip dans ~/hotelio, le dézipper, copier .env.lws.example en .env et le remplir.'
if ($SansVendor) {
    Write-Host 'Puis en SSH :  cd ~/hotelio && composer install --no-dev --optimize-autoloader'
}
