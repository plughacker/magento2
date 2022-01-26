define(['Magento_Checkout/js/model/url-builder'], function (urlBuilder) {
    return function (brand) {
        return urlBuilder.createUrl('/plug/installments/brandbyamount/', {});
    };
});
