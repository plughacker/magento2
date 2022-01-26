<?php
namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\HTTP\Client\Curl;
use PlugHacker\PlugPagamentos\Api\CreditCardManagementInterface;
use PlugHacker\PlugPagamentos\Api\Data\CreditCardInterface;
use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config\ConfigInterface as BaseConfig;

class CreditCardManagement implements CreditCardManagementInterface
{
    /**
     * @var BaseConfig
     */
    protected $_baseConfig;
    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @param BaseConfig $baseConfig
     * @param Curl $curl
     */
    public function __construct(
        BaseConfig $baseConfig,
        Curl $curl
    ) {
        $this->_baseConfig = $baseConfig;
        $this->_curl = $curl;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreditCardToken(CreditCardInterface $creditCard)
    {
        $apiBaseUrl = $this->_baseConfig->getBaseUrl();
        $clientId = $this->_baseConfig->getClientId();
        $secretKey = $this->_baseConfig->getSecretKey();

        $this->_curl->addHeader("Content-Type", "application/json");
        $this->_curl->addHeader("X-Client-Id", $clientId);
        $this->_curl->addHeader("X-Api-Key", $secretKey);

        $_creditCard = [];
        $_creditCard['cardHolderName'] = $creditCard->getCardHolderName();
        $_creditCard['cardNumber'] = $creditCard->getCardNumber();
        $_creditCard['cardExpirationDate'] = '12/2026'; //$creditCard->getCardExpirationDate();
        $_creditCard['cardCvv'] = $creditCard->getCardCvv();
        $params = json_encode($_creditCard);
        $this->_curl->post($apiBaseUrl . '/v1/tokens', $params);
        echo $this->_curl->getBody(); die;
        return json_decode($this->_curl->getBody(), true);
    }
}
