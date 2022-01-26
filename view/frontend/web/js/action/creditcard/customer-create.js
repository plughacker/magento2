define([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'mage/url'
], function ($, storage, urlBuilder, mageUrl) {
    return function (data) {
        let serviceUrl = urlBuilder.createUrl('/plug/customer/create/', {});
        return $.ajax({
            method: "POST",
            beforeSend: function(request) {
                request.setRequestHeader("Content-type", 'application/json');
            },
            url: mageUrl.build(serviceUrl),
            cache: false,
            data: JSON.stringify(data)
        });
    };
});
