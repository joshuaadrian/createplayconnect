var
  gulp            = require('gulp'),
  gutil           = require('gulp-util'), // Errors with load plugins... Need to fix
  pngquant        = require('imagemin-pngquant'), // Errors with load plugins... Need to fix
  gulpLoadPlugins = require('gulp-load-plugins'),
  plugins         = gulpLoadPlugins({camelize: true})
;

gulp.task('default', ['watch', 'css', 'js', 'images']);

gulp.task('watch', function() {
  gulp.watch('../css/**/*.scss', ['css']);
  gulp.watch('../js/**/*.js', ['js']);
  gulp.watch('../images/**/*.{jpg,png,gif,svg}', ['images']);
  plugins.livereload.listen();
  gulp.watch('../build/**').on('change', plugins.livereload.changed);
});

gulp.task('css', function() {
  gulp.src('../css/*.scss')
    .pipe(plugins.sourcemaps.init())
    .pipe(plugins.sass({style: 'expanded'})).on('error', gutil.log)
    .pipe(plugins.autoprefixer())
    .pipe(plugins.sourcemaps.write())
    .pipe(plugins.minifyCss({keepBreaks:false}))
    .pipe(gulp.dest('../build/css/'))
  ;
});

gulp.task('js', function() {

  // All Non Concat JS
  gulp.src(['../js/*.js','!../js/lettering.js','!../js/fittext.js','!../js/colorbox-config.js','!../js/colorbox.js','!../js/app.js'])
    .pipe(plugins.uglify())
    .pipe(plugins.rename({suffix: ".min"}))
    .pipe(gulp.dest('../build/js/'))
  ;

  // All Concat JS
  gulp.src(['../js/fittext.js','../js/lettering.js','../js/colorbox-config.js','../js/colorbox.js','../js/app.js' ])
    .pipe(plugins.uglify())
    .pipe(plugins.sourcemaps.init())
    .pipe(plugins.concat('scripts.min.js'))
    .pipe(plugins.sourcemaps.write())
    .pipe(gulp.dest('../build/js/'));
  ;
  
});

gulp.task('images', function () {
  return gulp.src('../images/*')
    .pipe(plugins.imagemin({
        progressive: true,
        svgoPlugins: [{removeViewBox: false}],
        use: [pngquant()]
    }))
    .pipe(gulp.dest('../build/images/'));
});