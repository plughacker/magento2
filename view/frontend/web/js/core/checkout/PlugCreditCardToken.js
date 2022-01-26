var PlugCreditCardToken = function (formObject, documentNumber = null) {
    this.documentNumber = documentNumber;
    if (documentNumber != null) {
        this.documentNumber = documentNumber.replace(/(\.|\/|\-)/g,"");
    }
    this.formObject = formObject;
};

PlugCreditCardToken.prototype.getDataToGenerateToken = function () {
    return {
        creditCard : {
            cardHolderName: this.formObject.creditCardHolderName.val(),
            cardNumber: this.formObject.creditCardNumber.val(),
            cardExpirationDate: this.formObject.creditCardExpMonth.val() + '/' + this.formObject.creditCardExpYear.val(),
            cardCvv: this.formObject.creditCardCvv.val()
        }
    };
}

PlugCreditCardToken.prototype.getToken = function (url) {
    var data = this.getDataToGenerateToken();
    return jQuery.ajax({
        method: 'POST',
        beforeSend: function(request) {
            request.setRequestHeader("Content-type", 'application/json');
        },
        url: url,
        async: false,
        cache: true,
        data: JSON.stringify(data)
    });
}
