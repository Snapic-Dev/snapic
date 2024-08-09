const mix = require('laravel-mix');
require('laravel-mix-purgecss');

/*
 |--------------------------------------------------------------------------
 | Gerenciamento de Assets do Mix
 |--------------------------------------------------------------------------
 |
 | O Mix fornece uma API limpa e fluente para definir alguns passos de build
 | do Webpack para sua aplicação Laravel. Por padrão, estamos compilando o
 | arquivo Sass para a aplicação, bem como agrupando todos os arquivos JS.
 |
 */

let outputPath = 'public/css/theme';
if(typeof process.env.npm_config_outputPath !== 'undefined'){
    outputPath = process.env.npm_config_outputPath;
}

let resourcesPath = 'resources/sass/';
if(typeof process.env.npm_config_resourcePath !== 'undefined'){
    resourcesPath = process.env.npm_config_resourcePath;
}

mix
    .sass(resourcesPath+'/bootstrap.scss', outputPath)
    .sass(resourcesPath+'/bootstrap.rtl.scss', outputPath)
    .sass(resourcesPath+'/bootstrap.dark.scss', outputPath)
    .sass(resourcesPath+'/bootstrap.rtl.dark.scss', outputPath);
