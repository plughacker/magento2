define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    "use strict";
    rendererList.push(
        {
            type: "plug_pix",
            component: "PlugHacker_PlugPagamentos/js/view/payment/pix"
        },
        {
            type: "plug_creditcard",
            component: "PlugHacker_PlugPagamentos/js/view/payment/creditcard"
        },
        {
            type: "plug_billet",
            component: "PlugHacker_PlugPagamentos/js/view/payment/boleto"
        }
    );
    return Component.extend({});
});
