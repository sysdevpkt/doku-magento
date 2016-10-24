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

        function getToken(response){

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
                        console.log('success');
                        console.log(response);

                        var obj = $.parseJSON(response);

                        if(obj.err == false){

                            console.log('process success');
                            Component.placeOrder();

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
                        console.log(response);
                    }
                });
            }
        }

        window.getToken = function (response) {
            getToken(response);
        };

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco',
                setToken: false,
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            dokuToken: function(){
                DokuToken(getToken());
            },

            getDokuForm: function(){
                var data = new Object();

                data.req_merchant_code = '2074'; //mall id or merchant id
                data.req_chain_merchant = 'NA'; //chain merchant id
                data.req_payment_channel = '15'; //payment channel
                data.req_basket = '';
                data.req_server_url = 'http://crm.doku.com/doku-library-staging/example-payment/merchant-example.php'; //merchant payment url to receive pairing code & token
                data.req_transaction_id = 'invoice_1477309296'; //invoice no
                data.req_amount = '10000.00';
                data.req_currency = '360'; //360 for IDR
                data.req_words = '49f67af8cfc38af03e5a088e344e1f3a61aef70a'; //your merchant unique key
                data.req_session_id = '1477309083779'; //your server timestamp
                data.req_form_type = 'inline';
                data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                getForm(data);

                return true;

            }
           
        });
    }
);
