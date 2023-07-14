<?php
namespace PlugHacker\PlugPagamentos\Model;

use \Magento\Store\Model\ScopeInterface;

/**
 * Class PlugConfigProvider
 *
 * @package PlugHacker\PlugPagamentos\Model
 */
class PlugConfigProvider
{
    /**
     * Contains if the module is active or not
     */
    const XML_PATH_ACTIVE            = 'plug/global/active';
    const PATH_CUSTOMER_STREET      = 'payment/plug_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER      = 'payment/plug_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT  = 'payment/plug_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT    = 'payment/plug_customer_address/district_attribute';
    const XML_PATH_SOFTDESCRIPTION  = 'payment/plug_creditcard/soft_description';

    /**
     * Contains scope config of Magento
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Contains the configurations
     *
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $config;

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
    }

    /**
     * Returns the soft_description configuration
     *
     * @return string
     */
    public function getSoftDescription()
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_SOFTDESCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    public function validateSoftDescription()
    {
        $softDescription = $this->getSoftDescription();

        if(strlen($softDescription) > 22){
            $newResult = substr($softDescription, 0, 21);
            $this->config->saveConfig(self::XML_PATH_SOFTDESCRIPTION, $newResult, 'default', 0);

            return false;
        }

        return true;
    }
    /**
     * Returns the soft_description configuration
     *
     * @return string
     */
    public function getModuleStatus()
    {
        return
            $this->scopeConfig->getValue(
                self::XML_PATH_ACTIVE,
                ScopeInterface::SCOPE_STORE
            );
    }

    public function getCustomerAddressConfiguration()
    {
        $street = $this->scopeConfig->getValue(
            self::PATH_CUSTOMER_STREET,
            ScopeInterface::SCOPE_STORE
        );

        $number = $this->scopeConfig->getValue(
            self::PATH_CUSTOMER_NUMBER,
            ScopeInterface::SCOPE_STORE
        );

        $district = $this->scopeConfig->getValue(
            self::PATH_CUSTOMER_DISTRICT,
            ScopeInterface::SCOPE_STORE
        );

        return [
            'street' => $street,
            'number' => $number,
            'district' => $district
        ];
    }

}
