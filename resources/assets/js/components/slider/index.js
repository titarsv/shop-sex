'use strict';

let $ = require('jquery');
require('slick-carousel');
require('../../../../../node_modules/slick-carousel/slick/slick.scss');

module.exports = function() {
  $('.slick-slider').each(function() {
    let $this = $(this);
    if ($this.parents('.hidden, .hover-prod-card').length == 0) {
      $this.slick();
    }
  });
};
