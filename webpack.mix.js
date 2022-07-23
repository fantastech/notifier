const mix = require('laravel-mix');
require('palette-webpack-plugin/src/mix');

mix
  .setPublicPath('./assets');

mix
  .sass('resources/styles/admin.scss', 'css')
  .options({ processCssUrls: false });

mix
 .copyDirectory('resources/scripts', 'assets/js')
 .copyDirectory('resources/images', 'assets/images');
