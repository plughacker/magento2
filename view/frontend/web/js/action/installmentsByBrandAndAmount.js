define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder'
], function (storage, urlBuilder) {
    return function (brand, amount) {
        var serviceUrl = urlBuilder.createUrl('/plug/installments/brandbyamount/' + brand + '/' + amount, {});
        return storage.get(
            serviceUrl, false
        )
    };
});
