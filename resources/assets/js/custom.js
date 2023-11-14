'use strict';
// Depends
var $ = require('jquery');
var swal = require('sweetalert2');
require('../../../node_modules/jquery.maskedinput/src/jquery.maskedinput');
require('../../../node_modules/sumoselect/jquery.sumoselect.min');

// Are you ready?
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var prefix = location.pathname.substr(0, 3) == '/ua' ? '/ua' : (location.pathname.substr(0, 3) == '/en' ? '/en' : '');

    $('.prod-quont input, .cart__item-quont input').on('keydown', function (e) {
        return !(/^[А-Яа-яA-Za-z \-]$/.test(e.key));
    });

    $('.root_cat span').click(function(e){
        e.preventDefault();
        e.stopPropagation();
        $(this).parent().toggleClass('active');
    });

  // Выбор вариации
  $('.variation-radio, .result-price .count_field').change(function(){
      var attrs = [];
      $('.variation-radio:checked').each(function(){
          var val = $(this).val();
          if(val != ''){
              attrs.push(val);
          }
      });
      var hash = attrs.sort().join('_');

      $('[name="variation"]').prop('checked', false);
      var input = $('#var_'+hash);
      if(hash != '' && input.length){
          input.prop('checked', true);
          location.hash = hash;
          var price = parseFloat(input.data('price'));
          $('.product-price').html(price);
      }else{
          if($('.variation-radio').length == 0){
              var price = parseFloat($('.product-price').data('price'));
              $('.product-price').html(price);
          }else{
              history.pushState("", document.title, window.location.pathname + window.location.search);
              $('.product-price').html($('.product-price').data('price'));
          }
      }
      hideVariationOptions();
  });

  function flushNextSelects(select){
      var selects = $('.variation-select');
      for(var i=selects.index(select)+1; i<selects.length; i++){
          selects.eq(i).find('option:first-child').prop('selected', true);
      }
  }

  function clearVariations(variations, attrs, attr){
      var new_variations = [];
      for(var v=0; v<variations.length; v++){
          var isset = true;
          if(variations[v].indexOf(attr) < 0){
              isset = false;
          }
          for(var a=0; a<attrs.length; a++){
              if(variations[v].indexOf(attrs[a]) < 0 ){
                  isset = false;
              }
          }
          if(isset){
              new_variations.push(variations[v]);
          }
      }
      return new_variations;
  }

  function hideVariationOptions(){
      var variations = [];
      var current_select;
      $('[name="variation"]').each(function(){
          variations.push($(this).attr('id').replace('var_', ''));
      });
      var selects = $('.variation-select');
      var attrs = [];
      if(selects.length > 1){
          if(selects.eq(0).val() !== '')
            attrs.push(selects.eq(0).val());

          for(var i=1; i<selects.length; i++){
              current_select = selects.eq(i);
              current_select.find('option').each(function(){
                var opt_var = clearVariations(variations, attrs, $(this).val());
                if(opt_var.length == 0){
                    $(this).attr('disabled', 'disabled').css('display', 'none');
                    if(current_select.val() == $(this).val()){
                        current_select.find('option:first-child').prop('selected', true);
                    }
                }else{
                    $(this).prop('disabled', false).css('display', 'block');
                }
            });
            if(selects.eq(i).val() !== ''){
                attrs.push(selects.eq(i).val());
            }else{
                // i++;
                // while(i<selects.length){
                //     console.log(selects.eq(i));
                //     selects.eq(i).find('option').prop('disabled', false).css('display', 'block');
                //     i++;
                // }
                // break;
            }
          }
      }
  }

    var hash_parts = location.hash.replace('#', '').split('_');
    if(hash_parts.length){
        for(var i=0; i<hash_parts.length; i++){
            var option = $('.prod-size-item input[value="'+hash_parts[i]+'"]');
            option.prop('checked', true);
            option.trigger('change');
        }
    }

    $('.btn_buy').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this);
        var qty = $('.result-price .count_field').val();
        var data = {
            action: 'add',
            product_id: $this.data('prod-id'),
            quantity: qty > 1 ? qty : 1
        };

        $.post(prefix+"/cart/update", data, function(cart){
            update_cart_quantity(cart);
        });
    });

    /*
     * Добавление отзывов комментариев
     */
    $('form.review-form, form.answer-form').on('submit', function(e){
        e.preventDefault();
        var $this = $(this);

        $.ajax({
            url: prefix+'/review/add',
            data: $(this).serialize(),
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                $this.find('.error-message').fadeOut(300);
                $this.find('button[type="submit"]').html('Отправляем...');
            },
            success: function (response) {
                if(response.error){
                    var html = '';
                    $.each(response.error, function(i, value){
                        html += value + '<br>';
                    });
                    // $('#error-' + response.type + ' > div').html(html);
                    // $('#error-' + response.type).fadeIn(300);

                    swal('Ошибка', html, 'error');

                } else if(response.success) {
                    // $('#error-' + response.type + ' > div').html(response.success);
                    // $('#error-' + response.type).fadeIn(300);

                    swal('Ваш отзыв успешно добавлен!', 'Он появится на сайте после модерации.', 'success');

                    setTimeout(function(){
                        $this.slideUp('slow');
                        $('.review-btn').fadeIn('slow');
                    },2500);
                    $('form.' + response.type + '-form')[0].reset();
                }
                $this.find('button[type="submit"]').html('Оставить отзыв')
            }
        });
    });

    window.sortBy = function(sort){
        var locate = location.search.split('&');
        var new_location = '';

        jQuery.each(locate, function (i, value) {
            var parameters = value.split('=');
            if (parameters[0] != 'sort') {
                new_location += value + '&';
            }
        });

        location.search = new_location + 'sort=' + sort;
    };

    /**
     * Отображение полей в зависимости от выбранного способа доставки
     */
    $('.order-page__form').on('change', '#checkout-step__delivery', function(){
        if ($(this).val() != 0) {
            $('.checkout-step__body').addClass('checkout-step__body_loader');
            $('.checkout-step__body_second .error-message').fadeOut(300);
            $('.checkout-step__body_second .error-message__text').html('');
            var data = {
                delivery: $(this).val(),
                order_id: $('#current_order_id').val()
            };

            $("#checkout-delivery-payment").load(prefix+"/checkout/delivery", data, function (cart) {
                //$('select').fancySelect();
            });
            $('.checkout-step__body').removeClass('checkout-step__body_loader');
        }
    });

    /**
     * Удаление товара из корзины
     */
    $('#order_checkout_content').on('click', '.cart__item-delete', function(){
        var $this = $(this);
        update_cart({
            action: 'remove',
            product_id: $this.data('prod-id')
        });
        $(this).parent('.cart__item').slideUp('slow').promise().done(function() {
            $(this).remove();
        });
    });

    /**
     * Обновление колличества товара в корзине
     */
    $('#order_checkout_content').on('input change', '.cart__item-quont input', function(){
        var $this = $(this);
        update_cart({
            action: 'update',
            product_id: $this.data('prod-id'),
            quantity: $this.val()
        });
    });

    /**
     * Кнопка уменьшения колличества товара в корзине
     */
    $('#order-popup, #order_cart_content, #order_checkout_content').on('click', '.cart_minus', function () {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });

    /**
     * Кнопка увеличения колличества товара в корзине
     */
    $('#order-popup, #order_cart_content, #order_checkout_content').on('click', '.cart_plus', function () {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });

    /**
     * Обработка оформления заказа
     */
    $('#order-checkout').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var error_div = form.find('.error-message');

        $.ajax({
            url: prefix+'/order/create',
            type: 'post',
            data: $(this).serialize(),
            beforeSend: function(){
                $('.checkout-step__body').addClass('checkout-step__body_loader');
                $('.checkout-step__body_second .error-message').fadeOut(300, function(){
                    $('.checkout-step__body_second .error-message__text').html('');
                });
                $('select, input').removeClass('input-error');
            },
            success: function(response) {

                if (response.error) {
                    var html = '';
                    $.each(response.error, function (id, text){
                        var error = id.split('.');
                        $('[name="' + error[0] + '[' + error[1] + ']"').addClass('input-error');
                        html += text + '<br>';
                    });
                    $('.cart-block_checkout .error-message__text').html(html);
                    $('.cart-block_checkout').removeClass('checkout-step__body_loader');
                    $('.cart-block_checkout .error-message').fa
                    deIn(300);
                } else if (response.success) {
                    console.log(response);
                    if (response.success == 'liqpay') {
                        // $('body').prepend(
                        //     '<form method="POST" id="liqpay-form" action="' + response.liqpay.url + '" accept-charset="utf-8">' +
                        //     '<input type="hidden" name="data" value="' + response.liqpay.data + '" />' +
                        //     '<input type="hidden" name="signature" value="' + response.liqpay.signature + '" />' +
                        //     '</form>');
                        // $('#liqpay-form').submit();
                        LiqPayCheckout.init({
                            data: response.liqpay.data,
                            signature:  response.liqpay.signature,
                            embedTo: "#liqpay_checkout",
                            mode: "embed" // embed || popup
                        }).on("liqpay.callback", function(data){
                            console.log(data.status);
                            console.log(data);
                            window.location = prefix+'/thank_you?order_id=' + response.order_id;
                        }).on("liqpay.ready", function(data){
                            $('#liqpay_checkout').css('display', 'block');
                        }).on("liqpay.close", function(data){
                            window.location = prefix+'/thank_you?order_id=' + response.order_id;
                        });
                    } else if (response.success == 'redirect') {
                        window.location = prefix+'/thank_you?order_id=' + response.order_id;
                    }
                }
            }
        })
    });

    $('.subscribe-form').on('submit', function(e){
        e.preventDefault();

        $.ajax({
            url: prefix+'/subscribe',
            data: $(this).serialize(),
            method: 'post',
            dataType: 'json',
            success: function(response){
                if (response.email){
                    swal('Подписка', response.email[0], 'error');
                } else if (response.success) {
                    swal('Подписка', response.success, 'success');
                }

                $('.subscribe-form').find('input[type="email"]').val('');
            }
        });
    });

    jQuery('.wishlist-add, .prod-card-wish').on('click', function () {
        var $this = $(this);
        var data = {};
        data['user_id'] = $this.attr('data-user-id');
        data['product_id'] = $this.attr('data-prod-id');
        if ($this.hasClass('active')) {
            data['action'] = 'remove';
        } else {
            data['action'] = 'add';
        }
        $.ajax({
            url: prefix+'/wishlist/update', type: 'POST', data: data, dataType: 'JSON',
            success: function (response) {
                if (response.count !== false) {
                    if($this.parents('.grid-product-card').length){
                        $this.parents('.grid-product-card').find('.prod-card-wish').toggleClass('active');
                    }else{
                        $this.toggleClass('active');
                    }
                }
            }
        });
    });

    $('.show-more-filters').click(function(e){
        e.preventDefault();
        $(this).parent().find('.overflow-scroll').css('height', 'auto');
        $(this).hide();
    });

    $('#filters input, #filters-min input').change(function(){
        var url = $(this).data('url')
        if(typeof url !== 'undefined' && location.pathname != url){
            location = url;
        }
    });

    $('.hover-prod-card').on('mouseenter, mousemove', function (e) {
        var slider = $(this).find('.slick-slider:not(.slick-initialized)');

        if (slider.length) {
            slider.slick();
        }
        $(this).find('.slick-slider').slick('setPosition');
    });

    $('.homepage-product-card-color a').click(function(e){
        e.preventDefault();
        var i = $(this).data('id');
        var slider = $(this).parents('.grid-product-card').find('.slick-slider');
        slider.slick('slickGoTo', i);
    });

    $('#checkout-btn').click(function (e) {
        e.preventDefault();
        var validate = true;
        if($('#safe-agreement').prop('checked') == false){
            $('#safe-agreement').addClass('not-valid');
            validate = false;
        }else{
            $('#safe-agreement').removeClass('not-valid');
        }
        if($('#public-agreement').prop('checked') == false){
            $('#public-agreement').addClass('not-valid');
            validate = false;
        }else{
            $('#public-agreement').removeClass('not-valid');
        }

        if(validate){
            //$(this).parents('form').submit();

            $.ajax({
                url: prefix+'/order/create',
                type: 'post',
                data: $(this).parents('form').serialize(),
                beforeSend: function(){
                    $('.checkout-step__body').addClass('checkout-step__body_loader');
                    $('.checkout-step__body_second .error-message').fadeOut(300, function(){
                        $('.checkout-step__body_second .error-message__text').html('');
                    });
                    $('select, input').removeClass('input-error');
                },
                success: function(response) {
                    if (response.error) {
                        var html = '';
                        $.each(response.error, function (id, text){
                            var error = id.split('.');
                            $('[name="' + error[0] + '[' + error[1] + ']"').addClass('input-error');
                            html += text + '<br>';
                        });
                    } else if (response.success) {
                        if (response.success == 'liqpay') {
                            LiqPayCheckout.init({
                                data: response.liqpay.data,
                                signature:  response.liqpay.signature,
                                // embedTo: "#liqpay_checkout",
                                mode: "popup" // embed || popup
                            }).on("liqpay.callback", function(data){
                                window.location = prefix+'/thank_you?order_id=' + response.order_id;
                            }).on("liqpay.ready", function(data){
                                $('#liqpay_checkout').css('display', 'block');
                            }).on("liqpay.close", function(data){
                                window.location = prefix+'/thank_you?order_id=' + response.order_id;
                            });
                        } else if (response.success == 'redirect') {
                            swal('Заказ оформлен!', 'Номер заказа: '+response.order_id, 'success');
                            setTimeout(function(){
                                window.location = prefix+'/user/history';
                            }, 5000);
                            //window.location = '/thank_you?order_id=' + response.order_id;
                        }
                    }
                }
            })
        }else{
            return false;
        }
    });

    $('#delivery-popup .save').click(function(){
        $('#current-delivery').text($('[for="'+$('#delivery-popup [name="delivery"]:checked').attr('id')+'"]').text());
        $.magnificPopup.close();
    });

    $('#delivery-popup .cancel').click(function(){
        $.magnificPopup.close();
    });

    // $('#delivery-popup [name="delivery"]').change(function(){
    //     $('#current-delivery').text($('[for="'+$(this).attr('id')+'"]').text());
    // });

    $('#pay-popup .save').click(function(){
        $('#current-pay').text($('[for="'+$('#pay-popup [name="payment"]:checked').attr('id')+'"]').text());
        $.magnificPopup.close();
    });

    $('#pay-popup .cancel').click(function(){
        $.magnificPopup.close();
    });
    // $(document).on('click', '.edit-profile.active', function () {
    //     var data = {
    //         fio: $('[name="fio"]').val(),
    //         phone: $('[name="phone"]').val(),
    //         email: $('[name="email"]').val(),
    //         user_birth: $('[name="user-birth"]').val()
    //     };
    //
    //     $.post('/saveUserData', data, function(response){
    //         window.location = window.location;
    //     });
    // })

    $('[name="subscr-type"]').change(function(){
        $.post(prefix+'/user/updateSubscr', {subscr: $('[name="subscr-type"]:checked').val()}, function(response){
            if(response.success){
                swal('Сохранено', 'Данные успешно сохранениы!', 'success');
            }else{
                swal('Ошибка', 'Не удалось сохранить данные', 'error');
            }
        });
    });
    $('.profile-address-btn').click(function(e){
        e.preventDefault();
        var data = {
            city: $('[name="city"]').val(),
            post_code: $('[name="post_code"]').val(),
            street: $('[name="street"]').val(),
            house: $('[name="house"]').val(),
            flat: $('[name="flat"]').val()
        };

        $.post(prefix+'/user/updateAddress', data, function(response){
            if(response.success){
                swal('Сохранено', 'Данные успешно сохранениы!', 'success');
            }else{
                swal('Ошибка', 'Не удалось сохранить данные', 'error');
            }
        });
    });

    $('.sign-up-form').submit(function (e) {
        if($('#email').val() == '' || $('#first_name').val() == '' || $('#phone').val() == '' || $('#password').val() == '' || $('#passwordr').val() == ''){
            e.preventDefault();
        }
    });

    $('.sign-up-form input').on('keyup', function(){
        if($('#email').val() != '' && $('#first_name').val() != '' && $('#phone').val() != '' && $('#password').val() != '' && $('#passwordr').val() != ''){
            $('.registr-btn').css('background-color', '#5F98B9');
        }else{
            $('.registr-btn').css('background-color', '#9DACB4');
        }
    });

    $('.sign-in-form').submit(function (e) {
        if($('#email').val() == '' || $('#pass').val() == ''){
            e.preventDefault();
        }
    });

    $('.sign-in-form input').on('keyup', function(){
        if($('#email').val() != '' && $('#pass').val() != ''){
            $('.registr-btn').css('background-color', '#5F98B9');
        }else{
            $('.registr-btn').css('background-color', '#9DACB4');
        }
    });

    $('#redirect_select').change(function(){
        if(window.location.href != $(this).val()){
            window.location = $(this).val();
        }
    });

    $('#phone').mask('+38 (999) 999-99-99');

    $('.filter-menu').click(function () {
        $('.aside-filter-menu-container').addClass('active');
    });

    $('#close_filter').click(function () {
        $('.aside-filter-menu-container').removeClass('active');
    });

    // sort select mobile

    $('.sumo-select').SumoSelect({
        forceCustomRendering: true
    });

    // Сортировка
    $('.sorting-select').change(function () {
        var s = window.location.search.replace('?', '').split('&');
        var search = {};
        if(s.length){
            for(i=0; i<s.length; i++){
                var p = s[i].split('=');
                if(p[0] != '')
                    search[p[0]] = p[1];
            }
        }

        search['order'] = $(this).val();
        s = '?';
        for (var key in search) {
            s += key + '=' + search[key];
        }

        if(location.href != location.origin + location.pathname + s){
            window.location = location.origin + location.pathname + s;
        }
    });

    $('.clear_cart').click(function(){
        update_cart({
            action: 'clear'
        });
        $('.cart__item').slideUp('slow').promise().done(function() {
            $(this).remove();
        });
    });

    $('.cart-form__input[name="phone"]').mask('+38 (999) 999-99-99');

    $('.click-buy-popup__form').on('click', function(){
        if(typeof gtag !== 'undefined'){
            gtag("event", "purchase", {
                transaction_id: Math.round(Math.random() * (999999999 - 999999) + 999999),
                value: $this.data('price'),
                items: [
                    {
                        item_id: ""+$('[name="product_id"]').val(),
                        name: $this.data('name'),
                        sku: $this.data('sku'),
                        category: $this.data('category'),
                        price: $this.data('price'),
                        quantity: 1,
                        currency: "UAH"
                    }
                ],
                currency: "UAH",
                send_to: "G-Y9W5S3LTY4"
            });
        }
    });

    $('.question-popup__form, .contact-form').on('click', function(){
        if(typeof gtag !== 'undefined'){
            gtag("event", "Form_Question_submit", {send_to: "G-Y9W5S3LTY4"});
        }
    });
});

/**
 * Обновление корзины
 * @param data
 */
function update_cart(data){
    var prefix = location.pathname.substr(0, 3) == '/ua' ? '/ua' : (location.pathname.substr(0, 3) == '/en' ? '/en' : '');

    $.post(prefix+"/cart/update", data, function(cart){
        var order_cart_content = $('#order_checkout_content');
        if(order_cart_content.length > 0){
            order_cart_content.load(prefix+"/checkout #order_checkout_content");
        }
        update_cart_quantity(cart);
    });
}

function update_cart_quantity(cart) {
    var quantity = cart.total_quantity;
    var price = cart.total_price;
    if(quantity){
        if($('.header__cart .header__cart-guant').length){
            $('.header__cart .header__cart-guant').text(quantity);
        }else{
            $('.header__cart').append('<p class="header__cart-guant">'+quantity+'</p>');
        }
        if($('.header__cart .header__cart-sum').length){
            $('.header__cart .header__cart-sum').text(price + 'грн');
        }else{
            $('.header__cart').append('<p class="header__cart-sum">'+price+'грн</p>');
        }
    }else{
        $('.header__cart .header__cart-guant, .header__cart .header__cart-sum').remove();
    }
}

/**
 * Загрузка городов и отделений Новой Почты
 * @param id
 * @param value
 */
window.newpostUpdate = function(id, value) {
    var prefix = location.pathname.substr(0, 3) == '/ua' ? '/ua' : (location.pathname.substr(0, 3) == '/en' ? '/en' : '');

    if (id == 'city') {
        var data = {
            city_id: value
        };
        var path = prefix+'/checkout/warehouses';
        var selector = jQuery('#checkout-step__warehouse');
    } else if (id == 'region') {
        var data = {
            region_id: value
        };
        var path = prefix+'checkout/cities';
        var selector = jQuery('#checkout-step__city');
    }

    jQuery.ajax({
        url: path,
        data: data,
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            jQuery('.checkout-step__body_second .error-message').fadeOut(300);
            jQuery('.checkout-step__body').addClass('checkout-step__body_loader');
            jQuery('.checkout-step__body_second .error-message__text').html('');
            jQuery('#checkout-step__warehouse').html('<option value="0">Сначала выберите город!</option>');
            jQuery('#checkout-step__warehouse').trigger('refresh');
        },
        success: function(response){
            if (response.error) {
                jQuery('.checkout-step__body_second .error-message__text').html(response.error);
                jQuery('.checkout-step__body').removeClass('checkout-step__body_loader');
                jQuery('.checkout-step__body_second .error-message').fadeIn(300);
            } else if (response.success) {
                var html = '<option value="0">Выберите город</option>';
                jQuery.each(response.success, function(i, resp){
                    if (id == 'city') {
                        var info = resp.address_ru;
                    } else if (id == 'region') {
                        var info = resp.name_ru;
                    }
                    html += '<option value="' + resp.id + '">' + info + '</option>';
                });
                selector.html(html);
                selector.trigger('update.fs');
                jQuery('.checkout-step__body').removeClass('checkout-step__body_loader');
            }
        }
    })
};