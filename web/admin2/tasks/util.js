'use strict';

var fs     = require('fs');
var path   = require('path');

var config = require('../config.js');

var libsPath        = path.join(__dirname, '..', config.paths.libs);
var bsJsPath        = path.join(__dirname, '..', config.paths.bsJs);
var jsSrcPath       = path.join(__dirname, '..', config.paths.jsSrc);
var jsBuildPath     = path.join(__dirname, '..', config.paths.jsBuild);
var customJsPath    = path.join(__dirname, '..', config.paths.customJs);
var customBuildPath = path.join(__dirname, '..', config.paths.customBuild);

exports.checkJsPathExistence = function(jsPath) {
  var srcPath;

  if (jsPath.indexOf(jsBuildPath) === 0) {
    srcPath = jsPath.replace(jsBuildPath, jsSrcPath);
  } else if (jsPath.indexOf(customBuildPath) === 0) {
    srcPath = jsPath.replace(customBuildPath, customJsPath);
  } else {
    srcPath = jsPath;
  }

  try {
    fs.statSync(srcPath);
  } catch (_e_) {
    console.log(_e_);
    throw new Error('Path "' + srcPath + '" not found.');
  }

  return jsPath;
};

exports.normalizePathSeparators = function(filePath) {
  return filePath.replace(/\\/g, '/');
};

exports.getAmdFilePath = function(filePath) {
  if (filePath.indexOf(jsBuildPath) !== -1) {
    return exports.normalizePathSeparators(filePath.replace(jsBuildPath + path.sep, ''));
  }

  if (filePath.indexOf(customBuildPath) !== -1) {
    return exports.normalizePathSeparators(filePath.replace(customBuildPath, 'pixeladmin/custom'));
  }

  if (filePath.indexOf(bsJsPath) !== -1) {
    return exports.normalizePathSeparators(filePath.replace(bsJsPath, 'bootstrap'));
  }

  var parts    = exports.normalizePathSeparators(filePath.replace(libsPath  + path.sep, '')).split('/');
  var dirName  = parts[0];
  var baseName = parts.slice(-1)[0];

  // return 'libs/' + dirName + '/' + baseName;
  return 'libs/' + baseName;
}

exports.getAmdNamespace = function(str) {
  var parts = str.split('-');

  for (var i = 0, l = parts.length; i < l; i++) {
    parts[i] = parts[i].charAt(0).toUpperCase() + parts[i].slice(1);
  }

  return parts.join('').replace(/\./g, '_');
}
