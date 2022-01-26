var PlugCustomerValidator = function (addressObject) {
    this.addressObject = addressObject;
    this.errors = [];
};

PlugCustomerValidator.prototype.validate = function () {
    var address = this.addressObject;
    if (address == null) {
        this.errors.push("Customer address is required");
        return;
    }

    if (address.vatId <= 0 && address.vatId != null) {
        this.errors.push("Customer document is a required field");
    }

    if (address.street.length < 3) {
        this.errors.push("Invalid address");
    }
}

PlugCustomerValidator.prototype.getErrors = function () {
    return this.errors;
}
