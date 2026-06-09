// Require Plugins
// ---------------------------------------------------------------------------
var browserSync = require("browser-sync").create();
var bulkSass = require('gulp-sass-bulk-import');
var changed  = require('gulp-changed');
var cleanCSS = require('gulp-clean-css');
var concat = require('gulp-concat');
var gulp = require('gulp');
var pug = require('gulp-pug');
var htmlmin = require('gulp-htmlmin');
var imagemin  = require ('gulp-imagemin');
var mozjpeg = require('imagemin-mozjpeg');
var newer = require('gulp-newer');
var notify  = require('gulp-notify');
var plumber = require('gulp-plumber');
var pngquant = require('imagemin-pngquant');
var rename = require('gulp-rename');
var rimraf = require('rimraf');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var watch = require("gulp-watch");



// Common Settings
// ---------------------------------------------------------------------------
var srcDir = 'src/';
var distDir = 'dist/';

var gulpRanInThisFolder = process.cwd();
var res = gulpRanInThisFolder.split("/");
var projectName = res[res.length - 1];
var projectUrl = projectName + ".test";



// Build
// ---------------------------------------------------------------------------
gulp.task('build', [
	'html',
	'pug',
	'php',
	'sass',
	'js',
	'images',
	'fonts',
  'copy',
  'wordpress',
]);



// HTML
// ---------------------------------------------------------------------------
gulp.task('html', function(){
	gulp.src([
		srcDir+'*.html',
		srcDir+'**/*.html',
	])
	.pipe(htmlmin({
		removeComments: true,
		collapseWhitespace: true,
		collapseBooleanAttributes: true,
		removeAttributeQuotes: true,
		removeRedundantAttributes: true,
		removeEmptyAttributes: true,
		removeScriptTypeAttributes: true,
		removeStyleLinkTypeAttributes: true,
		removeOptionalTags: true
	}))
	.pipe(gulp.dest(distDir));
	// .pipe(notify('HTML Compile Done.'));
});



// PUG
// ---------------------------------------------------------------------------
gulp.task('pug', function() {
  return gulp.src([
	  srcDir+'!(_)*.pug',
	  srcDir+'!(_)**/!(_)*.pug',
  ])
  .pipe(plumber({
	  errorHandler: notify.onError("Error: <%= error.message %>")
  }))
  .pipe(pug({
    pretty: true
  }))
  .pipe(gulp.dest(distDir));
});



// PHP
// ---------------------------------------------------------------------------
gulp.task('php', function(){
	gulp.src([
		srcDir+'*.php',
		srcDir+'**/*.php',
	])
	.pipe(gulp.dest(distDir));
	// .pipe(notify('PHP Compile Done.'));
});



// Sass
// ---------------------------------------------------------------------------
gulp.task('sass', function(){
	var cssDir = distDir + 'css/';
	gulp.src([
		srcDir+'scss/**/*.scss'
	])
	.pipe(plumber({
		errorHandler: notify.onError("Error: <%= error.message %>")
	}))
	.pipe(bulkSass())
	.pipe(sourcemaps.init())
	.pipe(sass({
		errLogToConsole: false,
		includePaths: srcDir,
	}))
	//.pipe(sourcemaps.write('./'))
	.pipe(gulp.dest(cssDir))
	.pipe(cleanCSS())
	.pipe(rename({suffix: '.min'}))
	.pipe(gulp.dest(cssDir));
	// .pipe(notify('Sass Compile Done.'));
});



// JS
// ---------------------------------------------------------------------------
gulp.task('js', function() {
	var jsDir = distDir + 'js/';
	gulp.src([
		srcDir+'js/*',
		srcDir+'js/**/*.js'
	])
	.pipe(newer(jsDir))
	.pipe(plumber({
		errorHandler: notify.onError("Error: <%= error.message %>")
	}))
	.pipe(gulp.dest(jsDir))
	.pipe(uglify())
	.pipe(rename({suffix: '.min'}))
	.pipe(gulp.dest(jsDir));
	// .pipe(notify('Js Compile Done.'));
});





// Images
// ---------------------------------------------------------------------------
gulp.task('images', function(){
	var imageDir = distDir + 'images/';
	return gulp.src([
		srcDir+'images/**/*'
	])
	.pipe(newer(imageDir))
	.pipe(imagemin([
		pngquant({
			// quality: '65-80',
			// speed: 1,
			// floyd: 0
		}),
		mozjpeg({
			// quality:85,
			progressive: true
		}),
		imagemin.svgo(),
		imagemin.optipng(),
		imagemin.gifsicle()
	]))
	.pipe(gulp.dest(imageDir));
	// .pipe(notify('Image Copy & Minify Done.'));
});



// Copy
// ---------------------------------------------------------------------------
gulp.task("copy", function() {
	gulp.src([
		srcDir + '**/*.{mp3,ogg,wav,mp4,mov,swf,pdf}'
	])
	.pipe(gulp.dest(distDir));
});


// WordPress
// ---------------------------------------------------------------------------
gulp.task('wordpress', function() {

	gulp.src([
		srcDir + 'wp/**/*'
	])
	.pipe(gulp.dest(distDir + "wp"));
});



// Fonts
// ---------------------------------------------------------------------------
gulp.task('fonts', function(){
	var fontDir = distDir + 'fonts/';
	return gulp.src([
		srcDir+'fonts/**/*'
	])
	.pipe(plumber({
		errorHandler: notify.onError("Error: <%= error.message %>")
	}))
	.pipe(gulp.dest(fontDir));
	// .pipe(notify('Fonts Copy Done.'));
});



// BrowserSync
// ---------------------------------------------------------------------------
gulp.task('browser-sync', function() {
	browserSync.init({
		open: 'external',
		// open: false,
		port: 8000,
		host: projectUrl,
		proxy: projectUrl
	});
});



// Watch
// ---------------------------------------------------------------------------
gulp.task('watch', function(){
	watch([
		srcDir+'!(_)*.html',
		srcDir+'!(_)**/!(_)*.html',
	], function(event){
		gulp.start('html');
	});
	watch([
		srcDir+'!(_)*.pug',
		srcDir+'!(_)**/!(_)*.pug',
	], function(event){
		gulp.start('pug');
	});
	watch([
		srcDir+'*.php',
		srcDir+'**/*.php',
	], function(event){
		gulp.start('php');
	});
	watch(srcDir+'scss/**/*.scss', function(event){
		gulp.start('sass');
	});
	watch(srcDir+'js/!(_)**/!(_)*.js', function(event){
		gulp.start('js');
	});
	watch(srcDir+'images/**/*', function(event){
		gulp.start('images');
	});
	watch(srcDir+'**/**/*.{mp3,ogg,wav,mp4,mov,swf}', function(event){
		gulp.start('copy');
	});
	watch(srcDir+'fonts/**/*', function(event){
		gulp.start('fonts');
	});
	watch(srcDir + 'wp/**/*', function(event){
		gulp.start('wordpress');
	});
	watch(distDir+'**/*', browserSync.reload);
});

// Clean dist Folder
// ---------------------------------------------------------------------------
gulp.task('clean', function (cb) {
	rimraf('./dist', cb);
});



// gulp default
// ---------------------------------------------------------------------------
gulp.task('default', ['build','browser-sync','watch']);
