define(['Magento_Checkout/js/model/url-builder'], function (urlBuilder) {
    return function () {
        return urlBuilder.createUrl('/plug/creditcard/token', {});
    };
});
