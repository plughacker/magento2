<?php
namespace PlugHacker\PlugPagamentos\Helper;


interface CustomerAddressInterface
{
    const PATH_STREET_ATTRIBUTE         = 'plug/plug_customer_address/street_attribute';
    const PATH_NUMBER_ATTRIBUTE         = 'plug/plug_customer_address/number_attribute';
    const PATH_COMPLEMENT_ATTRIBUTE     = 'plug/plug_customer_address/complement_attribute';
    const PATH_DISTRICT_ATTRIBUTE       = 'plug/plug_customer_address/district_attribute';

    /**
     * @return string
     */
    public function getStreetAttribute();

    /**
     * @return string
     */
    public function getNumberAttribute();

    /**
     * @return string
     */
    public function getComplementAttribute();

    /**
     * @return string
     */
    public function getDistrictAttribute();
}
