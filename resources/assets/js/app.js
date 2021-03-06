/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
//
// const app = new Vue({
//     el: '#app'
// });


'use strict';

// Depends
let $ = require('jquery');
require('./bootstrap');

// Modules
let Lazyload = require('./components/lazyload');
let Forms = require('./components/forms');
let Slider = require('./components/slider');
let Popup = require('./components/popup');
let Fancy_select = require('./components/fancyselect');
let Jscrollpane = require('./components/jscrollpane');
let Fancybox = require('./components/fancybox');
let Chosen = require('./components/chosen');

require('./components/jquery-ui');

// Are you ready?
$(function() {
    new Lazyload();
    new Forms();
    new Popup();
    new Fancy_select();
    new Jscrollpane();
    new Slider();
    new Fancybox();
    new Chosen();

    // Прокрутка к якорю
    $('.go_to').each(function() {
        var $this = $(this);
        $this.click(function() {
            var scroll_el = $($this.data('destination'));
            if ($(scroll_el).length != 0) {
                $('html, body').animate({
                    scrollTop: $(scroll_el).offset().top
                }, 500);
            }
            return false;
        });
    });

    /*$('.hmb-menu').click(function() {
      $(this).toggleClass('active');
      $('.mob-navigation').slideToggle();
    });*/

    $('.mobile-filters-toggle').click(function() {
        $('form.filters').addClass('open');
    });
    $('form.filters .close-btn').click(function() {
        $(this).parent().removeClass('open');
    });


    var price_range = $('.price-range');
    if (price_range.length) {
        price_range.slider({
            min: price_range.data('min'),
            max: price_range.data('max'),
            values: price_range.data('value').split(';'),
            range: true,
            slide: function(event, ui) {
                for (var i = 0; i < ui.values.length; ++i) {
                    $('input.sliderValue[data-index=' + i + ']').val(ui.values[i]);
                    // $('.clear-filters').addClass('active');
                }
            },
            stop: function( event, ui ) {
                var from = ui.values[0];
                var to = ui.values[1];
                var path_parts = location.pathname.split('/');
                var append = false;
                if(typeof path_parts[3] !== 'undefined'){
                    var filter = path_parts[3];
                    var filter_parts = filter.split('_');
                    for(var i=0; i<filter_parts.length; i++){
                        var filter_data = filter_parts[i].split('-');
                        if(filter_data.length == 3 && filter_data[0] == 'price'){
                            filter_parts[i] = 'price-'+from+'-'+to;
                            append = true;
                        }
                    }
                    if(!append){
                        filter_parts[filter_parts.length] = 'price-'+from+'-'+to;
                    }
                    path_parts[3] = filter_parts.join('_');
                }else{
                    path_parts[3] = 'price-'+from+'-'+to;
                }

                var path = path_parts.join('/');
                location = path;
                //$(this).parents('form').submit();
            }
        });



        // $('input.sliderValue').change(function() {
        //   var $this = $(this);
        //   $('.price-range').slider('values', $this.data('index'), $this.val());
        // });
    }

    $('.sliderValue').keypress(function(e) {
        if(e.which == 13) {
            var parent = $(this).parent();
            var from = parent.find('.sliderValue').eq(0).val();
            var to = parent.find('.sliderValue').eq(1).val();
            var path_parts = location.pathname.split('/');
            var append = false;
            if(typeof path_parts[3] !== 'undefined'){
                var filter = path_parts[3];
                var filter_parts = filter.split('_');
                for(var i=0; i<filter_parts.length; i++){
                    var filter_data = filter_parts[i].split('-');
                    if(filter_data.length == 3 && filter_data[0] == 'price'){
                        filter_parts[i] = 'price-'+from+'-'+to;
                        append = true;
                    }
                }
                if(!append){
                    filter_parts[filter_parts.length] = 'price-'+from+'-'+to;
                }
                path_parts[3] = filter_parts.join('_');
            }else{
                path_parts[3] = 'price-'+from+'-'+to;
            }

            var path = path_parts.join('/');
            location = path;
        }
    });

    $('.category-details').click(function() {
        $(this).next('ul').toggleClass('open');
    })

    $('#click-buy-popup .prod-quont input').change(function(){
        var total = $(this).val() * $(this).data('price');
        $('.result-price').text(total.toFixed(2) + ' грн');
    });
    $('#click-buy-popup .prod-quont input').keyup(function(){
        var total = $(this).val() * $(this).data('price');
        $('.result-price').text(total.toFixed(2) + ' грн');
    });

    if ($(window).width() >= 767) {
        $('.hmb-menu').click(function() {
            setTimeout(function() { $('body').trigger('scroll') }, 10);
            $(this).toggleClass('active');
            $('.navigation').toggle();
        });
    }
    else{
        $('.hmb-menu').click(function() {
            setTimeout(function() { $('body').trigger('scroll') }, 10);
            $(this).toggleClass('active');
            $('.header-nav').toggle();
        });
    }

    $('.search-btn').click(function() {
        $('.header-bot').toggle();
        return false;
    });

    $(document).on('click', '.header-bot', function(e) {
        e.stopPropagation();
    });
    if ($(window).width() < 767) {
        $(document).click(function () {
            $('.header-bot').hide();
        });
    }

    $('.mob-btn').click(function() {
        setTimeout(function() { $('body').trigger('scroll') }, 10);
    });

    setTimeout(function() { $('body').trigger('scroll') }, 10);

    // 18+ btn

    if ($(window).width() >= 767) {
        $('.header-adult').click(function () {
            $(this).toggleClass('active');
            return false;
        });

        $(document).click(function () {
            $('.header-adult').removeClass('active');
        });

        $(document).on('click', '.header-adult', function (e) {
            e.stopPropagation();
        });
    }

    // search
    var search_output = $('[data-output="search-results"]');
    $('[data-autocomplete="input-search"]').on('keyup focus', function(){
        var search = $(this).val();
        search_output.html('').hide();

        if (search.length > 1) {
            var data = {};
            data.search = search;
            var prefix = location.pathname.substr(0, 3) == '/ua' ? '/ua' : (location.pathname.substr(0, 3) == '/en' ? '/en' : '');
            $.get(prefix+'/livesearch', data,
                function(html) {
                    if(html === ''){
                        search_output.html('').hide();
                    }else{
                        search_output.html(html).show();
                    }
                }
            );
        } else {
            search_output.hide();
        }
    });
});

require('./custom.js');
