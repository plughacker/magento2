<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\Billet\Config;

interface ConfigInterface
{
    const PATH_INSTRUCTIONS     = 'payment/plug_billet/instructions';
    const PATH_TEXT             = 'payment/plug_billet/text';
    const PATH_EXPIRATION_DAYS  = 'payment/plug_billet/expiration_days';
    const PATH_CUSTOMER_STREET              = 'payment/plug_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER              = 'payment/plug_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT          = 'payment/plug_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT            = 'payment/plug_customer_address/district_attribute';
    const PATH_TITLE                        = 'payment/plug_billet/title';

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @return string
     */
    public function getText();

    /**
     * @return string
     */
    public function getExpirationDays();

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
