var Main = {
    timeout : null,

    init : function () {
        var self = this;
        let page = $('body').attr('data-page') || "";
        self.addEvents();

        if(page == "view") {
            let selected = localStorage.getItem('selected_item');
            
            if(selected != null) {
                selected = JSON.parse(selected);
                self.renderProductView(selected);
            }
        }
        else if(page == "cart") {
            let cart_item = localStorage.getItem('cart');

            if(cart_item != null) {
                cart_item = JSON.parse(cart_item);
                self.renderCart(cart_item[0]);
            }
        }
        else if(page == "checkout") {
            let selected = localStorage.getItem('cart');
            
            if(selected != null) {
                selected = JSON.parse(selected);
                self.renderCheckout(selected[0]);
            }
        }
    },

    addEvents : function () {
        var self = this;

        $('#btn-cart').unbind('click').bind('click', function (e) {
            e.preventDefault();
            let params = window.location.href.split("?");
            location.href = `./cart.html?${params[1] ? params[1].replace('#','') : ''}`;
        });

        $(".header__logo-link,.pCI2Oe,.cart-page-logo").unbind('click').bind('click', function (e) {
            e.preventDefault();
            let params = window.location.href.split("?");
            location.href = `./?${params[1] ? params[1].replace('#','') : ''}`;
        });

        $('#btn-checkout').unbind('click').bind('click', function (e) {
            e.preventDefault();
            let params = window.location.href.split("?");
            location.href = `./checkout.html?${params[1] ? params[1].replace('#','') : ''}`;
        });

        $('.home-product-item').unbind('click').bind('click', function (e) {
            e.preventDefault();

            var index = parseInt($(this).attr('data-index'));
            
            localStorage.setItem('selected_item', JSON.stringify(Data[index]));
            let params = window.location.href.split("?");
            location.href = `./view.html?${params[1] ? params[1].replace('#','') : ''}`;
        });

        $('#btn-add-cart').unbind('click').bind('click', function (e) {
            e.preventDefault();

            let selected = localStorage.getItem('selected_item');
            // localStorage.setItem('selected_item', JSON.stringify(Data[index]));

            if(selected != null) {
                // let cart = (localStorage.getItem('cart')) ? JSON.parse(localStorage.getItem('cart')) : [];
                let cart = [];
                selected = JSON.parse(selected);
                if(cart.some(e=>e.id==selected.id))
                {
                    alert("item already in the cart");
                }
                else
                {
                    cart.push(selected);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    self.renderAddCart(selected);
                }
               
            }
        });

        $('#btn-buy-now').unbind('click').bind('click', function (e) {
            e.preventDefault();
            let selected = localStorage.getItem('selected_item');

            if(selected != null) {
                selected = JSON.parse(selected);
                localStorage.setItem('cart', JSON.stringify([selected]));
                let params = window.location.href.split("?");
                location.href = './checkout.html'+`?${params[1] ? params[1].replace('#','') : ''}`;
            }
        });

        $('#btn-place-order').unbind('click').bind('click', function (e) {
            e.preventDefault();
            
            self.renderLoadingProcess();
        });

        $('.modal-backdrop').unbind('click').bind('click', function (e) {
            e.preventDefault();

            $(this).parent().removeClass('show');
        });

        $('.service-fee-compute').unbind('click').bind('click', function (e) {
            e.preventDefault();
            $('.help-fee-modal').addClass('show');
        });
    },

    renderProductView : function (data) {
        var self = this;
        let images_count = 5;
        $('.product-name').html(data.name);
        $('.product-sold').html(data.sold);
        $('.product-shop').html(data.shop_name);

        if(data.id == 10) {
            $('._12LYCl, .old-price, .percentage-cont').show();
            $('.variant-cont').removeClass('d-none');
            $('.Mzs0kz').hide();
            $('._12LYCl .ZCd4YG video').attr('src', './img/product/'+ data.id +'.mp4');
            $('.pcs-available').html('9326');
            $('.product-price').html('₱196 - ₱249');
            $('.ratings').html('4.8');
            $('.ratings-count').html('2.1K');
            
            self.addVariantEvents();
        }
        else {
            $('._12LYCl, .old-price, .percentage-cont').hide();
            $('.variant-cont').addClass('d-none');
            $('.Mzs0kz').show();
            $('.product-price').html('₱'+ self.utils.withComma(data.price));
            $('.ratings').html('5.0');
            $('.ratings-count').html('38');
            $('.pcs-available').html('3');
            $('.GZ2Hvh').css({
                'background-image' : 'url("./img/product/'+ data.id +'.jpg")',
                'background-size' : 'contain',
                'background-repeat' : 'no-repeat',
            });
        }

        $('.shopee-avatar__img').attr('src', './img/shop/'+ data.id +'.jpg');

        $('.hGIHhp').empty();

        if(data.id == 6) {
            images_count = 3;
        }
        else if(data.id == 9) {
            images_count = 4;
        }

        let videoOverlay = '';

        for(var i = 1; i <= images_count; i++) {
            if(data.id == 10 && i == 1) {
                videoOverlay = '<svg enable-background="new 0 0 15 15" viewBox="0 0 15 15" x="0" y="0" class="shopee-svg-icon zyYqzI"><g opacity=".54"><g><circle cx="7.5" cy="7.5" fill="#040000" r="7.3"></circle><path d="m7.5.5c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7m0-.5c-4.1 0-7.5 3.4-7.5 7.5s3.4 7.5 7.5 7.5 7.5-3.4 7.5-7.5-3.4-7.5-7.5-7.5z" fill="#ffffff"></path></g></g><path d="m6.1 5.1c0-.2.1-.3.3-.2l3.3 2.3c.2.1.2.3 0 .4l-3.3 2.4c-.2.1-.3.1-.3-.2z" fill="#ffffff"></path></svg>';
            }
            else {
                videoOverlay = '';
            }
            
            let elem = '<div class="PZ3-ep">'
                     +       '<div class="y1rHjh">'
                     +           '<div class="Mzs0kz">'
                     +               '<div class="agPpyA _8akja2" style="background-image: url(\'./img/product/'+ data.id +'-'+ i +'.jpg\'); background-size: contain; background-repeat: no-repeat;"></div>'
                     +               videoOverlay
                     +           '</div>'
                     +           '<div class=""></div>'
                     +       '</div>'
                     +   '</div>';
            
            $('.hGIHhp').append(elem);
        }
    },

    addVariantEvents: function () {
        var self = this;
        let scentArray = {
            'Calamansi' : [249, 265, 6],
            'Lemon' : [249, 265, 6],
            'Antibac' : [249, 265, 6],
            'Bubble Gum' : [249, 265, 6],
            'Watermelon' : [249, 265, 6],
            'Unscented' : [196, 210, 7],
            'Tutti-frutti' : [249, 265, 6],
            'Strawberry' : [249, 265, 6],
            'S Guard' : [249, 265, 6],
            'Orange' : [249, 265, 6],
            'Nature\'s Nectar' : [249, 265, 6],
            'Lavender' : [249, 265, 6],
            'Green Apple' : [249, 265, 6],
            'EXTRA SLES ONLY' : [200, 0, 0]
        };

        $('.product-variation').unbind('click').bind('click', function(e) {
            e.preventDefault();

            let ariaLabel = $(this).attr('aria-label'),
                data = scentArray[ariaLabel];

            $('.product-variation').removeClass('product-variation--selected');
            $(this).addClass('product-variation--selected');

            $('.product-price').html('₱'+ self.utils.withComma(data[0]));
            
            Data[4].price = data[0];
            localStorage.setItem('selected_item', JSON.stringify(Data[4]));
            localStorage.setItem('price', data[0]);

            if(data[2] > 0) {
                $('.old-price, .percentage').show();
                $('.old-price').html('₱'+ self.utils.withComma(data[1]));
                $('.percentage').html(data[2]);
            }
            else {
                $('.old-price, .percentage').hide();
            }
        });
    },

    renderAddCart : function (data) {
        var self = this;

        let elem = '<li class="header__cart-item">'
                 +        '<img src="./img/product/'+ data.id +'.jpg" alt="" class="header__cart-img" />'
                 +       '<div class="header__cart-item-info">'
                 +       '<div class="header__cart-item-head">'
                 +           '<h5 class="header__cart-item-name">'+ data.name +'</h5>'
                 +           '<div class="header__cart-item-price-wrap">'
                 +           '<span class="header__cart-item-price">₱'+ self.utils.withComma(data.price) +'</span>'
                 +           '<span class="header__cart-item-multiply">x</span>'
                 +           '<span class="header__cart-item-qnt">1</span>'
                 +           '</div>'
                 +       '</div>'
                 +       '</div>'
                 +   '</li>';
        
        $('.header__cart-list-item').prepend(elem);
        $('.header__cart-notice').html(3);

        if($('.toast').length) {
            $('.toast').show().fadeOut(1250, function () {
                $('.toast').hide();
            });
        }
    },

    renderCart : function (data) {
        var self = this;

        let price = self.utils.withComma(data.price);

        let elem = '<div class="aCSbhb">'
                 +       '<div class="_3ApheT">'
                 +           '<div class="_2zUYwP">'
                 +               '<div class="_3HHh5A">'
                 +                   '<label class="stardust-checkbox stardust-checkbox--checked">'
                 +                       '<input class="stardust-checkbox__input" type="checkbox">'
                 +                       '<div class="stardust-checkbox__box"></div>'
                 +                   '</label>'
                 +               '</div><a class="eQP4g3" href="#"><svg class="zuYTTL" height="16" viewbox="0 0 17 16" width="17">'
                 +               '<path d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z" fill="#333" fill-rule="evenodd" stroke="#333" stroke-width=".3"></path></svg><span style="margin-left: 10px;">'+ data.shop_name +'</span></a><button class="_13iGI_"><svg class="shopee-svg-icon _2KYoW7" viewbox="0 0 16 16">'
                 +               '<g fill-rule="evenodd">'
                 +                   '<path d="M15 4a1 1 0 01.993.883L16 5v9.932a.5.5 0 01-.82.385l-2.061-1.718-8.199.001a1 1 0 01-.98-.8l-.016-.117-.108-1.284 8.058.001a2 2 0 001.976-1.692l.018-.155L14.293 4H15zm-2.48-4a1 1 0 011 1l-.003.077-.646 8.4a1 1 0 01-.997.923l-8.994-.001-2.06 1.718a.5.5 0 01-.233.108l-.087.007a.5.5 0 01-.492-.41L0 11.732V1a1 1 0 011-1h11.52zM3.646 4.246a.5.5 0 000 .708c.305.304.694.526 1.146.682A4.936 4.936 0 006.4 5.9c.464 0 1.02-.062 1.608-.264.452-.156.841-.378 1.146-.682a.5.5 0 10-.708-.708c-.185.186-.445.335-.764.444a4.004 4.004 0 01-2.564 0c-.319-.11-.579-.258-.764-.444a.5.5 0 00-.708 0z"></path>'
                 +               '</g></svg></button>'
                 +               '<div class="_10IR5M"></div>'
                 +           '</div>'
                 +       '</div>'
                 +       '<div class="_2qN5oG">'
                 +           '<div class="_216OLk">'
                 +               '<div class="_1GcTXp">'
                 +                   '<div class="uUhc_B">'
                 +                       '<label class="stardust-checkbox stardust-checkbox--checked"><input class="stardust-checkbox__input" type="checkbox" checked>'
                 +                       '<div class="stardust-checkbox__box"></div>'
                 +                   '</label>'
                 +                   '</div>'
                 +                   '<div class="_2pPbjQ">'
                 +                       '<div class="YxpsCR">'
                 +                           '<a href="#" title="'+ data.name +'">'
                 +                           '<div class="_3SWf-5" style="background-image: url(\'./img/product/'+ data.id +'.jpg\');"></div></a>'
                 +                           '<div class="_3OrWGt">'
                 +                               '<a class="_2fQT1K" href="#" title="'+ data.name +'">'+ data.name +'</a>'
                 +                               '<div class="_931iK8">'
                 +                                   '<span></span>'
                 +                               '</div>'
                 +                           '</div>'
                 +                       '</div>'
                 +                   '</div>'
                 +                   '<div class="_30hIFE"></div>'
                 +                   '<div class="Ra8lP2">'
                 +                       '<div>'
                 +                           '<span class="_1CXksa">₱'+ price +'</span>'
                 +                       '</div>'
                 +                   '</div>'
                 +                   '<div class="_2ZUrV7">'
                 +                       '<div class="_36kVQQ shopee-input-quantity">'
                 +                           '<button class="EcPhjV"><svg class="shopee-svg-icon" viewbox="0 0 10 10" x="0" y="0">'
                 +                           '<polygon points="4.5 4.5 3.5 4.5 0 4.5 0 5.5 3.5 5.5 4.5 5.5 10 5.5 10 4.5"></polygon></svg></button><input aria-valuenow="1" class="EcPhjV _3cj9Np" role="spinbutton" type="text" value="1"><button class="EcPhjV"><svg class="shopee-svg-icon icon-plus-sign" viewbox="0 0 10 10" x="0" y="0">'
                 +                           '<polygon points="10 4.5 5.5 4.5 5.5 0 4.5 0 4.5 4.5 0 4.5 0 5.5 4.5 5.5 4.5 10 5.5 10 5.5 5.5 10 5.5"></polygon></svg></button>'
                 +                       '</div>'
                 +                       '<div class="_1hk8u6">'
                 +                           ''
                 +                       '</div>'
                 +                   '</div>'
                 +                   '<div class="dn3H7Y">'
                 +                       '<span>₱'+ price +'</span>'
                 +                   '</div>'
                 +                   '<div class="_2y8iJi _2qPRqW">'
                 +                       '<button class="delete-item RCd1Gx">Delete</button>'
                 +                   '</div>'
                 +               '</div>'
                 +           '</div>'
                 +       '</div>'
                 +       '<div class="_2oaKY4">'
                 +           '<svg class="shopee-svg-icon _1m76cP icon-free-shipping-line" viewbox="0 0 15 15" x="0" y="0">'
                 +           '<g>'
                 +               '<line fill="none" stroke-linejoin="round" stroke-miterlimit="10" x1="8.6" x2="4.2" y1="9.8" y2="9.8"></line>'
                 +               '<circle cx="3" cy="11.2" fill="none" r="2" stroke-miterlimit="10"></circle>'
                 +               '<circle cx="10" cy="11.2" fill="none" r="2" stroke-miterlimit="10"></circle>'
                 +               '<line fill="none" stroke-miterlimit="10" x1="10.5" x2="14.4" y1="7.3" y2="7.3"></line>'
                 +               '<polyline fill="none" points="1.5 9.8 .5 9.8 .5 1.8 10 1.8 10 9.1" stroke-linejoin="round" stroke-miterlimit="10"></polyline>'
                 +               '<polyline fill="none" points="9.9 3.8 14 3.8 14.5 10.2 11.9 10.2" stroke-linejoin="round" stroke-miterlimit="10"></polyline>'
                 +           '</g></svg>'
                 +           '<div class="_1PmfJ2">'
                 +               '₱50 off shipping with min order of ₱500'
                 +           '</div>'
                 +           '<div class="shopee-drawer" id="pc-drawer-id-4" tabindex="0">'
                 +               '<span class="_1UZAeJ">Learn more</span>'
                 +           '</div>'
                 +       '</div>'
                 +   '</div>';

        $('._1nrPtQ').after(elem);
        $('.total-cart').html(3);
        $('.total-select').html(1);
        $('.total-price').html('₱'+ price);
    },

    renderCheckout : function (data) {
        var self = this;
        let images_count = 5;

        $('.product-name').html(data.name);
        $('.product-price').html('₱'+ self.utils.withComma(data.price));
        $('.product-shop').html(data.shop_name);
        $('.total-payout').html('₱'+ self.utils.withComma((data.price + 45)))
        $('.total-payout-discounted').html('₱'+ self.utils.withComma((data.price + 45)))

        $('.GCGEKm').attr('src', './img/product/'+ data.id +'.jpg');
    }, 

    renderLoadingProcess : function () {
        var self = this;

        let selected = localStorage.getItem('cart');
            
        if(selected != null) {
            
            $('.loader-modal .modal-body svg').remove();
            $('.loader-modal .modal-body').prepend('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');

            $('.loader-modal .modal-body h1').html('Processing');
            $('.modal.loader-modal').addClass('show');
            selected = JSON.parse(selected);

            self.toDataUrl(`./img/product/${selected[0].id}.jpg`, function(base64){
                selected[0].image = base64;
                $('.loader-modal .modal-body h1').fadeOut(1000, function (){
                    $('.loader-modal .modal-body h1').html('Calculating Taxes').fadeIn(1000, function () {
                        setTimeout(function () {
                            self.transmitData(selected);
                        },1000);
                    });
                });
            });
        }
        else{
            alert("please refresh the data, need to add to cart first")
        }

       
    },

    toDataUrl: function(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var reader = new FileReader();
            reader.onloadend = function() {
                callback(reader.result);
            }
            reader.readAsDataURL(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    },
    getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    },

    transmitData : async function(data){
        var self = this;
        data = data[0];
        let trans_id = ("GTX"+Math.random().toString(36).substring(2,15)).toUpperCase();
        let shipping_fee = 45;
        let voucher = 0;
        let subtotal_amount = data.price;
        let total_amount = data.price + shipping_fee - voucher;
        let customer = {
            email:"",
            first_name:"",
            middle_name:"",
            last_name:""
        };
        let email = (self.getUrlParameter("email")) ? self.getUrlParameter("email") : "jondeerigor@gmail.com";
        if (email){
            customer.email = email;
            customer.first_name = (self.getUrlParameter("first_name")) ? self.getUrlParameter("first_name") : "PATRICK";
            customer.last_name = (self.getUrlParameter("last_name")) ? self.getUrlParameter("last_name") : "GARCIA";
        }
        let seller = {
            "registered_name": "H&L GOODS",
            "registered_address": "54 blk lot 2 gawaran street, talon dos, Las Piñas City, Metro Manila, Philippines",
            "business_name": "H&L GOODS",
            "tin": "029203920132",
            "contact_number": "092392829118",
            "email": "hnlgoods@support.com",
            "vat_type":"V"
        };
        let items = [{
            description:data.name,
            variant:"",
            qty:1,
            unit_price:data.price,
            total_price:data.price,
            image:data.image
        }];
        var param = {
            // trans_id:"KO1292030",
            trans_id:trans_id,
            shipping_fee:shipping_fee,
            voucher:voucher,
            coins:0,
            subtotal_amount:subtotal_amount,
            total_amount:total_amount,
            customer:customer,
            items:items,
            seller:seller,
            status:"PENDING"
        };

        $.ajax({
            url: '/api/transaction',
            type: 'post',
            data: param,
            headers: {
                "dsp-code": 'DSPNWLS',   
                "dsp-token": 'HGX5LRxgkzkBRixt',
                "dsp-secret": 'cJ1RJnoPIsavMiko3N5hQ7ky',
            },
            dataType: 'json',
            success: function (resp) {
                let _data = {
                    transaction_fee:resp.data.transaction_fee,
                    service_fee:resp.data.service_fee,
                    commission_fee:resp.data.commission_fee,
                    tax:resp.data.tax,
                    price:data.price,
                    online_platform_vat: resp.data.online_platform_vat,
                    shipping_vat: resp.data.shipping_vat,
                    item_vat: resp.data.item_vat,
                    withholding_tax:resp.data.withholding_tax,
                    trans_id
                }
                self.successModal(_data);
            },
            error:function(e)
            {
                alert(e.responseJSON.message);
                $('.loader-modal .modal-body .lds-ellipsis').remove();
                $('.modal.loader-modal').removeClass('show');
            }
        });

           
        // this.renderSummary(data);
    },

    successModal: function(data)
    {
        let self = this;
       
            $('.loader-modal .modal-body h1').fadeOut(1000, function () {
                let elem = '<svg id="successAnimation" class="animated" xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 70 70"><path id="successAnimationResult" fill="#D8D8D8" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"/><circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="#979797" stroke-width="2" stroke-linecap="round" fill="transparent"/><polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"/></svg>';

                $('.loader-modal .modal-body .lds-ellipsis').remove()
                $('.loader-modal .modal-body').prepend(elem);
                $('.loader-modal .modal-body h1').html('Process Complete').fadeIn(1000);

                setTimeout(function (){
                    self.renderSummary(data);
                }, 1500);
            });
              
    },

    renderSummary : function (data) {
        var self = this;

        $('.modal').removeClass('show');
        $('.summary-modal').addClass('show is-animated');
        let total_payment = data.price + 45;
        let transaction_fee = parseFloat(data.transaction_fee);
        let service_fee = parseFloat(data.service_fee);
        let commission_fee = parseFloat(data.commission_fee);

        let seller_income = total_payment - transaction_fee - service_fee - commission_fee;
        let seller_vat = seller_income * 0.12;

        let online_platform_vat = parseFloat(data.online_platform_vat);
        let shipping_vat = parseFloat(data.shipping_vat);
        let item_vat = parseFloat(data.item_vat);
        let withholding_tax = parseFloat(data.withholding_tax);

        let vat = parseFloat(data.tax);

        // $('.product-fee').html('₱'+ self.utils.withComma(fee));
        // $('.product-vat').html('₱'+ self.utils.withComma(vat));
        $('.trx-no').html(data.trans_id);
        $('.transaction-fee').html('₱'+ self.utils.withComma(transaction_fee));
        $('.commission-fee').html('₱'+ self.utils.withComma(commission_fee));
        $('.service-fee').html('₱'+ self.utils.withComma(service_fee));
        $('.total-fee').html('₱'+ self.utils.withComma(vat));
        $('.order-income').html('₱'+ self.utils.withComma(total_payment));
        // $('.seller-vat').html('₱'+ self.utils.withComma(seller_vat));
        $('.platform-vat').html('₱'+ self.utils.withComma(online_platform_vat));
        $('.shipping-vat').html('₱'+ self.utils.withComma(shipping_vat));
        $('.item-vat').html('₱'+ self.utils.withComma(item_vat));
        $('.withholding-tax').html('₱'+ self.utils.withComma(withholding_tax));
    },
    
    utils : {
        withComma : function (e) {
            return e.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};

$(function() {
    Main.init();
});