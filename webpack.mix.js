const mix = require('laravel-mix')

mix.browserSync('local.photo-app.com')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .version()