/**
 * Copyright 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/

define([
    'Magento_Checkout/js/view/payment/default',
], function (Component) {
    'use strict';

    let config = window.checkoutConfig.payment;
    return Component.extend({
        defaults: {
            template: 'Malga_Payments/payment/pix',
        },
        pixImage: config['malga_pix'].image
        // /**
        //  * Trigger order placing
        //  * @todo code to add recaptcha
        //  */
        // placeOrderClick: function () {
        //     if (this.validateFormFields() && additionalValidators.validate()) {
        //         var isReCaptchaEnabled = window.checkoutConfig.recaptcha_braintree;
        //         if (isReCaptchaEnabled) {
        //             var recaptchaCheckBox = jQuery("#recaptcha-checkout-braintree-wrapper input[name='recaptcha-validate-']");
        //
        //             if (recaptchaCheckBox.length && recaptchaCheckBox.prop('checked') === false) {
        //                 alert($t('Please indicate google recaptcha'));
        //             } else {
        //                 this.placeOrder();
        //             }
        //         } else {
        //             this.placeOrder();
        //         }
        //     }
        // }
    });
});
