var PlugBin = function () {
    binValue = '';
    brand = '';
    checkedBins = [0];
    selectedBrand = '';
};

PlugBin.prototype.init = function (newValue, data) {
    newValue = this.formatNumber(newValue);
    if (typeof this.checkedBins != 'undefined' && typeof this.checkedBins[newValue] != 'undefined') {
        this.binValue = newValue;
        this.selectedBrand = this.checkedBins[newValue];
        return;
    }

    if (this.validate(newValue)) {
        this.binValue = newValue;
        this.saveBinInformation(data);
        return;
    }

    this.selectedBrand = '';
};

PlugBin.prototype.formatNumber = function (number) {
    var newValue = String(number);
    return newValue.slice(0, 6);
};

PlugBin.prototype.validate = function (newValue) {
    if (newValue.length == 6 && this.binValue != newValue) {
        return true;
    }
    return false;
};

PlugBin.prototype.saveBinInformation = function (data) {
    if (typeof this.checkedBins == 'undefined') {
        this.checkedBins = [];
    }

    this.checkedBins[this.binValue] = data.type;
    this.selectedBrand = data.type;
};
