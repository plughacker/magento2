<?php

namespace PlugHacker\PlugPagamentos\Model\Config\Backend;

use Magento\Config\Model\ResourceModel\Config as ConfigWriter;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PlugHacker\PlugCore\Kernel\Services\APIService;
use PlugHacker\PlugCore\Payment\Aggregates\Webhook;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Model\PlugConfigProvider;
use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config\Config as TransactionBaseConfig;

class WebhookValidator extends ConfigValue
{
    protected $_isChanged = false;

    /**
     * @var PlugConfigProvider
     */
    protected $_plugConfigProvider;

    /**
     * @var TransactionBaseConfig
     */
    protected $_transactionBaseConfig;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * Contains the configurations
     *
     * @var ConfigInterface
     */
    protected $_configInterface;

    /**
     * @var ConfigWriter
     */
    private $_configWriter;

    /**
     * Application config
     *
     * @var ReinitableConfigInterface
     */
    private $_appConfig;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @param PlugConfigProvider $plugConfigProvider
     * @param TransactionBaseConfig $transactionBaseConfig
     * @param UrlInterface $urlInterface
     * @param ConfigInterface $configInterface
     * @param ConfigWriter $configWriter
     * @param ReinitableConfigInterface $appConfig
     * @param RequestInterface $request
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        PlugConfigProvider $plugConfigProvider,
        TransactionBaseConfig $transactionBaseConfig,
        UrlInterface $urlInterface,
        ConfigInterface $configInterface,
        ConfigWriter $configWriter,
        ReinitableConfigInterface $appConfig,
        RequestInterface $request,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_plugConfigProvider = $plugConfigProvider;
        $this->_transactionBaseConfig = $transactionBaseConfig;
        $this->_urlInterface = $urlInterface;
        $this->_configInterface = $configInterface;
        $this->_configWriter = $configWriter;
        $this->_appConfig = $appConfig;
        $this->_request = $request;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return ConfigValue|void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $params = $this->_request->getParams();

        $this->_isChanged = false;
        $value = $this->getValue();
        /*$clientId = $this->_transactionBaseConfig->getClientId();
        $merchantId = $this->_transactionBaseConfig->getMerchantId();
        $secretKey = $this->_transactionBaseConfig->getSecretKey();
        $webhookKey = $this->_transactionBaseConfig->getWebhookKey();*/
        $webhookKeyPath = $this->_transactionBaseConfig->getWebhookKeyPath();

        $clientId = $merchantId =  $secretKey =  $webhookKey = $isTestMode = '';
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['test_mode']['value'])
            && $params['groups']['plug']['groups']['plug_global']['fields']['test_mode']['value'] == 1) {
            $isTestMode = '_test';
        }
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['client_id'.$isTestMode]['value'])) {
            $clientId = $params['groups']['plug']['groups']['plug_global']['fields']['client_id'.$isTestMode]['value'];
        }
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['merchant_key'.$isTestMode]['value'])) {
            $merchantId = $params['groups']['plug']['groups']['plug_global']['fields']['merchant_key'.$isTestMode]['value'];
        }
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['secret_key'.$isTestMode]['value'])) {
            $secretKey = $params['groups']['plug']['groups']['plug_global']['fields']['secret_key'.$isTestMode]['value'];
        }
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['secret_key'.$isTestMode]['value'])) {
            $secretKey = $params['groups']['plug']['groups']['plug_global']['fields']['secret_key'.$isTestMode]['value'];
        }
        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['webhook_key'.$isTestMode]['value'])) {
            $webhookKey = $params['groups']['plug']['groups']['plug_global']['fields']['webhook_key'.$isTestMode]['value'];
        }

        $webhookKeyPath = $webhookKeyPath . $isTestMode;

        if ($value) {
            if (!$clientId) {
                throw new LocalizedException(
                    __("The Client Id was not set on the module basic configuration")
                );
            }

            if (!$merchantId) {
                throw new LocalizedException(
                    __("The Merchant Id was not set on the module basic configuration")
                );
            }

            if (!$secretKey) {
                throw new LocalizedException(
                    __("The Api Key was not set on the module basic configuration")
                );
            }
        }

        if (isset($params['groups']['plug']['groups']['plug_global']['fields']['webhook_key'.$isTestMode]['value'])) {
            $params['groups']['plug']['groups']['plug_global']['fields']['webhook_key'.$isTestMode]['value'] = 'abcc';
            $this->_request->setParams($params);
        }

        if ($clientId && $merchantId && $secretKey) {
            Magento2CoreSetup::bootstrap();
            $status = $value == 1;
            $endpoint = $this->_urlInterface->getBaseUrl() . ltrim('/rest/V1/plug/webhook', '/');
            $webhookPost = new Webhook();
            $webhookPost->setEvent('*');
            $webhookPost->setEndpoint($endpoint);
            $webhookPost->setVersion(1);
            $webhookPost->setStatus($status);

            $_isTestMode = ($isTestMode == '_test') ? true : null;
            //Send through the APIService to plug
            $apiService = new APIService($clientId, $merchantId, $secretKey, $_isTestMode);
            try {
                $response = $apiService->createWebhook($webhookPost);
                if (!isset($response['id'])) {
                    throw new LocalizedException(
                        __("The webhook request is invalid or the client id is inactive")
                    );
                }

                $this->_isChanged = true;
                $this->_appConfig->reinit();
                $this->_configWriter->saveConfig($webhookKeyPath, (string)$response['id'], 'default', 0);
            } catch (\Exception $e) {
                if ($e->getCode() != 409) {
                    throw new LocalizedException(
                        __("The webhook request already configured or something went wrong.")
                    );
                }
            }
        }
    }

    /**
     * Processing object after save data
     *
     * {@inheritdoc}. In addition, it sets status 'invalidate' for config caches
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->_isChanged) {
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
