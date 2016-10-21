/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'jquery'
    ],
    function (
        Component,
        rendererList,
        $
    ) {
        'use strict';

        console.log('panggil 1');

        rendererList.push(
            {
                type: 'oco',
                component: 'Doku_MerchantHosted/js/view/payment/method-renderer/oco-method'
            }
        );
        /** Add view logic here if needed */

        console.log('panggil 2');

        return Component.extend({

            initObservable: function() {

                console.log('panggil 3');

                $.getScript("https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js", function() {});
                $.getScript("https://staging.doku.com/doku-js/assets/js/doku.js?version="+ new Date().getTime(), function() {});

                $("head").append("<link>");
                var css = $("head").children(":last");
                css.attr({
                    rel:  "stylesheet",
                    type: "text/css",
                    href: "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
                });

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
                data.req_form_type = 'inline';
                data.req_custom_form = ['cc-field', 'cvv-field', 'name-field', 'exp-field'];

                getForm(data);

                return this;
            },

        });
    }
);