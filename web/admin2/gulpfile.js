'use strict';

var TASKS_DIR = './tasks';

// Modules
var path = require('path');
var gulp = require('gulp');

// Tasks
require(path.join(__dirname, TASKS_DIR, 'js'));
require(path.join(__dirname, TASKS_DIR, 'amd'));
require(path.join(__dirname, TASKS_DIR, 'css'));
require(path.join(__dirname, TASKS_DIR, 'watch'));

// Default task
gulp.task('default', [ 'compile-scss', 'compile-js-all' ], function(cb) {
  cb();
});



