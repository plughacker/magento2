define([
    "Magento_Checkout/js/view/payment/default",
    "ko",
    "jquery",
    'PlugHacker_PlugPagamentos/js/action/creditCardToken',
    'PlugHacker_PlugPagamentos/js/action/installmentsByBrand',
    "Magento_Checkout/js/model/quote",
    "Magento_Catalog/js/price-utils",
    "Magento_Checkout/js/model/totals",
    "Magento_Checkout/js/checkout-data",
    "Magento_Checkout/js/action/select-payment-method",
    "Magento_Checkout/js/model/full-screen-loader",
    "Magento_Checkout/js/model/payment/additional-validators",
    "Magento_Checkout/js/action/redirect-on-success",
    "mage/translate",
    "Magento_Ui/js/model/messageList",
    'Magento_Checkout/js/model/url-builder',
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugPaymentModuleBootstrap",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugPaymentMethodController",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugPlatformPlaceOrder",
    'PlugHacker_PlugPagamentos/js/model/credit-card-validation/credit-card-number-validator',
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugBin",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugPlatformFormBiding",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugPlatformFormHandler",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugCreditCardToken",
    "PlugHacker_PlugPagamentos/js/core/checkout/PlugInstallments",
    "PlugHacker_PlugPagamentos/js/core/validators/PlugCreditCardValidator",
    "PlugHacker_PlugPagamentos/js/core/validators/PlugCustomerValidator",
    "PlugHacker_PlugPagamentos/js/core/validators/PlugMultibuyerValidator"
], function(
    Component,
    ko,
    $,
    creditCardTokenAction,
    installmentsAction,
    quote,
    priceUtils,
    totals,
    checkoutData,
    selectPaymentMethodAction,
    fullScreenLoader,
    additionalValidators,
    redirectOnSuccessAction,
    $t,
    globalMessageList,
    urlBuilder,
    PlugCore,
    PlugPaymentController,
    PlugPlatformPlaceOrder,
    cardNumberValidator
) {
    window.PlugCore.messageList = globalMessageList;
    return Component.extend({
        initPaymentMethod: function() {
            plugPlatFormConfig = window.checkoutConfig;
            plugPlatFormConfig.plugModuleUrls = {};
            plugPlatFormConfig.grand_total = quote.getTotals()().grand_total;
            var baseUrl = plugPlatFormConfig.payment.plugccform.base_url;
            if (quote.billingAddress() &&
                typeof quote.billingAddress() != "undefined" &&
                quote.billingAddress().vatId === ""
            ) {
                quote.billingAddress().vatId = plugPlatFormConfig.customerData.taxvat
            }
            plugPlatFormConfig.base_url = baseUrl;
            plugPlatFormConfig.plugModuleUrls.installments = baseUrl + installmentsAction();
            plugPlatFormConfig.plugModuleUrls.creditCardTokenUrl = baseUrl + creditCardTokenAction();
            plugPlatFormConfig.addresses = {
                billingAddress: quote.billingAddress()
            };
            plugPlatFormConfig.loader = fullScreenLoader;

            /** @fixme Update total should be moved to platformFormBinging **/
            plugPlatFormConfig.updateTotals = quote;

            window.PlugCore.plugPlatFormConfig = plugPlatFormConfig;
            window.PlugCore.initPaymentMethod(
                this.getModel(),
                plugPlatFormConfig,
                cardNumberValidator
            );
        },

        getData: function() {
            return {
                "method": this.item.method
            };
        },

        getKey : function() {
            return window.checkoutConfig.payment.plugccform.pk_token;
        },

        /**
         * Place order
         */
        beforeplaceOrder: function(data, event){
            var _self = this;
            window.PlugCore.plugPlatFormConfig.addresses.billingAddress = quote.billingAddress();
            var PlugPlatformPlaceOrder = {
                obj : _self,
                data: data,
                event: event
            };

            window.PlugCore.placeOrder(
                PlugPlatformPlaceOrder,
                this.getModel()
            );
        },
        /**
         * Select current payment token
         */
        selectPaymentMethod: function() {
            var data = this.getData();
            if (data === undefined) {
                plugPlatFormConfig = window.PlugCore.plugPlatFormConfig;
                window.PlugCore.init(this.getModel(), plugPlatFormConfig, cardNumberValidator);
            }
            selectPaymentMethodAction(this.getData());
            checkoutData.setSelectedPaymentMethod(this.item.method);
            return true;
        },

        updateTotalWithTax: function(newTax) {
            if (typeof this.oldInstallmentTax == "undefined") {
                this.oldInstallmentTax = 0;
            }
            var total = quote.getTotals()();
            var subTotalIndex = null;
            for (var i = 0, len = total.total_segments.length; i < len; i++) {
                if (total.total_segments[i].code == "grand_total") {
                    subTotalIndex = i;
                    continue;
                }
                if (total.total_segments[i].code != "tax")
                    continue;
                total.total_segments[i].value = newTax;
            }
            total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value - this.oldInstallmentTax;
            total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value + parseFloat(newTax);
            total.tax_amount = parseFloat(newTax);
            total.base_tax_amount = parseFloat(newTax);
            this.oldInstallmentTax = newTax;
            window.checkoutConfig.payment.plugccform.installments.value = newTax;
            quote.setTotals(total);
        },
    })
});
