/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    let config = window.checkoutConfig.payment;
    if (config['malga_pix'] && config['malga_pix'].isActive) {
        rendererList.push({
            type: 'malga_pix',
            component: 'Malga_Payments/js/view/payment/method-renderer/pix'
        });
    }

    /** Add view logic here if needed */
    return Component.extend({});
});
