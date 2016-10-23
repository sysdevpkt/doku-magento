/**
 * Copyright © 2016 Doku. All rights reserved.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        function getToken(response){
            console.log('masuk getToken');
            console.log(response);
        }

        rendererList.push(
            {
                type: 'oco',
                component: 'Doku_MerchantHosted/js/view/payment/method-renderer/oco-method'
            }
        );

        /** Add view logic here if needed */

        return Component.extend({});
    }
);