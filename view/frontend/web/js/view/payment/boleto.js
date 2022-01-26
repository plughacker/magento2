define([
    "PlugHacker_PlugPagamentos/js/view/payment/default",
    "PlugHacker_PlugPagamentos/js/core/models/PlugBoletoModel"
], function (Component, $t) {
    return Component.extend({
        defaults: {
            template: "PlugHacker_PlugPagamentos/payment/default"
        },
        getCode: function() {
            return "plug_billet";
        },
        isActive: function() {
            return window.checkoutConfig.payment.plug_billet.active;
        },
        getTitle: function() {
            return window.checkoutConfig.payment.plug_billet.title;
        },
        getBase: function() {
            return "PlugHacker_PlugPagamentos/payment/boleto";
        },
        getForm: function() {
            return "PlugHacker_PlugPagamentos/payment/boleto-form";
        },
        getMultibuyerForm: function () {
            return "PlugHacker_PlugPagamentos/payment/multibuyer-form";
        },
        getText: function () {
            return window.checkoutConfig.payment.plug_billet.text;
        },
        getModel: function() {
            return 'boleto';
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
