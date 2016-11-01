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
        'mage/loader'
    ],
    function (Component, $, url, alert, checkout, loader) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco',
                setWindow: false,
                dokuObj: new Object(),
            },

            initObservable: function(){
                var self = this;

                if(!this.setWindow){
                    window.getToken = function (response){
                        self.getToken(response);
                    };
                    this.setWindow = true;
                }

                return this;
            },

            getMallId: function(){
                return window.checkoutConfig.payment.oco.mall_id
            },

            getPaymentTitle: function(){
                return window.checkoutConfig.payment.oco.payment_title
            },

            getPaymentChannels: function(){
                return $.parseJSON(window.checkoutConfig.payment.oco.payment_channels);
            },

            doPaymentChannel: function(data, event){
                loader.show;
                $("fieldset[id^='form-']").hide();
                $("[doku-div='form-payment'] :input").remove();
                this.dokuObj.req_payment_channel = event.target.value;

                if(event.target.value != '') {
                    this.dokuObj.req_email = (window.isCustomerLoggedIn ? window.customerData.email : checkout.getValidatedEmailValue());

                    $("#form-" + event.target.value).show();

                    if(event.target.value == '04'){
                        this.dokuObj.req_custom_form = ['username-field', 'password-field'];
                        this.dokuObj.req_url_payment = 'orderwallet';
                    }else if(event.target.value == '15'){
                        this.dokuObj.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];
                        this.dokuObj.req_url_payment = 'ordercc';
                    }

                    this.getDokuForm();
                }
                loader.hide;
            },

            dokuToken: function(){
                if(this.dokuObj.req_payment_channel != '') {
                    DokuToken(getToken);
                }else{
                    alert({
                        title: 'Payment Channel',
                        content: 'Please choose payment channel',
                        actions: {
                            always: function(){}
                        }
                    });
                }
            },

            getDokuForm: function(){
                var self = this;

                $.ajax({
                    type: 'GET',
                    url: url.build('doku/payment/words'),
                    showLoader: true,

                    success: function (response) {
                        var obj = $.parseJSON(response);
                        if(obj.err == false){

                            self.dokuObj = $.extend(self.dokuObj, obj);

                            var data = new Object();
                            data.req_merchant_code = self.getMallId(); //mall id or merchant id
                            data.req_chain_merchant = obj.req_chain_merchant; //chain merchant id
                            data.req_payment_channel = self.dokuObj.req_payment_channel; //payment channel
                            data.req_basket = obj.req_basket;
                            data.req_transaction_id = obj.req_invoice_no; //invoice no
                            data.req_amount = obj.req_amount;
                            data.req_currency = obj.req_currency; //360 for IDR
                            data.req_words = obj.req_words; //your merchant unique key
                            data.req_session_id = obj.req_session_id; //your server timestamp
                            data.req_form_type = obj.req_form_type;
                            data.req_custom_form = self.dokuObj.req_custom_form;
                            data.req_mage = true;

                            getForm(data);

                        }else{
                            alert({
                                title: 'Create words error!',
                                content: obj.req_response_msg + '<br>Please refresh this page if you want to use '+ self.getPaymentTitle(),
                                actions: {
                                    always: function(){}
                                }
                            });
                        }
                    },

                    error: function (xhr, status, error) {
                        alert({
                            title: 'Create words error!',
                            content: 'Please refresh this page if you want to use '+ self.getPaymentTitle(),
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
                    this.dokuObj = $.extend(this.dokuObj, response);

                    $.ajax({
                        type: 'POST',
                        url: url.build('doku/payment/'+ self.dokuObj.req_url_payment),
                        data: {dataResponse: JSON.stringify(self.dokuObj)},
                        showLoader: true,

                        success: function (response) {
                            var obj = $.parseJSON(response);

                            if(obj.err == false){
                                self.placeOrder()
                            }else{
                                alert({
                                    title: 'Payment error!',
                                    content: 'Error code : '+ obj.res_response_code + '<br>Please retry payment',
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
            },
        });
    }
);
