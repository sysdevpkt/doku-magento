/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url'
    ],
    function (Component, $, url) {
        'use strict';


        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco',
                setToken: false,
            },

            getMallId: function(){
                return window.checkoutConfig.payment.oco.mall_id
            },

            getSharedKey: function(){
                return window.checkoutConfig.payment.oco.shared_key
            },

            getCurrency: function(){
                return window.checkoutConfig.payment.oco.currency
            },

            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            dokuToken: function(){
                DokuToken(getToken);
            },

            dokuSha1: function(str){
                var rotate_left = function (n, s) {
                    var t4 = (n << s) | (n >>> (32 - s));
                    return t4;
                };
                var cvt_hex = function (val) {
                var str = '';
                var i;
                var v;
                for (i = 7; i >= 0; i--) {
                v = (val >>> (i * 4)) & 0x0f;
                str += v.toString(16);
                }
                return str;
                };
                var blockstart;
                var i, j;
                var W = new Array(80);
                var H0 = 0x67452301;
                var H1 = 0xEFCDAB89;
                var H2 = 0x98BADCFE;
                var H3 = 0x10325476;
                var H4 = 0xC3D2E1F0;
                var A, B, C, D, E;
                var temp;
                // utf8_encode
                str = unescape(encodeURIComponent(str));
                var str_len = str.length;
                var word_array = [];
                for (i = 0; i < str_len - 3; i += 4) {
                j = str.charCodeAt(i) << 24 | str.charCodeAt(i + 1) << 16 | str.charCodeAt(i + 2) << 8 | str.charCodeAt(i + 3);
                word_array.push(j);
                }
                switch (str_len % 4) {
                case 0:
                i = 0x080000000;
                break;
                case 1:
                i = str.charCodeAt(str_len - 1) << 24 | 0x0800000;
                break;
                case 2:
                i = str.charCodeAt(str_len - 2) << 24 | str.charCodeAt(str_len - 1) << 16 | 0x08000;
                break;
                case 3:
                i = str.charCodeAt(str_len - 3) << 24 | str.charCodeAt(str_len - 2) << 16 | str.charCodeAt(str_len - 1) <<
                8 | 0x80;
                break;
                }
                word_array.push(i);
                while ((word_array.length % 16) != 14) {
                word_array.push(0);
                }
                word_array.push(str_len >>> 29);
                word_array.push((str_len << 3) & 0x0ffffffff);
                for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
                for (i = 0; i < 16; i++) {
                W[i] = word_array[blockstart + i];
                }
                for (i = 16; i <= 79; i++) {
                W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
                }
                A = H0;
                B = H1;
                C = H2;
                D = H3;
                E = H4;
                for (i = 0; i <= 19; i++) {
                temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
                E = D;
                D = C;
                C = rotate_left(B, 30);
                B = A;
                A = temp;
                }
                for (i = 20; i <= 39; i++) {
                temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
                E = D;
                D = C;
                C = rotate_left(B, 30);
                B = A;
                A = temp;
                }
                for (i = 40; i <= 59; i++) {
                temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
                E = D;
                D = C;
                C = rotate_left(B, 30);
                B = A;
                A = temp;
                }
                for (i = 60; i <= 79; i++) {
                temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
                E = D;
                D = C;
                C = rotate_left(B, 30);
                B = A;
                A = temp;
                }
                H0 = (H0 + A) & 0x0ffffffff;
                H1 = (H1 + B) & 0x0ffffffff;
                H2 = (H2 + C) & 0x0ffffffff;
                H3 = (H3 + D) & 0x0ffffffff;
                H4 = (H4 + E) & 0x0ffffffff;
                }
                temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
                return temp.toLowerCase();
            },

            getDokuForm: function(){
                var self = this,
                    placeOrder;
                var data = new Object();

                console.log(this.getMallId());
                console.log(this.getSharedKey());
                console.log(this.getCurrency());

                data.req_merchant_code = '2074'; //mall id or merchant id
                data.req_chain_merchant = 'NA'; //chain merchant id
                data.req_payment_channel = '15'; //payment channel
                data.req_basket = '';
                data.req_server_url = 'http://crm.doku.com/doku-library-staging/example-payment/merchant-example.php'; //merchant payment url to receive pairing code & token
                data.req_transaction_id = 'invoice_1477315453'; //invoice no
                data.req_amount = '10000.00';
                data.req_currency = '360'; //360 for IDR
                data.req_words = '1978b2bbb9a66a70fbe0c39711867b28e8fd19a0'; //your merchant unique key
                data.req_session_id = '1477315462015'; //your server timestamp
                data.req_form_type = 'inline';
                data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                // getForm(data);

                window.getToken = function (response){

                    if (response != undefined && response != 'undefined') {

                        $.ajax({
                            type: 'POST',
                            url: url.build('doku/payment/order'),
                            data: {dataResponse: JSON.stringify(response)},

                            /**
                             * Success callback
                             * @param {Object} response
                             */
                            success: function (response) {
                                var obj = $.parseJSON(response);

                                if(obj.err == false){
                                    self.placeOrder();
                                }else{
                                    console.log('process failed');
                                    console.log(response);
                                }

                            },

                            /**
                             * Error callback
                             * @param {*} response
                             */
                            error: function (response) {
                                console.log('error');
                            }
                        });
                    }

                }

                return true;

            }
           
        });
    }
);
