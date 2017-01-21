'use strict';

// Modules
var fs          = require('fs');
var path        = require('path');
var gulp        = require('gulp');
var runSequence = require('run-sequence').use(gulp);

var config = require('../config.js');
var util   = require('./util');

// Gulp tasks
var gulpStripLine  = require('gulp-strip-line');
var gulpSourcemaps = require('gulp-sourcemaps');
var gulpBabel      = require('gulp-babel');
var gulpClean      = require('gulp-clean');
var gulpConcat     = require('gulp-concat');
var gulpReplace    = require('gulp-replace');
var gulpRename     = require('gulp-rename');
var gulpUglify     = require('gulp-uglify');

// Paths
var jsPath          = path.join(__dirname, '..', config.paths.js);
var jsSrcPath       = path.join(__dirname, '..', config.paths.jsSrc);
var jsBuildPath     = path.join(__dirname, '..', config.paths.jsBuild);
var customPath      = path.join(__dirname, '..', config.paths.custom);
var customJsPath    = path.join(__dirname, '..', config.paths.customJs);
var customBuildPath = path.join(__dirname, '..', config.paths.customBuild);
var jsDistPath      = path.join(__dirname, '..', config.paths.distJs);

var buildCustomJs;

try {
  fs.statSync(customJsPath);
  buildCustomJs = true;
} catch (_e_) {
  buildCustomJs = false
}


// Compile JS sources
gulp.task('compile-dist-js', function() {
  return gulp
    .src(path.join(jsSrcPath, '**/*.js'))
    .pipe(gulpStripLine([/^(import|export)/]))
    .pipe(gulpSourcemaps.init())
    .pipe(gulpBabel({
      presets: ['es2015'],
    }))
    .pipe(gulpStripLine([/^((?:"|')use strict(?:"|'))/]))
    .pipe(gulpSourcemaps.write('.', { sourceRoot: path.relative(jsBuildPath, jsSrcPath) + '/' }))
    .pipe(gulp.dest(jsBuildPath));
});

// Compile custom JS sources
gulp.task('compile-custom-js', function() {
  return gulp
    .src(path.join(customJsPath, '**/*.js'))
    .pipe(gulpStripLine([/^(import|export)/]))
    .pipe(gulpSourcemaps.init())
    .pipe(gulpBabel({
      presets: ['es2015'],
    }))
    .pipe(gulpStripLine([/^((?:"|')use strict(?:"|'))/]))
    .pipe(gulpSourcemaps.write('.', { sourceRoot: path.relative(customBuildPath, customJsPath) + '/' }))
    .pipe(gulp.dest(customBuildPath));
});

// Remove old JS files
gulp.task('clean-js', function() {
  return gulp
    .src(jsDistPath)
    .pipe(gulpClean({ force: true }));
});

// Create task for each base JS file

var jsTasks = [];

config.sources.js.forEach(function(fileName) {
  var taskName = 'compile-js-' + fileName.replace(/\.js$/, '');
  var filePath = path.join(jsPath, fileName);

  if (!fs.existsSync(filePath)) { return; }

  var sources = [];

  // Collect js sources to require
  fs.readFileSync(filePath, 'utf-8')
    .replace(/\/\/=\s+require\s+(.*)\s*/gi, function(_m_, $1) {
      sources.push(util.checkJsPathExistence(path.join(jsPath, $1)));
      return '';
    });

  // Collect custom js sources to require
  if (fileName === 'pixeladmin.js' && buildCustomJs) {
    fs.readFileSync(path.join(customPath, 'custom.js'), 'utf-8')
      .replace(/\/\/=\s+require\s+(.*)\s*/gi, function(_m_, $1) {
        sources.push(util.checkJsPathExistence(path.join(customPath, $1)));
        return '';
      });
  }

  jsTasks.push(taskName);

  gulp.task(taskName, function() {
    return gulp
      .src(sources, { base: '.' })
      .pipe(gulpSourcemaps.init({ loadMaps: true }))
      .pipe(gulpConcat(fileName))
      .pipe(gulpSourcemaps.write('.', { sourceRoot: '../../' }))
      .pipe(gulp.dest(jsDistPath));
  });
});

// Minify JS files
gulp.task('minify-js', function() {
  return gulp
    .src(path.join(jsDistPath, '*.js'))
    .pipe(gulpRename({ suffix: '.min' }))
    .pipe(gulpUglify())
    .pipe(gulp.dest(jsDistPath));
});

// Fix JS sourcemap paths
gulp.task('fix-js-sourcemap', function() {
  return gulp
    .src(path.join(jsDistPath, '*.js.map'))
    .pipe(gulpReplace('"js/dist/', '"js/src/'))
    .pipe(gulp.dest(jsDistPath));
});

// Base JS task
gulp.task('compile-js', function(cb) {
  if (!config.compileSources) {
    console.log('Skip JS sources compilation');
    return cb();
  }

  if (jsTasks.length) {
    var tasks = ['compile-dist-js'];

    if (buildCustomJs) {
      tasks.push('compile-custom-js');
    }

    return runSequence.apply(null, tasks.concat([ 'clean-js', jsTasks, 'minify-js', 'fix-js-sourcemap', cb ]));
  }

  console.log('No javascript sources found!');
  cb();
});

// Base JS task
gulp.task('compile-js-all', function(cb) {
  return runSequence('compile-js', 'compile-amd', cb);
});
