/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/checkout-data'
    ],
    function (Component, $, url, alert, checkout) {
        'use strict';


        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco',
                setWindow: false
            },

            initObservable: function(){
                if(!this.setWindow){
                    window.getToken = this.getToken();
                    this.setWindow = true;
                }
            },

            getMallId: function(){
                return window.checkoutConfig.payment.oco.mall_id
            },

            getMailingAddress: function() {
                return window.isCustomerLoggedIn ? window.customerData.email : checkout.getValidatedEmailValue();
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
                        console.log('success');
                        console.log(obj);
                        if(obj.err == false){

                            data.req_merchant_code = self.getMallId(); //mall id or merchant id
                            data.req_chain_merchant = obj.chain_merchant; //chain merchant id
                            data.req_payment_channel = obj.payment_channel; //payment channel
                            data.req_basket = obj.basket;
                            data.req_transaction_id = obj.invoice_no; //invoice no
                            data.req_amount = obj.amount;
                            data.req_currency = obj.currency; //360 for IDR
                            data.req_words = obj.words; //your merchant unique key
                            data.req_session_id = obj.session_id; //your server timestamp
                            data.req_form_type = obj.form_type;
                            data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                            getForm(data);

                            // window.getToken = function (response){

                            //     if (response != undefined && response != 'undefined') {

                            //         $.ajax({
                            //             type: 'POST',
                            //             url: url.build('doku/payment/order'),
                            //             data: {dataResponse: JSON.stringify(response), dataBasket: obj.basket, dataEmail: self.getMailingAddress()},

                            //             /**
                            //              * Success callback
                            //              * @param {Object} response
                            //              */
                            //             success: function (response) {
                            //                 var obj = $.parseJSON(response);
                            //                 console.log('success');
                            //                 console.log(obj);

                            //                 if(obj.err == false){
                            //                     self.placeOrder();
                            //                 }else{
                            //                     alert({
                            //                         title: 'Payment error!',
                            //                         content: obj.msg + '<br>Please retry payment',
                            //                         actions: {
                            //                             always: function(){}
                            //                         }
                            //                     });
                            //                 }

                            //             },

                            //             /**
                            //              * Error callback
                            //              * @param {*} response
                            //              */
                            //             error: function (xhr, status, error) {
                            //                 alert({
                            //                     title: 'Payment Error!',
                            //                     content: 'Please retry payment',
                            //                     actions: {
                            //                         always: function(){}
                            //                     }
                            //                 });
                            //             }
                            //         });
                            //     }

                            // }

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

                return true;

            },

            getToken: function(response){
                if (response != undefined && response != 'undefined') {

                    $.ajax({
                        type: 'POST',
                        url: url.build('doku/payment/order'),
                        data: {dataResponse: JSON.stringify(response), dataBasket: obj.basket, dataEmail: self.getMailingAddress()},

                        /**
                         * Success callback
                         * @param {Object} response
                         */
                        success: function (response) {
                            var obj = $.parseJSON(response);
                            console.log('success');
                            console.log(obj);

                            if(obj.err == false){
                                self.placeOrder();
                            }else{
                                alert({
                                    title: 'Payment error!',
                                    content: obj.msg + '<br>Please retry payment',
                                    actions: {
                                        always: function(){}
                                    }
                                });
                            }

                        },

                        /**
                         * Error callback
                         * @param {*} response
                         */
                        error: function (xhr, status, error) {
                            alert({
                                title: 'Payment Error!',
                                content: 'Please retry payment',
                                actions: {
                                    always: function(){}
                                }
                            });
                        }
                    });
                }
            }
           
        });
    }
);
