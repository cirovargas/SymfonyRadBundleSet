'use strict';

// Modules
var fs          = require('fs');
var path        = require('path');
var gulp        = require('gulp');
var runSequence = require('run-sequence').use(gulp);

var config = require('../config.js');
var util   = require('./util');

// Gulp tasks
var gulpBabel  = require('gulp-babel');
var gulpClean  = require('gulp-clean');
var gulpUmd    = require('gulp-umd');
var gulpUglify = require('gulp-uglify');

// Paths
var libsPath        = path.join(__dirname, '..', config.paths.libs);
var bsJsPath        = path.join(__dirname, '..', config.paths.bsJs);
var jsPath          = path.join(__dirname, '..', config.paths.js);
var jsSrcPath       = path.join(__dirname, '..', config.paths.jsSrc);
var jsBuildPath     = path.join(__dirname, '..', config.paths.jsBuild);
var customPath      = path.join(__dirname, '..', config.paths.custom);
var customJsPath    = path.join(__dirname, '..', config.paths.customJs);
var customBuildPath = path.join(__dirname, '..', config.paths.customBuild);
var amdDistPath     = path.join(__dirname, '..', config.paths.distAmd);

var buildCustomJs;

try {
  fs.statSync(customJsPath);
  buildCustomJs = true;
} catch (_e_) {
  buildCustomJs = false
}


// Remove old files
gulp.task('clean-amd', function() {
  return gulp
    .src(amdDistPath)
    .pipe(gulpClean({ force: true }));
});

// Compile JS sources
gulp.task('compile-dist-amd', function() {
  return gulp
    .src(path.join(jsSrcPath, '**/*.js'))
    .pipe(gulpBabel({
      presets: ['es2015'],
      plugins: ['transform-es2015-modules-umd'],
    }))
    .pipe(gulp.dest(path.join(amdDistPath, 'pixeladmin')));
});

// Compile custom JS sources
gulp.task('compile-custom-amd', function() {
  return gulp
    .src(path.join(customJsPath, '**/*.js'))
    .pipe(gulpBabel({
      presets: ['es2015'],
      plugins: ['transform-es2015-modules-umd'],
    }))
    .pipe(gulp.dest(path.join(amdDistPath, 'pixeladmin', 'custom')));
});

// Minify AMD modules
gulp.task('minify-amd-modules', function() {
  return gulp
    .src([ path.join(amdDistPath, '**/*.js'), '!' + path.join(amdDistPath, '**/*.min.js') ])
    .pipe(gulpUglify())
    .pipe(gulp.dest(amdDistPath));
});

// Create task for each base JS file

var amdConf    = require('../amd.json');
var amdDeps    = amdConf.dependencies;
var amdExports = amdConf.exports;
var amdTasks   = [];

config.sources.js.forEach(function(fileName) {
  var filePath = path.join(jsPath, fileName);

  if (!fs.existsSync(filePath)) { return; }

  var amdSources = [];
  var sources = [];

  // Collect js sources to require
  fs.readFileSync(filePath, 'utf-8')
    .replace(/\/\/=\s+require\s+(.*)\s*/gi, function(_m_, $1) {
      var fullPath = util.checkJsPathExistence(path.join(jsPath, $1));

      if (fullPath.indexOf(jsPath) !== 0) {
        if (amdDeps[util.getAmdFilePath(fullPath)]) {
          amdSources.push(fullPath);
        } else {
          sources.push(fullPath);
        }
      }

      return '';
    });

  // Collect custom js sources to require
  if (fileName === 'pixeladmin.js' && buildCustomJs) {
    fs.readFileSync(path.join(customPath, 'custom.js'), 'utf-8')
      .replace(/\/\/=\s+require\s+(.*)\s*/gi, function(_m_, $1) {
        var fullPath = util.checkJsPathExistence(path.join(customPath, $1));

        if (fullPath.indexOf(customBuildPath) !== 0) {
          if (amdDeps[util.getAmdFilePath(fullPath)]) {
            amdSources.push(fullPath);
          } else {
            sources.push(fullPath);
          }
        }

        return '';
      });
  }

  if (amdSources.length) {
    var amdTask  = 'compile-amd-' + fileName.replace(/\.js$/, '');

    amdTasks.push(amdTask);

    gulp.task(amdTask, function() {
      return gulp
        .src(amdSources, { base: '.' })
        .pipe(gulpUmd({
          dependencies: function(file) {
            return (amdDeps[util.getAmdFilePath(file.path)] || []);
          },
          exports: function(file) {
            return (amdExports[util.getAmdFilePath(file.path)] || null);
          },
          namespace: function(file) {
            return util.getAmdNamespace(
              file.path.replace(/\\/g, '/').split('/').slice(-1)[0].replace('.js', '')
            );
          }
        }))
        .pipe(gulp.dest(function(file) {
          file.path = util.getAmdFilePath(file.path);
          return amdDistPath;
        }));
    });
  }

  if (sources.length) {
    var copyTask = 'compile-amd-copy-' + fileName.replace(/\.js$/, '');

    amdTasks.push(copyTask);

    gulp.task(copyTask, function() {
      return gulp
        .src(sources, { base: '.' })
        .pipe(gulp.dest(function(file) {
          file.path = util.getAmdFilePath(file.path);
          return amdDistPath;
        }));
    });
  }
});

// Base AMD task
gulp.task('compile-amd', function(cb) {
  if (!config.compileAmd) {
    console.log('Skip AMD compilation');
    return cb();
  }

  if (amdTasks.length) {
    var tasks = [ 'clean-amd', 'compile-dist-amd' ];

    if (buildCustomJs) {
      tasks.push('compile-custom-amd');
    }

    tasks = tasks.concat(amdTasks);

    if (config.minifyAmdModules) {
      tasks.push('minify-amd-modules');
    }

    return runSequence.apply(null, tasks.concat([cb]));
  }

  console.log('No javascript sources found!');
  cb();
});
