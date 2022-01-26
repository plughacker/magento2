var PlugInstallments = function () {
}

PlugInstallments.prototype.init = function () {
};

PlugInstallments.prototype.addOptions = function (element, installments) {
    if (installments !== undefined) {
        jQuery(element).find('option').remove();
        installments.forEach(function (value) {
            var opt = new Option(value.label, value.id);
            jQuery(opt).attr("interest", value.interest);
            jQuery(element).append(opt);
        });
    }
}
