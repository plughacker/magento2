var PlugBoletoModel = function (formObject) {
    this.formObject = formObject;
    this.errors = [];
};

PlugBoletoModel.prototype.placeOrder = function (placeOrderObject) {
    this.placeOrderObject = placeOrderObject;
    this.placeOrderObject.placeOrder();
}

PlugBoletoModel.prototype.validate = function () {
    var multibuyerValidator = new PlugMultibuyerValidator(this.formObject);
    var isMultibuyerValid = multibuyerValidator.validate();
    if (isMultibuyerValid) {
        return true;
    }
    return false;
};

PlugBoletoModel.prototype.addErrors = function (error) {
    this.errors.push({message: error});
}

PlugBoletoModel.prototype.getData = function () {
    var data = {
        'method': "plug_billet",
        'additional_data': {}
    };
    if (typeof this.formObject.multibuyer != 'undefined' &&
        typeof this.formObject.multibuyer.showMultibuyer != 'undefined' &&
        this.formObject.multibuyer.showMultibuyer.prop( "checked" ) === true
    ) {
        multibuyer = this.formObject.multibuyer;
        fullName = multibuyer.firstname.val() + ' ' + multibuyer.lastname.val();

        data.additional_data.billet_buyer_checkbox = 1;
        data.additional_data.billet_buyer_name = fullName;
        data.additional_data.billet_buyer_email = multibuyer.email.val();
        data.additional_data.billet_buyer_document = multibuyer.document.val();
        data.additional_data.billet_buyer_street_title = multibuyer.street.val();
        data.additional_data.billet_buyer_street_number = multibuyer.number.val();
        data.additional_data.billet_buyer_street_complement = multibuyer.complement.val();
        data.additional_data.billet_buyer_zipcode = multibuyer.zipcode.val();
        data.additional_data.billet_buyer_neighborhood = multibuyer.neighborhood.val();
        data.additional_data.billet_buyer_city = multibuyer.city.val();
        data.additional_data.billet_buyer_state = multibuyer.state.val();
        data.additional_data.billet_buyer_home_phone = multibuyer.homePhone.val();
        data.additional_data.billet_buyer_mobile_phone = multibuyer.mobilePhone.val();
    }

    return data;
}
