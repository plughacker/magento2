var PlugPaymentMethodController = function (methodCode, platformConfig, cardNumberValidator) {
    this.methodCode = methodCode;
    this.platformConfig = platformConfig;
    this.cardNumberValidator = cardNumberValidator;
};

PlugPaymentMethodController.prototype.init = function () {
    var paymentMethodInit = this.methodCode + 'Init';
    this[paymentMethodInit]();
};

PlugPaymentMethodController.prototype.formObject = function (formObject) {
    this.formObject = formObject
};

PlugPaymentMethodController.prototype.formValidation = function () {
    formValidation = this.methodCode + 'Validation';
    return this[formValidation]();
};

PlugPaymentMethodController.prototype.creditcardInit = function () {
    this.platformConfig = PlugPlatformConfig.bind(this.platformConfig);
    this.formObject = PlugFormObject.creditCardInit(this.platformConfig.isMultibuyerEnabled);

    if (!this.formObject) {
        return;
    }

    this.model = new PlugCreditCardModel(
        this.formObject,
        this.platformConfig.clientId,
        this.platformConfig
    );

    this.fillCardAmount(this.formObject, 1);
    this.hideCardAmount(this.formObject);
    this.fillFormText(this.formObject, 'plug_creditcard');
    this.fillSavedCreditCardsSelect(this.formObject);
    this.fillBrandList(this.formObject, 'plug_creditcard');
    this.fillInstallments(this.formObject);

    if (!this.platformConfig.isMultibuyerEnabled) {
        this.removeMultibuyerForm(this.formObject);
    }

    if (this.platformConfig.isMultibuyerEnabled) {
        this.fillMultibuyerStateSelect(this.formObject);
        this.addShowMultibuyerListener(this.formObject);
    }

    this.addCreditCardListeners(this.formObject, this.cardNumberValidator);
    this.modelToken = new PlugCreditCardToken(this.formObject);
};

PlugPaymentMethodController.prototype.pixInit = function () {
    this.platformConfig = PlugPlatformConfig.bind(this.platformConfig);
    this.formObject = PlugFormObject.pixInit(this.platformConfig.isMultibuyerEnabled);

    if (!this.formObject) {
        return;
    }

    this.model = new PlugPixModel(this.formObject);
    this.hideCardAmount(this.formObject);

    if (!this.platformConfig.isMultibuyerEnabled) {
        this.removeMultibuyerForm(this.formObject);
    }

    if (this.platformConfig.isMultibuyerEnabled) {
        this.fillMultibuyerStateSelect(this.formObject);
        this.addShowMultibuyerListener(this.formObject);
    }
};

PlugPaymentMethodController.prototype.boletoInit = function () {
    this.platformConfig = PlugPlatformConfig.bind(this.platformConfig);
    this.formObject = PlugFormObject.boletoInit(this.platformConfig.isMultibuyerEnabled);

    if (!this.formObject) {
        return;
    }

    this.model = new PlugBoletoModel(this.formObject);
    this.hideCardAmount(this.formObject);

    if (!this.platformConfig.isMultibuyerEnabled) {
        this.removeMultibuyerForm(this.formObject);
    }

    if (this.platformConfig.isMultibuyerEnabled) {
        this.fillMultibuyerStateSelect(this.formObject);
        this.addShowMultibuyerListener(this.formObject);
    }
};

PlugPaymentMethodController.prototype.removeSavedCardsSelect = function (formObject) {
    var formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.removeSavedCardsSelect(formObject);
}

var timesRunObserver = 1;
PlugPaymentMethodController.prototype.addCreditCardListeners = function (formObject, cardNumberValidator) {
    if (!formObject) {
        return;
    }

    this.addCreditCardNumberListener(formObject, cardNumberValidator);
    this.addCreditCardInstallmentsListener(formObject);
    this.addCreditCardHolderNameListener(formObject);
    this.addSavedCreditCardsListener(formObject);
    this.removeSavedCards(formObject);

    if (timesRunObserver <= 1) {
        timesRunObserver++;
        this.addListenerUpdateAmount(cardNumberValidator);
    }
};

PlugPaymentMethodController.prototype.removeSavedCards = function (formObject) {
    if (checkoutConfig.payment[formObject.savedCardSelectUsed].enabled_saved_cards) {
        return;
    }

    var selectCard = document.querySelector(formObject.containerSelector).querySelector('.saved-card');
    if (selectCard == null) {
        return;
    }
    selectCard.remove();
};

PlugPaymentMethodController.prototype.addListenerUpdateAmount = function (cardNumberValidator) {
    var observerMutation = new MutationObserver(function (mutationsList, observer) {
        var initCreditCard = new PlugPaymentMethodController('creditcard', platFormConfig, cardNumberValidator);
        initCreditCard.init();
    });

    observerMutation.observe(document.getElementById('opc-sidebar'),{
        attributes: false,
        childList: true,
        subtree: true
    });
}

PlugPaymentMethodController.prototype.addInputAmountBalanceListener = function(formObject, id) {
    var paymentMethodController = this;
    formObject.inputAmount.on('change', function () {
        paymentMethodController.fillInstallments(formObject);
        var formId = paymentMethodController.model.getFormIdInverted(id);
        var form = paymentMethodController.formObject[formId];
        paymentMethodController.fillInstallments(form);
        setTimeout(function () {
            paymentMethodController.updateTotalByPaymentMethod(paymentMethodController, form.creditCardInstallments);
        }, 3000);
    });

    formObject.inputAmount.on('keyup', function(){
        element = jQuery(this);
        var orginalValue = platFormConfig.updateTotals.getTotals()().grand_total
        var orderAmount = (orginalValue).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        orderAmount = orderAmount.replace(/[^0-9]/g, '');
        orderAmount = Number(orderAmount);
        var value = element.val();
        value = value.replace(/[^0-9]/g, '');
        value = Number(value);
        if (value >= orderAmount) {
            value = orderAmount - 1;
        }

        if (isNaN(value) || value === 0) {
            value = 1;
        }
        var remaining = orderAmount - value;
        remaining = (remaining / 100).toFixed(2);
        value = (value / 100).toFixed(2);

        var formId = paymentMethodController.model.getFormIdInverted(id);
        var form = paymentMethodController.formObject[formId];

        form.inputAmount.val(remaining.toString().replace('.', paymentMethodController.platformConfig.currency.decimalSeparator));
        element.val(value.toString().replace('.', paymentMethodController.platformConfig.currency.decimalSeparator));
    });
}

PlugPaymentMethodController.prototype.addCreditCardHolderNameListener = function(formObject) {
    var paymentMethodController = this;
    formObject.creditCardHolderName.on('keyup', function () {
        var element = jQuery(this);
        paymentMethodController.clearNumbers(element);
    });
}

PlugPaymentMethodController.prototype.addCreditCardNumberListener = function(formObject, cardNumberValidator) {
    var paymentMethodController = this;
    formObject.creditCardNumber.unbind();
    formObject.creditCardNumber.on('keydown', function () {
        element = jQuery(this);
        paymentMethodController.limitCharacters(element, 19);
    });

    var binObj = new PlugBin();
    formObject.creditCardNumber.on('keyup', function () {
        var element = jQuery(this);
        paymentMethodController.clearLetters(element);
    });

    formObject.creditCardNumber.on('change', function () {
        var waitForFroogaloop;
        var element = jQuery(this);
        waitForFroogaloop = setTimeout(function() {
            paymentMethodController.setBin(binObj,  element, formObject, cardNumberValidator);
            clearTimeout(waitForFroogaloop);
        }, 300);
    }).bind(this);
};

PlugPaymentMethodController.prototype.twoCardsTotal = function (paymentMethod) {
    var card1 = paymentMethod.formObject[0].creditCardInstallments.selector;
    var card2 = paymentMethod.formObject[1].creditCardInstallments.selector;

    var totalCard1 = paymentMethod.formObject[0].inputAmount.val().replace(platformConfig.currency.decimalSeparator, ".");
    var totalCard2 = paymentMethod.formObject[1].inputAmount.val().replace(platformConfig.currency.decimalSeparator, ".");

    var interestTotalCard1 = jQuery(card1).find(":selected").attr("interest");
    var interestTotalCard2 = jQuery(card2).find(":selected").attr("interest");

    var sumTotal = (parseFloat(totalCard1) + parseFloat(totalCard2));
    var sumInterestTotal = (parseFloat(interestTotalCard1) + parseFloat(interestTotalCard2));

    sumTotal = (sumTotal + sumInterestTotal).toString();
    sumInterestTotal = sumInterestTotal.toString();
    return { sumTotal, sumInterestTotal };
}

PlugPaymentMethodController.prototype.updateTotalByPaymentMethod = function (paymentMethod, event) {
    var interest = jQuery(event).find(':selected').attr("interest");
    var grandTotal = jQuery(event).find(':selected').attr("total_with_tax");
    paymentMethod.updateTotal(
        interest,
        grandTotal,
        jQuery(event).attr('name')
    );
}

PlugPaymentMethodController.prototype.addCreditCardInstallmentsListener = function (formObject) {
    var paymentMethodController = this;
    formObject.creditCardInstallments.on('change', function () {
        var value = jQuery(this).val();
        if (value != "" && value != 'undefined') {
            paymentMethodController.updateTotalByPaymentMethod(paymentMethodController, this);
        }
    });
};

PlugPaymentMethodController.prototype.addSavedCreditCardsListener = function(formObject) {
    var paymentMethodController = this;
    var selector = formObject.savedCreditCardSelect.selector;
    var brand = jQuery(selector + ' option:selected').attr('brand');
    if (brand == undefined) {
        brand = formObject.creditCardBrand.val();
    }

    formObject.creditCardBrand.val(brand);
    formObject.savedCreditCardSelect.on('change', function() {
        var value = jQuery(this).val();
        var brand = jQuery(selector + ' option:selected').attr('brand');
        formObject.creditCardBrand.val(brand);
        if (value === 'new') {
            jQuery(formObject.containerSelector + ' .new').show();
            if (typeof formObject.multibuyer != 'undefined' &&
                typeof formObject.multibuyer.showMultibuyer != 'undefined'
            ) {
                formObject.multibuyer.showMultibuyer.parent().show();
            }
            return;
        }

        paymentMethodController.fillInstallments(formObject);
        jQuery(formObject.containerSelector + ' .new').hide();
        if (typeof formObject.multibuyer != 'undefined' &&
            typeof formObject.multibuyer.showMultibuyer != 'undefined'
        ) {
            formObject.multibuyer.showMultibuyer.parent().hide();
        }
    });
};

PlugPaymentMethodController.prototype.placeOrder = function (placeOrderObject) {
    var customerValidator = new PlugCustomerValidator(
        this.platformConfig.addresses.billingAddress
    );
    customerValidator.validate();
    var errors = customerValidator.getErrors();

    if (errors.length > 0) {
        for (id in errors) {
            this.model.addErrors(errors[id]);
        }
        return;
    }
    this.model.placeOrder(placeOrderObject);
};

PlugPaymentMethodController.prototype.updateTotal = function(interest, grandTotal, selectName) {
    var paymentMethodController = this;

    /**@fixme Move gettotals() to PlatformFormBiding */
    var total = paymentMethodController.platformConfig.updateTotals.getTotals()();
    interest = (parseInt((interest * 100).toFixed(2))) / 100;
    if (interest < 0) {
        interest = 0;
    }

    total.tax_amount = interest;
    total.base_tax_amount = interest;

    for (var i = 0, len = total.total_segments.length; i < len; i++) {
        if (total.total_segments[i].code === "grand_total") {
            grandTotal = parseInt((grandTotal * 100).toFixed(2));
            total.total_segments[i].value = grandTotal / 100;
            continue;
        }

        if (total.total_segments[i].code === "tax") {
            total.total_segments[i].value = interest;
        }
    }
    paymentMethodController.platformConfig.updateTotals.setTotals(total);
};

PlugPaymentMethodController.prototype.sumInterests = function(interest, selectName) {
    var paymentMethodController = this;
    var formObject = paymentMethodController.formObject;
    for (id in formObject) {
        if (id.length > 1 || formObject[id].creditCardInstallments == undefined) {
            continue;
        }
        var name = formObject[id].creditCardInstallments.attr('name');
        if (name == selectName) {
            continue;
        }

        var otherInterest = formObject[id].creditCardInstallments.find(':selected').attr('interest');
        if (isNaN(otherInterest)) {
            continue;
        }
        interest = parseFloat(otherInterest) + parseFloat(interest);
    }

    return interest;
}

PlugPaymentMethodController.prototype.removeInstallmentsSelect = function (formObject) {
    var formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.removeInstallmentsSelect(formObject);
}

PlugPaymentMethodController.prototype.showCvvCard = function (formObject) {
    var cvvElement = document.querySelector(formObject.containerSelector + " .cvv");
    if (cvvElement !== undefined) {
        cvvElement.style.display = "";
    }
}

PlugPaymentMethodController.prototype.fillInstallments = function (form) {
    var _self = this;
    if (form.creditCardBrand === undefined) {
        return;
    }
    var installmentSelected = form.creditCardInstallments.val();
    formHandler = new PlugFormHandler();
    var selectedBrand = form.creditCardBrand.val();
    var amount = form.inputAmount.val();
    if (typeof selectedBrand == "undefined") {
        selectedBrand = 'default';
    }

    if (typeof amount == "undefined") {
        amount = 0;
    }

    form.creditCardInstallments.prop('disabled', true);
    var installmentsUrl =
        this.platformConfig.urls.installments + '/' +
        selectedBrand + '/' +
        amount;

    jQuery.ajax({
        url: installmentsUrl,
        method: 'GET',
        cache: true,
    }).done(function(data) {
        formHandler = new PlugFormHandler();
        formHandler.updateInstallmentSelect(data, form.creditCardInstallments, installmentSelected);
        form.creditCardInstallments.prop('disabled', false);
        formHandler.init(form);
        formHandler.switchBrand(selectedBrand);
    });
};

PlugPaymentMethodController.prototype.fillBrandList = function (formObject, method) {
    if (method === undefined) {
        method = 'plug_creditcard';
    }
    var formHandler = new PlugFormHandler();
    formHandler.fillBrandList(
        this.platformConfig.avaliableBrands[method],
        formObject
    );
};

PlugPaymentMethodController.prototype.fillCardAmount = function (formObject, count, card = null) {
    var orderAmount = platFormConfig.updateTotals.getTotals()().grand_total / count;
    var amount = orderAmount.toFixed(this.platformConfig.currency.precision);
    var separator = ".";
    amount = amount.replace(separator, this.platformConfig.currency.decimalSeparator);
    if (card === 1) {
        var orderAmountOriginal =  amount.replace(this.platformConfig.currency.decimalSeparator, ".");
        var amountBalance = (platFormConfig.updateTotals.getTotals()().grand_total - orderAmountOriginal).toFixed(2);
        formObject.inputAmount.val(amountBalance.replace(".", this.platformConfig.currency.decimalSeparator));
        return;
    }
    formObject.inputAmount.val(amount);
};

PlugPaymentMethodController.prototype.setBin = function (binObj, creditCardNumberElement, formObject, cardNumberValidator) {
    var bin = binObj, result;
    var cardNumber = bin.formatNumber(creditCardNumberElement.val());
    if (cardNumber.length < 4) {
        return;
    }

    var isNewBrand = bin.validate(cardNumber);
    result = cardNumberValidator(cardNumber);
    if (!result.isPotentiallyValid && !result.isValid) {
        return false;
    }

    bin.init(cardNumber, result.card);

    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.switchBrand(bin.selectedBrand);
    if (isNewBrand) {
        this.fillInstallments(formObject);
    }
    return;
};

PlugPaymentMethodController.prototype.limitCharacters = function (element, limit) {
    var val = element.val();
    if(val != "" && val.length > limit) {
        element.val(val.substring(0, limit));
    }
};

PlugPaymentMethodController.prototype.clearLetters = function (element) {
    var val = element.val();
    var newVal = val.replace(/[^0-9]+/g, '');
    element.val(newVal);
};

PlugPaymentMethodController.prototype.clearNumbers = function (element) {
    var val = element.val();
    var newVal = val.replace(/[0-9.-]+/g, '');
    element.val(newVal);
};

PlugPaymentMethodController.prototype.hideCardAmount = function (formObject) {
    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.hideInputAmount(formObject);
};

PlugPaymentMethodController.prototype.fillFormText = function (formObject, method = null) {
    formText = this.platformConfig.text;
    var creditCardExpYear = formObject.creditCardExpYear.val();
    var creditCardExpMonth = formObject.creditCardExpMonth.val()
    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.fillExpirationYearSelect(formText, method, creditCardExpYear);
    formHandler.fillExpirationMonthSelect(formText, method, creditCardExpMonth);
};

PlugPaymentMethodController.prototype.fillSavedCreditCardsSelect = function (formObject) {
    platformConfig = this.platformConfig;
    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.fillSavedCreditCardsSelect(platformConfig, formObject);
    if (typeof formObject.savedCreditCardSelect.selector != 'undefined') {
        selector = formObject.savedCreditCardSelect.selector;
        var brand = jQuery(selector + ' option:selected').attr('brand');
        if (brand === undefined) {
            brand = formObject.creditCardBrand.val();
        }
        formObject.creditCardBrand.val(brand);

        if (typeof formObject.multibuyer != 'undefined' &&
            typeof formObject.multibuyer.showMultibuyer != 'undefined' &&
            formObject.savedCreditCardSelect[0].length > 0
        ) {
            formObject.multibuyer.showMultibuyer.parent().hide();
        }
    }
};

PlugPaymentMethodController.prototype.fillMultibuyerStateSelect = function (formObject) {
    platformConfig = this.platformConfig;
    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.fillMultibuyerStateSelect(platformConfig, formObject);
};

PlugPaymentMethodController.prototype.removeMultibuyerForm = function (formObject) {
    formHandler = new PlugFormHandler();
    formHandler.init(formObject);
    formHandler.removeMultibuyerForm(formObject);
};

PlugPaymentMethodController.prototype.addShowMultibuyerListener = function(formObject) {
    jQuery(formObject.multibuyer.showMultibuyer.selector).on('click', function () {
        formHandler.init(formObject);
        formHandler.toggleMultibuyer(formObject);
    });
}

PlugPaymentMethodController.prototype.isTotalOnAmountInputs = function(formObject, platformConfig) {
    var orderTotal = platformConfig.updateTotals.getTotals()().grand_total;
    var card1 = formObject[0].inputAmount.val().replace(platformConfig.currency.decimalSeparator, ".");
    var card2 = formObject[1].inputAmount.val().replace(platformConfig.currency.decimalSeparator, ".");
    var totalInputs = (parseFloat(card1) + parseFloat(card2));
    return orderTotal === totalInputs;
}
