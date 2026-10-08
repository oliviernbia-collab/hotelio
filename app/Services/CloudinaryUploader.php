<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudinaryUploader
{
    /**
     * Upload un fichier vers Cloudinary et retourne son URL sécurisée (https).
     * resourceType 'auto' laisse Cloudinary détecter le type (utile pour les justificatifs PDF/image mélangés).
     */
    public static function upload(UploadedFile $file, string $folder, string $resourceType = 'image'): string
    {
        $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder'        => "hotelio/{$folder}",
            'resource_type' => $resourceType,
        ]);

        return $result['secure_url'];
    }

    /**
     * Supprime un fichier Cloudinary à partir de son URL (best-effort).
     * Ignore silencieusement les anciennes valeurs (chemins locaux pré-migration) ou les échecs réseau.
     */
    public static function destroy(?string $url): void
    {
        if (! $url || ! str_contains($url, 'cloudinary.com')) {
            return;
        }

        if (! preg_match('#/upload/(?:v\d+/)?(.+?)\.[a-zA-Z0-9]+$#', $url, $matches)) {
            return;
        }

        try {
            cloudinary()->uploadApi()->destroy($matches[1]);
        } catch (\Throwable) {
            // Suppression distante best-effort : on ne bloque jamais l'opération locale pour ça.
        }
    }
}
