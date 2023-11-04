const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .extract()
    .js('resources/js/stream.js', 'public/js')
    .vue(3)
    // .sass('resources/css/app.scss', 'public/css')
    .postCss("resources/css/input.css", "public/css/app.css", [
        require("tailwindcss"),
    ])
    .version();
