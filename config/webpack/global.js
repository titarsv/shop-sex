'use strict';

var domain = 'test.lh'; // домен сайта
var ab = ''; // a, b или ничего
var ab_dir = '/2/'; // директория для AB-теста

// Depends
var path         = require('path');
var glob         = require('glob');
var webpack      = require('webpack');
var Manifest     = require('manifest-revision-webpack-plugin');
var TextPlugin   = require('extract-text-webpack-plugin');
var autoprefixer = require('autoprefixer');
var HtmlPlugin   = require('html-webpack-plugin');

/**
 * Global webpack config
 * @param  {[type]} _path [description]
 * @return {[type]}       [description]
 */
module.exports = function(_path, ENV) {
  // define local variables
  var rootAssetPath = _path + 'resources/assets';

  // return objecy
  var webpackConfig = {
    // Точки входа
    entry: {
      application: _path + '/resources/assets/app.js',
      vendors: []
    },

    // Файлы вывода
    output: {
      path: path.join(_path, 'public'),
      chunkFilename: '[id].bundle.[chunkhash].js',
      publicPath: ab == 'b' ? ab_dir : '/'
    },

    // resolves modules
    resolve: {
      extensions: ['', '.js'],
      modulesDirectories: ['node_modules'],
      alias: {
        _svg: path.join(_path, 'resources/assets', 'assets', 'svg'),
        _fonts: path.join(_path, 'resources/assets', 'assets', 'fonts'),
        _modules: path.join(_path, 'resources/assets', 'modules'),
        _images: path.join(_path, 'resources/assets', 'assets', 'images'),
        _stylesheets: path.join(_path, 'resources/assets', 'assets', 'stylesheets'),
        _templates: path.join(_path, 'resources/assets', 'assets', 'templates')
      }
    },

    // modules resolvers
    module: {
      loaders: [
        { test: /\.html$/, loaders: [ 'html-loader?attrs=img:src link:href img:data-src a:data-mfp-src  source:src img:data-lazy', 'purifycss-loader' ] },
        { loader: 'babel',
          test: /\.js$/,
          query: {
            presets: ['es2015'],
            ignore: ['node_modules', 'bower_components']
          }
        }
      ]
    },

    // post css
    postcss: [autoprefixer({ browsers: ['last 5 versions'] })],

    sassLoader: {
      outputStyle:    'expanded',
      sourceMap:      'true'
    },

    // load plugins
    plugins: [
      // new webpack.optimize.CommonsChunkPlugin('vendors', 'assets/js/vendors.js'),
      new TextPlugin('assets/css/[name].css'),
      new Manifest(path.join(_path + '/config', 'manifest.json'), {
        rootAssetPath: rootAssetPath,
        ignorePaths: ['.DS_Store']
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery"
      }),
      new webpack.DllReferencePlugin({
        context: __dirname,
        manifest: require('../../resources/assets/vendor/vendor-manifest.json')
      })
    ]
  };

  webpackConfig.cache = true;
  webpackConfig.devtool = "eval";

  webpackConfig.module.loaders[webpackConfig.module.loaders.length] = {
    test: /\.scss$/,
    loaders: ['style-loader', 'css-loader?sourceMap', 'sass-loader?sourceMap']
  };

  webpackConfig.module.loaders[webpackConfig.module.loaders.length] = {
    test: /\.(css|ico|png|jpg|jpeg|gif)$/i,
    loaders: ['url-loader?limit=4096&context=' + rootAssetPath + '&name=assets/static/[ext]/[name]_[hash].[ext]']
  };

  webpackConfig.module.loaders[webpackConfig.module.loaders.length] = {
    test: /\.woff(2)?(\?[a-z0-9=&.]+)?$/i,
    loaders: ['url-loader?limit=4096&context=' + rootAssetPath + '&name=assets/static/[ext]/[name]_[hash].[ext]']
  };

  webpackConfig.module.loaders[webpackConfig.module.loaders.length] = {
    test: /\.(ttf|eot|svg)(\?[a-z0-9=&.]+)?$/i,
    loaders: ['url-loader?limit=4096&context=' + rootAssetPath + '&name=assets/static/[ext]/[name]_[hash].[ext]']
  };

  webpackConfig.plugins[webpackConfig.plugins.length] = new webpack.HotModuleReplacementPlugin();

  webpackConfig.output.publicPath = 'http://localhost:88/'


  let templates = glob.sync(_path + "**/resources/assets/assets/templates/layouts/*.html");

  for(let temp in templates){
      let template = templates[temp].replace(path.join(_path, 'resources/assets', 'assets', 'templates', 'layouts/').replace(/\\/g, "/"), "");
      webpackConfig.plugins[webpackConfig.plugins.length] = new HtmlPlugin({
          title: 'Landing',
          chunks: ['application', 'vendors'],
          filename: template,
          template: path.join(_path, 'resources/assets', 'assets', 'templates', 'layouts', template)
      });
  }

  return webpackConfig;
};
