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
                template: 'Doku_MerchantHosted/payment/core',
                setWindow: false,
                dokuObj: {},
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
                return window.checkoutConfig.payment.core.mall_id
            },

            getPaymentTitle: function(){
                return window.checkoutConfig.payment.core.payment_title
            },

            getPaymentChannels: function(){
                return $.parseJSON(window.checkoutConfig.payment.core.payment_channels);
            },

            doPaymentChannel: function(data, event){
                loader.show;
                $("fieldset[id^='form-']").hide();
                $("[doku-div='form-payment'] :input").remove();
                $("#cc_number-field input").remove();
                $("#challenge_code_1-field input").remove();
                $("#response_token-field input").remove();
                this.dokuObj.req_payment_channel = event.target.value;

                if(event.target.value != '') {
                    this.dokuObj.req_email = (window.isCustomerLoggedIn ? window.customerData.email : checkout.getValidatedEmailValue());
                    if(event.target.value == '04' || event.target.value == '15') {
                        $("#form-" + event.target.value).show();
                        if (event.target.value == '04') {
                            this.dokuObj.req_custom_form = ['username-field', 'password-field'];
                            this.dokuObj.req_url_payment = 'orderwallet';
                        } else if (event.target.value == '15') {
                            this.dokuObj.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];
                            this.dokuObj.req_url_payment = 'ordercc';
                            this.checkToken();
                        }
                        this.getDokuForm();
                    }else if(event.target.value == '02'){
                        $("#form-" + event.target.value).show();
                        $("#cc_number-field").append('<input type="text" id="cc_number" name="cc_number" class="input-text cc-number"/>');
                        $("#challenge_code_1-field").append('<input type="text" id="challenge_code_1" name="challenge_code_1" readonly="true" class="input-text"/>');
                        $("#response_token-field").append('<input type="text" id="response_token" name="response_token" class="input-text"/>');

                        this.getChallengeCode3();
                        this.dokuObj.req_url_payment = 'ordermandiriclickpay';

                        var data = {};

                        data.req_cc_field = 'cc_number';
                        data.req_challenge_field = 'challenge_code_1';

                        dokuMandiriInitiate(data);
                    }else{
                        this.dokuObj.req_url_payment = 'orderva';
                    }
                }
                loader.hide;
            },

            dokuToken: function(){
                if(this.dokuObj.req_payment_channel != undefined && this.dokuObj.req_payment_channel != '') {
                    if(this.dokuObj.req_payment_channel == '04' || this.dokuObj.req_payment_channel == '15'){
                        DokuToken(getToken);
                    }else if(this.dokuObj.req_payment_channel == '02'){
                        this.dokuObj.challenge_code2 = '0000100000';
                        this.doMandiriClickPay();
                    }else{
                        this.generateCode();
                    }
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
                    type: 'POST',
                    url: url.build('doku/payment/words'),
                    showLoader: true,

                    success: function (response) {
                        var obj = $.parseJSON(response);
                        if(obj.err == false){

                            self.dokuObj = $.extend(self.dokuObj, obj);

                            var data = {};
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

            generateCode: function(){
                var self = this;
                $.ajax({
                    type: 'POST',
                    url: url.build('doku/payment/'+ self.dokuObj.req_url_payment),
                    data: {dataResponse: JSON.stringify(self.dokuObj)},
                    showLoader: true,

                    success: function (response) {
                        var obj = $.parseJSON(response);

                        if (obj.err == false) {
                            self.placeOrder()
                        } else {
                            alert({
                                title: 'Payment error!',
                                content: 'Error code : ' + obj.res_response_code + '<br>Please retry payment',
                                actions: {
                                    always: function () {
                                    }
                                }
                            });
                        }

                    },
                    error: function (xhr, status, error) {
                        alert({
                            title: 'Generate Code Error!',
                            content: 'Please retry payment',
                            actions: {
                                always: function () {
                                }
                            }
                        });
                    }
                });
            },

            getChallengeCode3: function () {
                var challenge3 = Math.floor(Math.random() * 999999999);
                $("#challenge_code_3-label").text("Challenge Code 3 : "+ challenge3);
                $("#challenge_code_3").val(challenge3);
                $('.cc-number').payment('formatCardNumber');
                this.dokuObj.challenge_code3 = challenge3;
            },

            doMandiriClickPay: function () {
                var self = this;
                this.dokuObj.response_token = $("#response_token").val();
                this.dokuObj.challenge_code1 = $("#challenge_code_1").val();
                this.dokuObj.cc_number = $("#cc_number").val();

                $.ajax({
                    type: 'POST',
                    url: url.build('doku/payment/'+ self.dokuObj.req_url_payment),
                    data: {dataResponse: JSON.stringify(self.dokuObj)},
                    showLoader: true,

                    success: function (response) {
                        var obj = $.parseJSON(response);

                        if (obj.err == false) {
                            self.placeOrder()
                        } else {
                            alert({
                                title: 'Payment error!',
                                content: 'Error code : ' + obj.res_response_code + '<br>Please retry payment',
                                actions: {
                                    always: function () {
                                    }
                                }
                            });
                        }

                    },
                    error: function (xhr, status, error) {
                        alert({
                            title: 'Payment Error!',
                            content: 'Please retry payment',
                            actions: {
                                always: function () {
                                }
                            }
                        });
                    }
                });

            },
            checkToken: function(){
                var self = this;

                console.log('url : '+ url.build('doku/token'));

                if(window.isCustomerLoggedIn){
                    $.ajax({
                        type: 'POST',
                        url: url.build('doku/token'),
                        data: {dataResponse: JSON.stringify(self.dokuObj)},
                        showLoader: true,
                        success: function (response) {
                            var obj = $.parseJSON(response);

                            if (obj.err == false) {
                                //self.placeOrder()
                            } else {
                                alert({
                                    title: 'Check Token Error!',
                                    content: 'Error code : ' + obj.res_response_code + '<br>Please select payment channel again',
                                    actions: {
                                        always: function () {
                                            $("#payment_channels").val("");
                                        }
                                    }
                                });
                            }

                        },
                        error: function (xhr, status, error) {
                            alert({
                                title: 'Check Token Error!',
                                content: 'Please select payment channel again',
                                actions: {
                                    always: function () {
                                        $("#payment_channels").val("");
                                    }
                                }
                            });
                        }
                    });
                }
            }
        });
    }
);
