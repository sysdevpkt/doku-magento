/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/checkout-data',
        'ko'
    ],
    function (Component, $, url, alert, checkout, ko) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco',
                setWindow: false,
                basket: ''
            },

            initObservable: function(){
                var self = this;

                if(!this.setWindow){
                    window.getToken = function (response){
                        self.getToken(response);
                    };

                    ko.applyBindings({
                        paymentChannels: window.checkoutConfig.payment.oco.payment_channels
                    });

                    this.setWindow = true;
                }

                return this;
            },

            getMallId: function(){
                return window.checkoutConfig.payment.oco.mall_id
            },

            getPaymentChannels: function(){
                return $.parseJSON(window.checkoutConfig.payment.oco.payment_channels);
            },

            getMailingAddress: function() {
                return window.isCustomerLoggedIn ? window.customerData.email : checkout.getValidatedEmailValue();
            },

            dokuToken: function(){
                DokuToken(getToken);
            },

            getDokuForm: function(){
                var self = this;
                var data = new Object();
                
                $.ajax({
                    type: 'GET',
                    url: url.build('doku/payment/words'),

                    success: function (response) {
                        var obj = $.parseJSON(response);
                        if(obj.err == false){

                            self.basket = obj.basket;

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
                    var self = this;

                    $.ajax({
                        type: 'POST',
                        url: url.build('doku/payment/order'),
                        data: {dataResponse: JSON.stringify(response), dataBasket: self.basket, dataEmail: self.getMailingAddress()},

                        success: function (response) {
                            var obj = $.parseJSON(response);

                            if(obj.err == false){
                                self.placeOrder();
                            }else{
                                alert({
                                    title: 'Payment error!',
                                    content: 'Error code : '+ res_response_code + '<br>Please retry payment',
                                    actions: {
                                        always: function(){}
                                    }
                                });
                            }

                        },
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
