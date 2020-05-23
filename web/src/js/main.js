$(document).ready(function () {
    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            scrollDistance: 300, // Distance from top/bottom before showing element (px)
            scrollFrom: 'top', // 'top' or 'bottom'
            scrollSpeed: 300, // Speed back to top (ms)
            easingType: 'linear', // Scroll to top easing (see http://easings.net/)
            animation: 'fade', // Fade, slide, none
            animationSpeed: 200, // Animation in speed (ms)
            scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
            //scrollTarget: false, // Set a custom target element for scrolling to the top
            scrollText: '<i class="fa fa-angle-up"></i>', // Text for element, can contain HTML
            scrollTitle: false, // Set a custom <a> title if required.
            scrollImg: false, // Set true to use image
            activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            zIndex: 2147483647 // Z-Index for the overlay
        });
    });

    $(document).on('click', '.add-to-cart', function () {
        let productId =  $(this).data('id'),
            _csrf = $('meta[name=_csrf]').attr("content"),
            self = this;
        let data = {
            product:productId,
            _csrf: _csrf
        };
        $.ajax({
            url: '/cart/add',
            data: data,
            dataType: 'json',
            type: 'POST',
            success: function (res) {
               if (res.error === false){
                   $(self).text('in cart').prop('href','/cart').removeClass('add-to-cart');
               }
            },
            error: function(res){}
        });
        return false;
    });

    $(document).on('click', '.js-delete-product', function () {
        let productId =  $(this).data('id'),
            _csrf = $('meta[name=_csrf]').attr("content"),
            self = this;
        let data = {
            product:productId,
            _csrf: _csrf
        };
        $.ajax({
            url: '/cart/delete',
            data: data,
            dataType: 'json',
            type: 'POST',
            success: function (res) {
                if (res.error === false){
                    $(self).closest('tr.cart_product_' + productId).remove();
                    let countProduct = $('.cart_product').length;
                    if (countProduct === 0){
                       $('.cart_info').remove();
                       $('.cart-empty').show();
                    }
                }
            },
        });
        return false;
    });

    $(document).on('click', '.js-check-out', function () {
        let _csrf = $('meta[name=_csrf]').attr("content"),
            cartProducts = $('.checkout-product'),
            data = {
                products: [],
                _csrf: _csrf
            };

        if (cartProducts.length === 0){
            return false;
        }

        cartProducts.each(function(idx, element){
          let id = $(element).data('product');
            data.products.push(id);
        });

        $.ajax({
            url: '/order/create',
            data: data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function(){
              $('.checkout-button').hide();
            },
            success: function (res) {
                if (res.error === false){
                    document.location.replace('/cart/checkout?order=' + res.orderId);
                } else {
                    if (confirm(res.message)) {
                        document.location.reload();
                    }
                    $('.checkout-button').show();
                }
            },
        });
        return false;
    });

    $(document).on('click', '.js-payment', function () {
        let _csrf = $('meta[name=_csrf]').attr("content"),
            totalPrice = $(this).data('payment'),
            orderId = $(this).data('order'),
            data = {
                totalPrice: totalPrice,
                orderId: orderId,
                _csrf: _csrf
            };

        $.ajax({
            url: '/order/payment',
            data: data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function(){
                $('.payment-button').hide();
            },
            success: function (res) {
                if (res.error === false){
                    $('.payment-button').remove();
                    $('.payment-status').text('Order paid');
                } else {
                    $('.payment-button').show();
                }
            },
        });
        return false;
    });


});
