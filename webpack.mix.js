// const mix = require('laravel-mix');
// const path = require('path');

// /*
//  |--------------------------------------------------------------------------
//  | Mix Asset Management
//  |--------------------------------------------------------------------------
//  |
//  | Mix provides a clean, fluent API for defining some Webpack build steps
//  | for your Laravel application. By default, we are compiling the Sass
//  | file for the application as well as bundling up all the JS files.
//  |
//  */

// // mix.js('resources/js/app.js', 'public/js')
// //     .vue()
// //     .sass('resources/sass/app.scss', 'public/css');


// /*
//  |--------------------------------------------------------------------------
//  | Mix Asset Management
//  |--------------------------------------------------------------------------
//  |
//  | Mix provides a clean, fluent API for defining some Webpack build steps
//  | for your Laravel applications. By default, we are compiling the CSS
//  | file for the application as well as bundling up all the JS files.
//  |
//  */

//  mix.js('resources/js/app.js', 'public/js')
//  .vue()
//  .css('resources/css/app.css', 'public/css')
// //  .postCss('resources/css/app.css', 'public/css', [
// //      //
// //  ])
//  .webpackConfig({
//     resolve: {
//         extensions: ['.js', '.vue', '.scss', '.css'],
//         alias: {
//             '@': path.resolve(__dirname, 'resources/js'),
//         },
//       }
// });