PlugPixModel = function (formObject) {
    this.formObject = formObject;
    this.errors = [];
};

PlugPixModel.prototype.placeOrder = function (placeOrderObject) {
    this.placeOrderObject = placeOrderObject;
    this.placeOrderObject.placeOrder();
}

PlugPixModel.prototype.validate = function () {
    var multibuyerValidator = new PlugMultibuyerValidator(this.formObject);
    var isMultibuyerValid = multibuyerValidator.validate();
    if (isMultibuyerValid) {
        return true;
    }
    return false;
};

PlugPixModel.prototype.addErrors = function (error) {
    this.errors.push({message: error});
}

PlugPixModel.prototype.getData = function () {
    var data = {
        'method': "plug_pix",
        'additional_data': {}
    };

    if (typeof this.formObject.multibuyer != 'undefined' &&
        typeof this.formObject.multibuyer.showMultibuyer != 'undefined' &&
        this.formObject.multibuyer.showMultibuyer.prop( "checked" ) === true
    ) {
        multibuyer = this.formObject.multibuyer;
        fullName = multibuyer.firstname.val() + ' ' + multibuyer.lastname.val();
        data.additional_data.pix_buyer_checkbox = 1;
        data.additional_data.pix_buyer_name = fullName;
        data.additional_data.pix_buyer_email = multibuyer.email.val();
        data.additional_data.pix_buyer_document = multibuyer.document.val();
        data.additional_data.pix_buyer_street_title = multibuyer.street.val();
        data.additional_data.pix_buyer_street_number = multibuyer.number.val();
        data.additional_data.pix_buyer_street_complement = multibuyer.complement.val();
        data.additional_data.pix_buyer_zipcode = multibuyer.zipcode.val();
        data.additional_data.pix_buyer_neighborhood = multibuyer.neighborhood.val();
        data.additional_data.pix_buyer_city = multibuyer.city.val();
        data.additional_data.pix_buyer_state = multibuyer.state.val();
        data.additional_data.pix_buyer_home_phone = multibuyer.homePhone.val();
        data.additional_data.pix_buyer_mobile_phone = multibuyer.mobilePhone.val();
    }

    return data;
}
