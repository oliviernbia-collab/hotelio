const { getDefaultConfig } = require('expo/metro-config');
const path = require('path');

const config = getDefaultConfig(__dirname);

// Certains packages (ex. react-i18next@17) publient un champ "exports" dont les
// require() relatifs internes (avec extension .js explicite) ne se résolvent pas
// correctement via l'algorithme "package exports" de Metro. On désactive cette
// résolution pour retrouver le comportement classique par chemin de fichier.
config.resolver.unstable_enablePackageExports = false;

// Exclure le dossier backend (Node.js) du bundle React Native.
// On utilise le chemin absolu pour garantir le match sur Windows.
const backendDir = path.resolve(__dirname, 'backend');
const backendEscaped = backendDir.replace(/\\/g, '\\\\');
const backendRegex = new RegExp(`^${backendEscaped}($|\\\\|\\/)`);

// blockList est un tableau dans la config Expo — on ajoute sans écraser.
const existingBlockList = config.resolver.blockList;
if (Array.isArray(existingBlockList)) {
  config.resolver.blockList = [...existingBlockList, backendRegex];
} else if (existingBlockList) {
  config.resolver.blockList = [existingBlockList, backendRegex];
} else {
  config.resolver.blockList = [backendRegex];
}

const expoUiStub      = path.resolve(__dirname, 'shims/expo-ui-stub.js');
const glassEffectStub = path.resolve(__dirname, 'shims/expo-glass-effect-stub.js');

// extraNodeModules overrides node_modules lookup before resolveRequest
config.resolver.extraNodeModules = {
  ...config.resolver.extraNodeModules,
  'expo-glass-effect': glassEffectStub,
};

config.resolver.resolveRequest = (context, moduleName, platform) => {
  if (moduleName === '@expo/ui/jetpack-compose' || moduleName.startsWith('@expo/ui/')) {
    return { type: 'sourceFile', filePath: expoUiStub };
  }
  if (moduleName === 'expo-glass-effect') {
    return { type: 'sourceFile', filePath: glassEffectStub };
  }
  return context.resolveRequest(context, moduleName, platform);
};

module.exports = config;
