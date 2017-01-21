'use strict';

// Modules
var fs          = require('fs');
var path        = require('path');
var gulp        = require('gulp');
var runSequence = require('run-sequence').use(gulp);

var config = require('../config.js');

// Gulp tasks
var gulpClean        = require('gulp-clean');
var gulpSourcemaps   = require('gulp-sourcemaps');
var gulpSass         = require('gulp-sass');
var gulpAutoprefixer = require('gulp-autoprefixer');
var gulpRename       = require('gulp-rename');
var gulpMinifyCss    = require('gulp-minify-css');
var cssFlip          = require('css-flip');
var through          = require('through2');

// Paths
var scssPath       = path.join(__dirname, '..', config.paths.scss);
var customScssPath = path.join(__dirname, '..', config.paths.customScss);
var cssDistPath    = path.join(__dirname, '..', config.paths.distCss);

// Remove old CSS files
gulp.task('clean-css', function() {
  return gulp
    .src(cssDistPath)
    .pipe(gulpClean({ force: true }));
});

// Create task for each base CSS file

var scssTasks = [];

config.sources.scss.forEach(function(fileName) {
  var taskName = 'compile-scss-' + fileName.replace(/\.scss$/, '');
  var filePath = path.join(scssPath, fileName);

  if (!fs.existsSync(filePath)) { return; }

  scssTasks.push(taskName);

  gulp.task(taskName, function() {
    return gulp
      .src(filePath)
      .pipe(gulpSourcemaps.init())
      .pipe(gulpSass().on('error', gulpSass.logError))
      .pipe(gulpAutoprefixer({
        browsers: [ '> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'IE 9' ],
      }))
      .pipe(gulpSourcemaps.write('.', { sourceRoot: path.relative(cssDistPath, scssPath) }))
      .pipe(gulp.dest(cssDistPath));
  });
});

// Create rtl direction
gulp.task('compile-rtl-css', function() {
  const workingPath = path.join(__dirname, '..', config.paths.dist);

  return gulp
    .src([ path.join(workingPath, '**/*.css'), '!' + path.join(workingPath, '**/*.rtl.css') ])
    .pipe(gulpRename({ suffix: '.rtl' }))
    .pipe(through.obj(function(file, _enc_, cb) {
      file.contents = new Buffer(cssFlip(file.contents.toString('utf8')));
      cb(null, file);
    }))
    .pipe(gulp.dest(workingPath));
});

// Minify CSS files
gulp.task('minify-css', function() {
  return gulp
    .src(path.join(cssDistPath, '*.css'))
    .pipe(gulpRename({ suffix: '.min' }))
    .pipe(gulpMinifyCss())
    .pipe(gulp.dest(cssDistPath));
});

gulp.task('compile-themes', function() {
  var themes = config.themes.map(function(fileName) {
    return path.join(scssPath, 'themes', fileName, fileName + '.scss');
  });

  var customThemes = config.customThemes.map(function(fileName) {
    return path.join(customScssPath, 'themes', fileName, fileName + '.scss');
  });

  return gulp
    .src(themes.concat(customThemes))
    .pipe(gulpSourcemaps.init())
    .pipe(gulpSass().on('error', gulpSass.logError))
    .pipe(gulpAutoprefixer({
      browsers: [ '> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'IE 9' ],
    }))
    .pipe(gulpSourcemaps.write('.', { sourceRoot: path.relative(cssDistPath, path.join(scssPath)) }))
    .pipe(gulp.dest(path.join(cssDistPath, 'themes')));
});

gulp.task('minify-themes', function() {
  return gulp
    .src(path.join(cssDistPath, 'themes', '*.css'))
    .pipe(gulpRename({ suffix: '.min' }))
    .pipe(gulpMinifyCss())
    .pipe(gulp.dest(path.join(cssDistPath, 'themes')));
});

// Compile SCSS
gulp.task('compile-scss', function(cb) {
  if (!config.compileSources) {
    console.log('Skip SCSS sources compilation');
    return cb();
  }

  const tasks = [ 'clean-css', scssTasks, 'compile-themes' ];

  if (config.includeRtlSupport) {
    tasks.push('compile-rtl-css');
  }


  if (scssTasks.length) {
    return runSequence.apply(null, tasks.concat([ 'minify-css', 'minify-themes','projeto', cb ]));
  }


  tasks.push('projeto');

  console.log('No SCSS sources found!');
  cb();
});


gulp.task('projeto', function () {

  gulp.src(path.join(config.paths.custom, '/custom.css'))
      .pipe(gulpRename('projeto.min.css'))
      .pipe(gulpMinifyCss())
      .pipe(gulpAutoprefixer({
        browsers: [ '> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'IE 9' ],
      }))
      .pipe(gulp.dest(cssDistPath))
});
