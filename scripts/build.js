const fs = require('fs');
const path = require('path');

const PACKAGE_NAME = '@andreilupu/wp-sockets-js';
const ASSETS_DIR = path.resolve(__dirname, '../assets');
const NODE_MODULES_DIR = path.resolve(__dirname, '../node_modules');
const PKG_DIR = path.join(NODE_MODULES_DIR, PACKAGE_NAME);

if (!fs.existsSync(ASSETS_DIR)) {
    fs.mkdirSync(ASSETS_DIR);
}

// Copy JS (UMD -> index.js)
// We use UMD because it's compatible with WordPress enqueuing
const srcJs = path.join(PKG_DIR, 'dist/index.umd.js');
const destJs = path.join(ASSETS_DIR, 'index.js');

if (fs.existsSync(srcJs)) {
    fs.copyFileSync(srcJs, destJs);
    console.log(`Copied ${srcJs} to ${destJs}`);
} else {
    console.error(`Source file not found: ${srcJs}`);
    process.exit(1);
}

// Copy CSS
const srcCss = path.join(PKG_DIR, 'dist/index.css');
const destCss = path.join(ASSETS_DIR, 'index.css');
if (fs.existsSync(srcCss)) {
    fs.copyFileSync(srcCss, destCss);
    console.log(`Copied ${srcCss} to ${destCss}`);
}

// Generate asset file
// We need to read the package.json of the JS lib to get the version
const pkgJson = require(path.join(PKG_DIR, 'package.json'));
const version = pkgJson.version;

// Dependencies required by the React app
// Note: wp-dom-ready is important for the initialization script
const dependencies = [
    'wp-element',
    'wp-hooks',
    'wp-components',
    'wp-data',
    'wp-compose',
    'wp-i18n',
    'wp-core-data',
    'wp-dom-ready'
];

const assetContent = `<?php return array('dependencies' => array('${dependencies.join("', '")}'), 'version' => '${version}');`;
const destAsset = path.join(ASSETS_DIR, 'index.asset.php');
fs.writeFileSync(destAsset, assetContent);
console.log(`Generated ${destAsset}`);
