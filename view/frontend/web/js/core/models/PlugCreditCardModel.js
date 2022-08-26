var PlugCreditCardModel = function (formObject, clientId, plugPlatformConfig) {
    this.formObject = formObject;
    this.clientId = clientId;
    this.plugPlatformConfig = plugPlatformConfig;
    this.errors = [];
};

PlugCreditCardModel.prototype.placeOrder = function (placeOrderObject) {
    this.placeOrderObject = placeOrderObject;
    var _self = this;
    if (typeof _self.formObject.savedCreditCardSelect.val() != 'undefined' &&
        _self.formObject.savedCreditCardSelect.html().length > 1 &&
        _self.formObject.savedCreditCardSelect.val() !== 'new' &&
        _self.formObject.savedCreditCardSelect.val() !== ''
    ) {
        _self.placeOrderObject.placeOrder();
        return;
    }

    this.getCreditCardToken(
        function (data) {
            data = JSON.parse(data);
            if (typeof data.tokenId !== 'undefined' && data.tokenId !== '') {
                _self.formObject.creditCardToken.val(data.tokenId);
                _self.placeOrderObject.placeOrder();
            } else {
                _self.addErrors("invalid credit card token");
            }
        },
        function (error) {
            var errors = error.responseJSON;
            _self.addErrors("Cartão inválido. Por favor, verifique os dados digitados e tente novamente");
        }
    );
};

PlugCreditCardModel.prototype.addErrors = function (error) {
    this.errors.push({message: error});
}

PlugCreditCardModel.prototype.validate = function () {
    var creditCardValidator = new PlugCreditCardValidator(this.formObject);
    var isCreditCardValid = creditCardValidator.validate();
    var multibuyerValidator = new PlugMultibuyerValidator(this.formObject);
    var isMultibuyerValid = multibuyerValidator.validate();
    if (isCreditCardValid && isMultibuyerValid) {
        return true;
    }
    return false;
};

PlugCreditCardModel.prototype.getCreditCardToken = function (success, error) {
    var modelToken = new PlugCreditCardToken(this.formObject);
    modelToken.getToken(this.plugPlatformConfig.urls.creditCardTokenUrl)
        .done(success)
        .fail(error);
};

PlugCreditCardModel.prototype.getData = function () {
    saveThiscard = 0;
    var formObject = this.formObject;
    if (formObject.saveThisCard.prop( "checked" )) {
        saveThiscard = 1;
    }

    var data = this.fillData();
    data.additional_data.cc_buyer_checkbox = false;

    if (typeof formObject.multibuyer != 'undefined' &&
        formObject.multibuyer.showMultibuyer.prop( "checked" ) === true
    ) {
        data = this.fillMultibuyerData(data);
    }

    return data;
};

PlugCreditCardModel.prototype.fillData = function() {
    var formObject = this.formObject;
    return {
        'method': "plug_creditcard",
        'additional_data': {
            'cc_type': formObject.creditCardBrand.val(),
            'cc_last_4': this.getLastFourNumbers(),
            'cc_exp_year': formObject.creditCardExpYear.val(),
            'cc_exp_month': formObject.creditCardExpMonth.val(),
            'cc_owner': formObject.creditCardHolderName.val(),
            'cc_savecard': saveThiscard,
            'cc_saved_card': formObject.savedCreditCardSelect.val(),
            'cc_installments': formObject.creditCardInstallments.val(),
            'cc_token_credit_card': formObject.creditCardToken.val(),
            'cc_card_tax_amount' : formObject.creditCardInstallments.find(':selected').attr('interest')
        }
    };
};

PlugCreditCardModel.prototype.fillMultibuyerData = function(data) {
    multibuyer = this.formObject.multibuyer;
    fullname = multibuyer.firstname.val() + ' ' + multibuyer.lastname.val();

    data.additional_data.cc_buyer_checkbox = 1;
    data.additional_data.cc_buyer_name = fullname;
    data.additional_data.cc_buyer_email = multibuyer.email.val();
    data.additional_data.cc_buyer_document = multibuyer.document.val();
    data.additional_data.cc_buyer_street_title = multibuyer.street.val();
    data.additional_data.cc_buyer_street_number = multibuyer.number.val();
    data.additional_data.cc_buyer_street_complement = multibuyer.complement.val();
    data.additional_data.cc_buyer_zipcode = multibuyer.zipcode.val();
    data.additional_data.cc_buyer_neighborhood = multibuyer.neighborhood.val();
    data.additional_data.cc_buyer_city = multibuyer.city.val();
    data.additional_data.cc_buyer_state = multibuyer.state.val();
    data.additional_data.cc_buyer_home_phone = multibuyer.homePhone.val();
    data.additional_data.cc_buyer_mobile_phone = multibuyer.mobilePhone.val();

    return data;
};

PlugCreditCardModel.prototype.getLastFourNumbers = function() {
    var number = this.formObject.creditCardNumber.val();
    if (number !== undefined) {
        return number.slice(-4);
    }
    return "";
}
