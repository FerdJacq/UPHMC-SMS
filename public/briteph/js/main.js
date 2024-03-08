var dsp = {code:"DSPNWLW", token:"HGX5LRxgkzkBRixt", secret:"cJ1RJnoPIsavMiko3N5hQ7ky"};

let customer = {
    email:"",
    first_name:"",
    middle_name:"",
    last_name:""
};
let email = (getUrlParameter("email")) ? getUrlParameter("email") : "jondeerigor@gmail.com";

if (email){
    customer.email = email;
    customer.first_name = (getUrlParameter("first_name")) ? getUrlParameter("first_name") : "PATRICK";
    customer.last_name = (getUrlParameter("last_name")) ? getUrlParameter("last_name") : "GARCIA";
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

var param = {
    // trans_id:"KO1292030",
    trans_id: (Math.random() + 1).toString(36).substring(2,6),
    shipping_fee:95,
    voucher:0,
    coins:0,
    subtotal_amount:250,
    total_amount:345,
    customer:customer,
    seller:seller,
    items:[{
        description:"Dishwashing Liquid DIY Kit",
        variant:"Calamansi Scent",
        qty:1,
        unit_price:250,
        total_price:250,
        image:"./img/1.jpg"
    }],
    status:"PENDING"
};

function getUrlParameter(sParam) {
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
}

function process(){
    // $(".summary-modal").addClass("is-animated show");
    $('.contact-info-modal').removeClass('show');
    $('.loader-modal .modal-body svg').remove();
    $('.loader-modal .modal-body').prepend('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');

    $('.loader-modal .modal-body h1').html('Processing');
    $('.modal.loader-modal').addClass('show');
    param.trans_id = ("GTX"+Math.random().toString(36).substring(2,15)).toUpperCase();

    toDataUrl(param.items[0].image, function(base64){
        param.items[0].image = base64;
        $('.loader-modal .modal-body h1').fadeOut(1000, function (){
            $('.loader-modal .modal-body h1').html('Calculating Taxes').fadeIn(1000, function () {
                setTimeout(function () {
                    transmitData(param);
                },1000);
            });
        });
    });
}

function transmitData(param){
    $.ajax({
        url: '/api/transaction',
        type: 'post',
        data: param,
        headers: {
            "dsp-code": dsp.code,   
            "dsp-token": dsp.token,
            "dsp-secret": dsp.secret,
        },
        dataType: 'json',
        success: function (resp) {
            let _data = {
                transaction_fee:resp.data.transaction_fee,
                service_fee:resp.data.service_fee,
                commission_fee:resp.data.commission_fee,
                tax:resp.data.tax,
                price:param.total_amount,
                online_platform_vat: resp.data.online_platform_vat,
                shipping_vat: resp.data.shipping_vat,
                item_vat: resp.data.item_vat,
                withholding_tax:resp.data.withholding_tax
            }
            successModal(_data);
        },
        error:function(e)
        {
            alert(e.responseJSON.message);
            $('.loader-modal .modal-body .lds-ellipsis').remove();
            $('.modal.loader-modal').removeClass('show');
        }
    });
}

function successModal(data)
{
        $('.loader-modal .modal-body h1').fadeOut(1000, function () {
            let elem = '<svg id="successAnimation" class="animated" xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 70 70"><path id="successAnimationResult" fill="#D8D8D8" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"/><circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="#979797" stroke-width="2" stroke-linecap="round" fill="transparent"/><polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"/></svg>';

            $('.loader-modal .modal-body .lds-ellipsis').remove()
            $('.loader-modal .modal-body').prepend(elem);
            $('.loader-modal .modal-body h1').html('Process Complete').fadeIn(1000);

            setTimeout(function (){
                renderSummary(data);
            }, 1500);
        });        
}

function renderSummary(data) {
    var self = this;

    $('.modal').removeClass('show');
    $('.summary-modal').addClass('show is-animated');
    let total_payment = data.price;
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

    // $('.product-fee').html('₱'+ withComma(fee));
    // $('.product-vat').html('₱'+ withComma(vat));
    $(".trx-no").html(param.trans_id);
    $('.transaction-fee').html('₱'+ withComma(transaction_fee));
    $('.commission-fee').html('₱'+ withComma(commission_fee));
    $('.service-fee').html('₱'+ withComma(service_fee));
    $('.total-fee').html('₱'+ withComma(vat));
    $('.order-income').html('₱'+ withComma(total_payment));
    // $('.seller-vat').html('₱'+ withComma(seller_vat));
    $('.platform-vat').html('₱'+ withComma(online_platform_vat));
    $('.shipping-vat').html('₱'+ withComma(shipping_vat));
    $('.item-vat').html('₱'+ withComma(item_vat));
    $('.withholding-tax').html('₱'+ withComma(withholding_tax));
}

function withComma(e){
    return e.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function toDataUrl(url, callback) {
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
}


$("#proceed-order").click(function(){
    param.customer.first_name = ($("#first_name").val()) ? $("#first_name").val() : "PATRICK";
    param.customer.last_name = ($("#last_name").val()) ? $("#last_name").val() : "GARCIA";
    param.customer.email = ($("#email").val()) ? $("#email").val() : "jondeerigor@gmail.com";
    process();
});

$('#proceed-btn').unbind('click').bind('click', function (e) {
    e.preventDefault();

    $('.contact-info-modal input').val('');
    $("#first_name").val(param.customer.first_name);
    // $("#middle_name").val(param.customer.middle_name);
    $("#last_name").val(param.customer.last_name);
    $("#email").val(param.customer.email);
    $('.contact-info-modal').addClass('show');
});

$( document ).ready(function() {
    
});
// process();