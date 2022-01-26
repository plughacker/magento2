define([
    "PlugHacker_PlugPagamentos/js/view/payment/default",
    "PlugHacker_PlugPagamentos/js/core/models/PlugPixModel"
], function (Component, $t) {
    return Component.extend({
        defaults: {
            template: "PlugHacker_PlugPagamentos/payment/default"
        },

        getCode: function () {
            return "plug_pix";
        },

        isActive: function () {
            return window.checkoutConfig.payment.plug_pix.active;
        },

        getTitle: function () {
            return window.checkoutConfig.payment.plug_pix.title;
        },

        getBase: function () {
            return "PlugHacker_PlugPagamentos/payment/pix";
        },

        getForm: function () {
            return "PlugHacker_PlugPagamentos/payment/pix-form";
        },

        getMultibuyerForm: function () {
            return "PlugHacker_PlugPagamentos/payment/multibuyer-form";
        },

        getText: function () {
            return window.checkoutConfig.payment.plug_pix.text;
        },

        getModel: function () {
            return 'pix';
        },

        getData: function () {
            var paymentMethod = window.PlugCore.paymentMethod[this.getModel()];
            if (paymentMethod === undefined) {
                return paymentMethod;
            }
            var paymentModel = paymentMethod.model;
            return paymentModel.getData();
        },
    });
});
