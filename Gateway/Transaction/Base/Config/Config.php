<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config;

class Config extends AbstractConfig implements ConfigInterface
{
    /**
     * @return string
     */
    public function getMerchantId()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_MERCHANT_ID_TEST);
        }

        return $this->getConfig(static::PATH_MERCHANT_ID);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_SECRET_KEY_TEST);
        }

        return $this->getConfig(static::PATH_SECRET_KEY);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_CLIENT_ID_TEST);
        }

        return $this->getConfig(static::PATH_CLIENT_ID);
    }

    /**
     * @return string
     */
    public function getWebhookKey()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_WEBHOOK_KEY_TEST);
        }

        return $this->getConfig(static::PATH_WEBHOOK_KEY);
    }

    /**
     * @return string
     */
    public function getWebhookKeyPath()
    {
        /*if ($this->getTestMode()) {
            return static::PATH_WEBHOOK_KEY_TEST;
        }*/

        return static::PATH_WEBHOOK_KEY;
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        return $this->getConfig(static::PATH_TEST_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        if ($this->getConfig(static::PATH_TEST_MODE)) {
            return $this->getConfig(static::PATH_SAND_BOX_URL);
        }

        return $this->getConfig(static::PATH_PRODUCTION_URL);
    }

    /**
     * @return string
     */
    public function getCustomerStreetAttribute()
    {
        return $this->getConfig(static::PATH_CUSTOMER_STREET);
    }

    /**
     * @return string
     */
    public function getCustomerAddressNumber()
    {
        return $this->getConfig(static::PATH_CUSTOMER_NUMBER);
    }

    /**
     * @return string
     */
    public function getCustomerAddressComplement()
    {
        return $this->getConfig(static::PATH_CUSTOMER_COMPLEMENT);
    }

    /**
     * @return string
     */
    public function getCustomerAddressDistrict()
    {
        return $this->getConfig(static::PATH_CUSTOMER_DISTRICT);
    }

    /**
     * @return bool
     */
    public function isSendEmail()
    {
        $sendEmail = $this->getConfig(static::PATH_SEND_EMAIL);

        if ($sendEmail == '1') {
            return true;
        }

        return false;
    }
}
