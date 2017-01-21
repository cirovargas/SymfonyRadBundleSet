'use strict';

// Modules
var fs   = require('fs');
var path = require('path');
var os   = require('os');

// Constants
var PLATFORM      = os.platform();
var ROOT_PATH     = __dirname;
var CONFIG_PATH   = path.join(ROOT_PATH, 'config.json');

function _detectOS() {
  switch (PLATFORM) {
  case 'win32':
  case 'win64':
    return 'windows';
  case 'darwin':
    return 'osx';
  default:
    return 'linux';
  }
}

function _getScssSources(config) {
  const re = (/^[^_][a-zA-Z0-9_-]+\.scss$/i);

  return fs.readdirSync(path.join(config.paths.root, config.paths.scss))
    .filter(fileName => re.test(fileName));
}

function _getThemes(config, searchPath) {
  const re = (/^[a-zA-Z0-9-]+$/i);

  try {
    fs.statSync(path.join(config.paths.root, searchPath));
  } catch (_e_) {
    return [];
  }

  return fs.readdirSync(path.join(config.paths.root, searchPath))
    .filter(fileName => re.test(fileName));
}

function _getJsSources(config) {
  const re = (/^[a-zA-Z0-9_-]+\.js$/i);

  return fs.readdirSync(path.join(config.paths.root, config.paths.js))
    .filter(fileName => re.test(fileName));
}

function loadConfig() {
  var config = require(CONFIG_PATH);

  // Define paths
  //

  config.paths.root = ROOT_PATH;

  config.paths.scss   = 'scss';
  config.paths.js     = 'js';
  config.paths.libs   = 'libs';
  config.paths.custom = 'custom';

  config.paths.distCss = path.join(config.paths.dist, 'css');
  config.paths.distJs  = path.join(config.paths.dist, 'js');
  config.paths.distAmd = path.join(config.paths.dist, 'js/amd');

  config.paths.jsSrc   = path.join(config.paths.js, 'src');
  config.paths.jsBuild = path.join(config.paths.js, 'build');

  config.paths.bsScss = path.join(config.paths.libs, 'bootstrap-sass/assets/stylesheets/bootstrap');
  config.paths.bsJs   = path.join(config.paths.libs, 'bootstrap-sass/assets/javascripts/bootstrap');

  config.paths.customScss  = path.join(config.paths.custom, 'scss');
  config.paths.customJs    = path.join(config.paths.custom, 'js');
  config.paths.customBuild = path.join(config.paths.custom, 'build');

  //

  config.os = _detectOS();

  config.sources = {
    scss: _getScssSources(config),
    js:   _getJsSources(config),
  };

  config.themes       = _getThemes(config, path.join(config.paths.scss, 'themes'));
  config.customThemes = _getThemes(config, path.join(config.paths.customScss, 'themes'));

  // Get compilation options

  config.compileSources = (typeof config.compileSources === 'undefined') ?
    true :
    config.compileSources;

  config.compileAmd = (config.compileSources && typeof config.compileAmd === 'undefined') ?
    true :
    (config.compileSources && config.compileAmd);

  config.minifyAmdModules = (config.compileAmd && typeof config.minifyAmdModules === 'undefined') ?
    true :
    (config.compileAmd && config.minifyAmdModules);

  config.includeRtlSupport = (config.compileSources && typeof config.includeRtlSupport === 'undefined') ?
    true :
    (config.compileSources && config.includeRtlSupport);

  return config;
}

module.exports = loadConfig();
