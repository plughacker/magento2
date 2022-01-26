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
            var _self = this;
            platFormConfig = window.checkoutConfig;
            platFormConfig.moduleUrls = {};
            creditCardTokenUrl = creditCardTokenAction();
            installmentsUrl = installmentsAction();
            platFormConfig.grand_total = quote.getTotals()().grand_total;
            var baseUrl = platFormConfig.payment.ccform.base_url;
            if (quote.billingAddress() &&
                typeof quote.billingAddress() != "undefined" &&
                quote.billingAddress().vatId === ""
            ) {
                quote.billingAddress().vatId = platFormConfig.customerData.taxvat
            }
            platFormConfig.base_url = baseUrl;
            platFormConfig.moduleUrls.installments = baseUrl + installmentsUrl;
            platFormConfig.moduleUrls.creditCardTokenUrl = baseUrl + creditCardTokenUrl;
            platFormConfig.addresses = {
                billingAddress: quote.billingAddress()
            };
            platFormConfig.loader = fullScreenLoader;

            /** @fixme Update total should be moved to platformFormBinging **/
            platFormConfig.updateTotals = quote;
            window.PlugCore.platFormConfig = platFormConfig;
            window.PlugCore.initPaymentMethod(
                this.getModel(),
                platFormConfig,
                cardNumberValidator
            );
        },

        getData: function() {
            return {
                "method": this.item.method
            };
        },

        getKey : function() {
            return window.checkoutConfig.payment.ccform.pk_token;
        },

        /**
         * Place order
         */
        beforeplaceOrder: function(data, event){
            var _self = this;
            window.PlugCore.platFormConfig.addresses.billingAddress = quote.billingAddress();
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
                var platFormConfig = window.PlugCore.platFormConfig;
                window.PlugCore.init(this.getModel(), platFormConfig, cardNumberValidator);
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
            window.checkoutConfig.payment.ccform.installments.value = newTax;
            quote.setTotals(total);
        },
    })
});
