define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder'
], function (storage, urlBuilder) {
    return function (orderId) {
        var serviceUrl = urlBuilder.createUrl('/plug/redirect-after-placeorder/:orderId/link', {
            orderId: orderId
        });
        return storage.post(serviceUrl, false);
    };
});
