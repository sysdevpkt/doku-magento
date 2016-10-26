/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert'
    ],
    function (Component, $, url, alert) {
        'use strict';


        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco'
            },

            getMallId: function(){
                return window.checkoutConfig.payment.oco.mall_id
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
                        var obj = $.parseJSON(response);
                        console.log(obj);
                        if(obj.err == false){

                            //data.req_merchant_code = self.getMallId(); //mall id or merchant id
                            //data.req_chain_merchant = obj.chain_merchant; //chain merchant id
                            //data.req_payment_channel = obj.payment_channel; //payment channel
                            //data.req_basket = '';
                            //data.req_transaction_id = 'invoice_1477315453'; //invoice no
                            //data.req_amount = '10000.00';
                            //data.req_currency = '360'; //360 for IDR
                            //data.req_words = '1978b2bbb9a66a70fbe0c39711867b28e8fd19a0'; //your merchant unique key
                            //data.req_session_id = '1477315462015'; //your server timestamp
                            //data.req_form_type = 'inline';
                            //data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                        }else{
                            alert({
                                title: 'Create words error!',
                                content: obj.msg + '<br>Please refresh this page if you want to use payment with Doku Payment Gateway',
                                actions: {
                                    always: function(){}
                                }
                            });
                        }
                    },

                    error: function (xhr, status, error) {
                        alert({
                            title: 'Create words error!',
                            content: 'Please refresh this page if you want to use payment with Doku Payment Gateway',
                            actions: {
                                always: function(){}
                            }
                        });
                    }
                });

                data.req_merchant_code = '2074'; //mall id or merchant id
                data.req_chain_merchant = 'NA'; //chain merchant id
                data.req_payment_channel = '15'; //payment channel
                data.req_basket = '';
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
