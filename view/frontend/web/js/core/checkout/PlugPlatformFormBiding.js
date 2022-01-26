var PlugFormObject = {};
var PlugPlatformConfig = {};

PlugPlatformConfig.bind = function (platformConfig) {
    grandTotal = parseFloat(platformConfig.grand_total);

    clientId = platformConfig.payment.ccform.pk_token;

    urls = {
        base: platformConfig.base_url,
        installments : platformConfig.moduleUrls.installments,
        creditCardTokenUrl : platformConfig.moduleUrls.creditCardTokenUrl
    };

    currency = {
        code : platformConfig.quoteData.base_currency_code,
        decimalSeparator : platformConfig.basePriceFormat.decimalSymbol,
        precision : platformConfig.basePriceFormat.precision
    };

    text = {
        months: platformConfig.payment.ccform.months,
        years: platformConfig.payment.ccform.years
    }

    avaliableBrands = this.getAvaliableBrands(platformConfig);
    savedAllCards = this.getSavedCreditCards(platformConfig);

    loader = {
        start: platformConfig.loader.startLoader,
        stop: platformConfig.loader.stopLoader
    };
    totals = platformConfig.totalsData;

    var config = {
        avaliableBrands: avaliableBrands,
        orderAmount : grandTotal.toFixed(platformConfig.basePriceFormat.precision),
        urls: urls,
        currency : currency,
        text: text,
        clientId: clientId,
        totals: totals,
        loader: loader,
        addresses: platformConfig.addresses,
        updateTotals: platformConfig.updateTotals,
        savedAllCards: savedAllCards,
        region_states: platformConfig.region_states,
        isMultibuyerEnabled: platformConfig.is_multi_buyer_enabled
    };

    this.PlugPlatformConfig = config;

    return this.PlugPlatformConfig;
};

PlugPlatformConfig.getAvaliableBrands = function (data) {
    return {
        'plug_creditcard': this.getBrands(
            data,
            data.payment.ccform.availableTypes.plug_creditcard
        )
    };
}

PlugPlatformConfig.getBrands = function (data, paymentMethodBrands) {
    var availableBrands = [];

    if (paymentMethodBrands !== undefined) {
        var brands = Object.keys(paymentMethodBrands);

        for (var i = 0, len = brands.length; i < len; i++) {
            url = data.payment.ccform.icons[brands[i]].url
            fixArray = [];
            imageUrl = fixArray.concat(url);

            availableBrands[i] = {
                'title': brands[i],
                'image': imageUrl[0]

            };
        }
    }
    return availableBrands;
}

PlugFormObject.creditCardInit = function (isMultibuyerEnabled) {

    this.PlugFormObject = {};

    var containerSelector = '#plug_creditcard-form';

    if (typeof jQuery(containerSelector).html() == 'undefined') {
        this.PlugFormObject = null;
        return;
    }

    var creditCardForm = {
        'containerSelector' : containerSelector,
        "creditCardNumber" : jQuery(containerSelector + " .cc_number"),
        "creditCardHolderName" : jQuery(containerSelector + " .cc_owner"),
        "creditCardExpMonth" : jQuery(containerSelector + " .cc_exp_month"),
        "creditCardExpYear" : jQuery(containerSelector + " .cc_exp_year"),
        "creditCardCvv" : jQuery(containerSelector + " .cc_cid"),
        "creditCardInstallments" : jQuery(containerSelector + " .cc_installments"),
        "creditCardBrand" : jQuery(containerSelector + " .cc_type"),
        "creditCardToken" : jQuery(containerSelector + " .cc_token"),
        "inputAmount" : jQuery(containerSelector + " .cc_amount"),
        "inputAmountContainer" : jQuery(containerSelector + " .amount-container"),
        "savedCreditCardSelect" : jQuery(containerSelector + " .cc_saved_creditcards"),
        "saveThisCard" : jQuery(containerSelector + " .save_this_card")
    };

    if (isMultibuyerEnabled) {
        var multibuyerForm = {
            "showMultibuyer" : jQuery(containerSelector + " .show_multibuyer"),
            "firstname" : jQuery(containerSelector + " .multibuyer_firstname"),
            "lastname" : jQuery(containerSelector + " .multibuyer_lastname"),
            "email" : jQuery(containerSelector + " .multibuyer_email"),
            "zipcode" : jQuery(containerSelector + " .multibuyer_zipcode"),
            "document" : jQuery(containerSelector + " .multibuyer_document"),
            "street" : jQuery(containerSelector + " .multibuyer_street"),
            "number" : jQuery(containerSelector + " .multibuyer_number"),
            "complement" : jQuery(containerSelector + " .multibuyer_complement"),
            "neighborhood" : jQuery(containerSelector + " .multibuyer_neighborhood"),
            "city" : jQuery(containerSelector + " .multibuyer_city"),
            "state" : jQuery(containerSelector + " .multibuyer_state"),
            "homePhone" : jQuery(containerSelector + " .multibuyer_home_phone"),
            "mobilePhone" : jQuery(containerSelector + " .multibuyer_mobile_phone")
        }
    }

    this.PlugFormObject = creditCardForm;
    this.PlugFormObject.numberOfPaymentForms = 1;
    this.PlugFormObject.multibuyer = multibuyerForm;
    this.PlugFormObject.savedCardSelectUsed = 'plug_creditcard';

    return this.PlugFormObject;
};

PlugFormObject.getMultibuyerForm = function (containerSelector) {
    return {
        "showMultibuyer" : jQuery(containerSelector + " .show_multibuyer"),
        "firstname" : jQuery(containerSelector + " .multibuyer_firstname"),
        "lastname" : jQuery(containerSelector + " .multibuyer_lastname"),
        "email" : jQuery(containerSelector + " .multibuyer_email"),
        "zipcode" : jQuery(containerSelector + " .multibuyer_zipcode"),
        "document" : jQuery(containerSelector + " .multibuyer_document"),
        "street" : jQuery(containerSelector + " .multibuyer_street"),
        "number" : jQuery(containerSelector + " .multibuyer_number"),
        "complement" : jQuery(containerSelector + " .multibuyer_complement"),
        "neighborhood" : jQuery(containerSelector + " .multibuyer_neighborhood"),
        "city" : jQuery(containerSelector + " .multibuyer_city"),
        "state" : jQuery(containerSelector + " .multibuyer_state"),
        "homePhone" : jQuery(containerSelector + " .multibuyer_home_phone"),
        "mobilePhone" : jQuery(containerSelector + " .multibuyer_mobile_phone")
    }
}


PlugFormObject.pixInit = function (isMultibuyerEnabled) {

    this.PlugFormObject = {};

    var containerSelector = '#plug_pix-form';

    if (typeof jQuery(containerSelector).html() == 'undefined') {
        this.PlugFormObject = null;
        return;
    }

    var pixElements = {
        'containerSelector' : containerSelector,
        "inputAmount" : jQuery(containerSelector + " .cc_amount"),
        "inputAmountContainer" : jQuery(containerSelector + " .amount-container")
    };

    if (isMultibuyerEnabled) {
        var multibuyerForm = {
            "showMultibuyer": jQuery(containerSelector + " .show_multibuyer"),
            "firstname": jQuery(containerSelector + " .multibuyer_firstname"),
            "lastname": jQuery(containerSelector + " .multibuyer_lastname"),
            "email": jQuery(containerSelector + " .multibuyer_email"),
            "zipcode": jQuery(containerSelector + " .multibuyer_zipcode"),
            "document": jQuery(containerSelector + " .multibuyer_document"),
            "street": jQuery(containerSelector + " .multibuyer_street"),
            "number": jQuery(containerSelector + " .multibuyer_number"),
            "complement": jQuery(containerSelector + " .multibuyer_complement"),
            "neighborhood": jQuery(containerSelector + " .multibuyer_neighborhood"),
            "city": jQuery(containerSelector + " .multibuyer_city"),
            "state": jQuery(containerSelector + " .multibuyer_state"),
            "homePhone": jQuery(containerSelector + " .multibuyer_home_phone"),
            "mobilePhone": jQuery(containerSelector + " .multibuyer_mobile_phone")
        }
    }

    this.PlugFormObject = pixElements;
    this.PlugFormObject.numberOfPaymentForms = 1;
    this.PlugFormObject.multibuyer = multibuyerForm;
    return this.PlugFormObject;
}

PlugFormObject.boletoInit = function (isMultibuyerEnabled) {

    this.PlugFormObject = {};

    var containerSelector = '#plug_billet-form';

    if (typeof jQuery(containerSelector).html() == 'undefined') {
        this.PlugFormObject = null;
        return;
    }

    var boletoElements = {
        'containerSelector' : containerSelector,
        "inputAmount" : jQuery(containerSelector + " .cc_amount"),
        "inputAmountContainer" : jQuery(containerSelector + " .amount-container")
    };

    if (isMultibuyerEnabled) {
        var multibuyerForm = {
            "showMultibuyer": jQuery(containerSelector + " .show_multibuyer"),
            "firstname": jQuery(containerSelector + " .multibuyer_firstname"),
            "lastname": jQuery(containerSelector + " .multibuyer_lastname"),
            "email": jQuery(containerSelector + " .multibuyer_email"),
            "zipcode": jQuery(containerSelector + " .multibuyer_zipcode"),
            "document": jQuery(containerSelector + " .multibuyer_document"),
            "street": jQuery(containerSelector + " .multibuyer_street"),
            "number": jQuery(containerSelector + " .multibuyer_number"),
            "complement": jQuery(containerSelector + " .multibuyer_complement"),
            "neighborhood": jQuery(containerSelector + " .multibuyer_neighborhood"),
            "city": jQuery(containerSelector + " .multibuyer_city"),
            "state": jQuery(containerSelector + " .multibuyer_state"),
            "homePhone": jQuery(containerSelector + " .multibuyer_home_phone"),
            "mobilePhone": jQuery(containerSelector + " .multibuyer_mobile_phone")
        }
    }

    this.PlugFormObject = boletoElements;
    this.PlugFormObject.numberOfPaymentForms = 1;
    this.PlugFormObject.multibuyer = multibuyerForm;
    return this.PlugFormObject;
}

PlugPlatformConfig.getSavedCreditCards = function (platFormConfig) {
    var creditCard = null;

    if (
        platFormConfig.payment.plug_creditcard.enabled_saved_cards &&
        typeof(platFormConfig.payment.plug_creditcard.cards != "undefined")
    ) {
        creditCard = platFormConfig.payment.plug_creditcard.cards;
    }

    return {
        "plug_creditcard": creditCard
    };
};
