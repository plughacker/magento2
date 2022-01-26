<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\CreditCard\Config;

interface ConfigInterface
{
    const PATH_ACTIVE                       = 'payment/plug_creditcard/active';
    const PATH_ENABLED_SAVED_CARDS          = 'payment/plug_creditcard/enabled_saved_cards';
    const PATH_PAYMENT_ACTION               = 'payment/plug_creditcard/payment_action';
    const PATH_ANTIFRAUD_ACTIVE             = 'payment/plug_creditcard/antifraud_active';
    const PATH_ANTIFRAUD_MIN_AMOUNT         = 'payment/plug_creditcard/antifraud_min_amount';
    const PATH_SOFT_DESCRIPTION             = 'payment/plug_creditcard/soft_description';
    const PATH_CUSTOMER_STREET              = 'payment/plug_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER              = 'payment/plug_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT          = 'payment/plug_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT            = 'payment/plug_customer_address/district_attribute';
    const PATH_TITLE                        = 'payment/plug_creditcard/title';

    /**
     * @return bool
     */
    public function getActive();

    /**
     * @return bool
     */
    public function getEnabledSavedCards();

    /**
     * @return string
     */
    public function getPaymentAction();

    /**
     * @return bool
     */
    public function getAntifraudActive();

    /**
     * @return string
     */
    public function getAntifraudMinAmount();

    /**
     * @return string
     */
    public function getSoftDescription();

    /**
     * @return string
     */
    public function getCustomerStreetAttribute();

    /**
     * @return string
     */
    public function getCustomerAddressNumber();

    /**
     * @return string
     */
    public function getCustomerAddressComplement();

    /**
     * @return string
     */
    public function getCustomerAddressDistrict();

    /**
     * @return string
     */
    public function getTitle();

}
