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

        console.log('panggil 4');

        return Component.extend({
            defaults: {
                template: 'Doku_MerchantHosted/payment/oco'
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            initObservable: function() {

                console.log('panggil 5');
                return this;
            },

            initDoku: function(){

                console.log('thaaaaash');

            },

           
        });
    }
);
