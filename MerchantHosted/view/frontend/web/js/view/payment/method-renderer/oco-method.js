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

            getDokuForm: function(){
                var self = this,
                    placeOrder;
                var data = new Object();

                $.ajax({
                    type: 'GET',
                    url: url.build('doku/payment/words'),

                    /**
                     * Success callback
                     * @param {Object} response
                     */
                    success: function (response) {
                        console.log('success');
                        console.log(response);

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
