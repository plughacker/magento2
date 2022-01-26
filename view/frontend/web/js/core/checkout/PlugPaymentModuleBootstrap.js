var PlugCore = {
    paymentMethod : []
};

PlugCore.initPaymentMethod = function (methodCode, platformConfig, cardNumberValidator) {
    var _self = this;
    setTimeout(function() {
        _self.init(methodCode, platformConfig, cardNumberValidator);
    }, 1000);
};

PlugCore.init = function (methodCode, platformConfig, cardNumberValidator) {
    this.paymentMethod[methodCode] = new PlugPaymentMethodController(methodCode, platformConfig, cardNumberValidator);
    this.paymentMethod[methodCode].init();
}

PlugCore.initBin = function (methodCode, obj) {
    this.paymentMethod[methodCode].initBin(obj);
};

PlugCore.validatePaymentMethod = function (methodCode) {
    this.paymentMethod = new PlugPaymentMethodController(methodCode);
    this.paymentMethod.init();
    return this.paymentMethod.formValidation();
};

PlugCore.placeOrder = function(platformObject, model) {
    if (this.paymentMethod[model].model.validate()) {
        try {
            var platformOrderPlace = new PlugPlatformPlaceOrder(
                platformObject.obj,
                platformObject.data,
                platformObject.event
            );
            this.paymentMethod[model].placeOrder(platformOrderPlace);
        } catch (e) {
            console.log(e)
        }
    }
    var errors = this.paymentMethod[model].model.errors;
    if (errors.length > 0) {
        for (index in errors) {
            this.messageList.addErrorMessage(errors[index]);
        }
        jQuery("html, body").animate({scrollTop: 0}, 600);
        console.log(errors);
    }
}
