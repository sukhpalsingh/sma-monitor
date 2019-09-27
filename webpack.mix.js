const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts(
        [
            'node_modules/chart.js/dist/Chart.min.js',
            'node_modules/jquery/dist/jquery.js',
        ],
        'public/js/lib.js'
    )
    .js('resources/js/app.js', 'public/js')
    .copy('resources/css/app.css', 'public/css')
    .styles(
        [
            'node_modules/bootstrap/dist/css/bootstrap.min.css',
        ],
        'public/css/lib.css'
    )
    mix.copyDirectory('resources/img', 'public/img');;
