'use strict';

let $ = require('jquery');
// require('../../../../../node_modules/lazyloadxt/dist/jquery.lazyloadxt');
// require('../../../../../node_modules/lazyloadxt/dist/jquery.lazyloadxt.picture');
require('./jquery.lazyloadxt');
require('./jquery.lazyloadxt.picture');

require('./index.scss');


module.exports = function () {
    // $(window).lazyLoadXT();
    $('.lazyload').lazyLoadXT({
        edgeY: 200,
        srcAttr: 'data-img'
    });
};
