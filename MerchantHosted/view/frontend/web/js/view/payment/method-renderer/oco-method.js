/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        var formDefault = '<ul>' +
            '<div doku-div="form-payment">' +
            '<li>' +
            '<div class="styled-input fleft width50" id="cc-field">' +
            '<label>CC</label>' +
            '</div>' +
            '<div class="styled-input fright width50" id="cvv-field">' +
            '<label>CVV</label>' +
            '</div>' +
            '<div class="clear"></div>' +
            '</li>' +
            '<li>' +
            '<div class="styled-input fleft width50" id="name-field">' +
            '<label>NAME</label>' +
            '</div>' +
            '<div class="styled-input fright width50" id="exp-field">' +
            '<label>EXP</label>' +
            '</div>' +
            '<div class="clear"></div>' +
            '</li>' +
            '</div>' +
            '<li>' +
            '<div class="styled-input fleft width50">' +
            '<input type="text" name="email_cc" id="email_cc" required />' +
            '<label>Email</label>' +
            '</div>' +
            '<div class="styled-input fright width50">' +
                '<input type="text" name="phone_cc" id="phone_cc" required />' +
            '<label>Phone</label>' +
            '</div>' +
            '<div class="clear"></div>' +
                '</li>' +
                '<li>' +
                '<div class="styled-input fleft width50">' +
                '<input type="text" name="address" id="address" required />' +
            '<label>Billing Address</label>' +
            '</div>' +
            '<div class="styled-input fright width50">' +
                '<input type="text" name="id_number" id="id_number" required />' +
            '<label>ID Number</label>' +
            '</div>' +
            '<div class="clear"></div>' +
                '</li>' +
                '</ul>';

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco'
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            initObservable: function() {
                $.getScript("https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js", function() {});
                $.getScript("https://staging.doku.com/doku-js/assets/js/doku.js?version="+ new Date().getTime(), function() {});

                $("head").append("<link>");
                var css = $("head").children(":last");
                css.attr({
                    rel:  "stylesheet",
                    type: "text/css",
                    href: "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
                });
                
                return this;
            },

            initDoku: function(){

                $.fancybox.open([
                    {
                        closeClick: false,
                        type: 'iframe',
                        openEffect: 'fade',
                        closeEffect: 'fade',
                        openSpeed: 'slow',
                        closeSpeed: 'slow',
                        content: formDefault,
                        closeBtn: false,
                        autoResize: true,
                        helpers : {
                            overlay : {
                                closeClick : false
                            }
                        }
                    }

                ]);

                var data = new Object();

                data.req_merchant_code = '2074'; //mall id or merchant id
                data.req_chain_merchant = 'NA'; //chain merchant id
                data.req_payment_channel = '15'; //payment channel
                data.req_basket = '';
                data.req_server_url = 'http://crm.doku.com/doku-library-staging/example-payment/merchant-example.php'; //merchant payment url to receive pairing code & token
                data.req_transaction_id = 'invoice_1477040126'; //invoice no
                data.req_amount = '10000.00';
                data.req_currency = '360'; //360 for IDR
                data.req_words = 'f2be9d27c3ce8b01eba427f1b08c399a0d5051ac'; //your merchant unique key
                data.req_session_id = '1477040127626'; //your server timestamp
                data.req_form_type = 'full';

                getForm(data);

            },

           
        });
    }
);
