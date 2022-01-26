var config = {
    map: {
        '*': {
            jquerymask: 'PlugHacker_PlugPagamentos/js/plugins/jquery.mask.min'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'PlugHacker_PlugPagamentos/js/mixin/billing-address-mixin': true
            }
        }
    }
};
