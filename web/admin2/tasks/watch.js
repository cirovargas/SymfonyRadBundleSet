'use strict';

// Modules
var fs   = require('fs');
var path = require('path');
var gulp = require('gulp');

var config = require('../config.js');

// Paths
var scssPath       = path.join(__dirname, '..', config.paths.scss);
var jsSrcPath      = path.join(__dirname, '..', config.paths.jsSrc);
var customScssPath = path.join(__dirname, '..', config.paths.customScss);
var customJsPath   = path.join(__dirname, '..', config.paths.customJs);


// Watch task
gulp.task('watch', function() {
  var scssWatchPaths = [path.join(scssPath, '**/*')];
  var jsWatchPaths   = [path.join(jsSrcPath, '**/*')];

  try {
    fs.statSync(customScssPath);
    scssWatchPaths.push(path.join(customScssPath, '**/*'));
    scssWatchPaths.push(path.join(__dirname, '..', config.paths.custom, 'custom.scss'));
  } catch (_e_) {}

  try {
    fs.statSync(customJsPath);
    jsWatchPaths.push(path.join(customJsPath, '**/*'));
  } catch (_e_) {}

  gulp.watch(scssWatchPaths, ['compile-scss']);
  gulp.watch(jsWatchPaths, ['compile-js-all']);
});
