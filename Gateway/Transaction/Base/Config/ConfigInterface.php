<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config;

interface ConfigInterface
{
    const PATH_CLIENT_ID_TEST     = 'plug/global/client_id_test';
    const PATH_SECRET_KEY_TEST     = 'plug/global/secret_key_test';
    const PATH_MERCHANT_ID_TEST      = 'plug/global/merchant_key_test';
    const PATH_WEBHOOK_KEY_TEST      = 'plug/global/webhook_key_test';
    const PATH_CLIENT_ID          = 'plug/global/client_id';
    const PATH_SECRET_KEY          = 'plug/global/secret_key';
    const PATH_MERCHANT_ID           = 'plug/global/merchant_key';
    const PATH_WEBHOOK_KEY           = 'plug/global/webhook_key';
    const PATH_TEST_MODE           = 'plug/global/test_mode';
    const PATH_SEND_EMAIL          = 'plug/global/sendmail';
    const PATH_CUSTOMER_STREET     = 'payment/plug_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER     = 'payment/plug_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT = 'payment/plug_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT   = 'payment/plug_customer_address/district_attribute';
    const PATH_SAND_BOX_URL        = 'plug/general/sandbox_url';
    const PATH_PRODUCTION_URL      = 'plug/general/production_url';

    /**
     * @return string
     */
    public function getMerchantId();

    /**
     * @return string
     */
    public function getSecretKey();

    /**
     * @return string
     */
    public function getClientId();

    /**
     * @return string
     */
    public function getWebhookKey();

    /**
     * @return string
     */
    public function getWebhookKeyPath();

    /**
     * @return string
     */
    public function getTestMode();

    /**
     * @return string
     */
    public function getBaseUrl();

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
     * @return bool
     */
    public function isSendEmail();
}
