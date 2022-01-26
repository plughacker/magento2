<?php
namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractModuleCoreSetup;
use PlugHacker\PlugCore\Kernel\Repositories\ConfigurationRepository;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Model\PlugConfigProvider;

class DataValidateAdmin implements ObserverInterface
{
    /**
     * Contains the config provider for Plug
     *
     * @var \PlugHacker\PlugPagamentos\Model\PlugConfigProvider
     */
    protected $configProviderPlug;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cacheFrontendPool;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @param PlugConfigProvider $configProviderPlug
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ResponseFactory $responseFactory
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param RequestInterface $request
     */
    public function __construct(
        PlugConfigProvider $configProviderPlug,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ResponseFactory $responseFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        RequestInterface $request
    ) {
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->responseFactory = $responseFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->configProviderPlug = $configProviderPlug;
        $this->_request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->updateModuleConfiguration();

        if (!$this->moduleIsEnable()) {
            return $this;
        }

        $this->validateConfigMagento();

/*
        $scopeData = $this->getScopeData($observer);
        $this->cofigureWebhook($scopeData);*/

        return $this;
    }

    protected function initializeModule()
    {
        Magento2CoreSetup::bootstrap();
    }

    private function updateModuleConfiguration()
    {
        $this->initializeModule();

        $moduleConfig = AbstractModuleCoreSetup::getModuleConfiguration();
        $configRepository = new ConfigurationRepository();

        $outdatedConfiguration =
            $this->getOutDatedConfiguration(
                $moduleConfig,
                $configRepository
            );

        if ($outdatedConfiguration !== null) {
            $moduleConfig->setId($outdatedConfiguration->getId());
        }

        $configRepository->save($moduleConfig);

        AbstractModuleCoreSetup::setModuleConfiguration($moduleConfig);
    }


    public function moduleIsEnable()
    {
        return $this->configProviderPlug->getModuleStatus();
    }

    protected function validateConfigMagento()
    {
        $disableModule = false;
        $disableMessage = [];
        $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');
        if(!$this->configProviderPlug->validateSoftDescription()){
            $disableModule = true;
            $disableMessage[] = __("Error to save Plug Soft Description Credit Card, size too big max 22 character." ,
                $url
            );
        }

        if ($disableModule) {
            $this->disableModule($disableMessage, $url);
        }

        return $this;
    }

    protected function cofigureWebhook($scopeData)
    {
        $params = $this->_request->getParams();

        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Params');
        $logger->info(print_r($params['groups'], true));
        $logger->info('$scopeData');
        $logger->info(print_r($scopeData, true));

    }

    protected function disableModule($disableMessage, $url)
    {
        foreach ($disableMessage as $message) {
            $this->messageManager->addError($message);
        }

        $this->cleanCache();

        $this->responseFactory->create()
                ->setRedirect($url)
                ->sendResponse();
        exit(0);
    }

    protected function cleanCache()
    {
        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');

        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }

        return $this;
    }

    protected function getOutDatedConfiguration($moduleConfig, $configRepository)
    {
        $storeId = Magento2CoreSetup::getCurrentStoreId();
        $moduleConfig->setStoreId($storeId);

        return $configRepository->findByStore($storeId);
    }

    protected function getScopeData($observer)
    {
        $scopeData = [];

        $scopeData['scope']    = 'default';
        $scopeData['scope_id'] = null;

        $website = $observer->getWebsite();
        $store   = $observer->getStore();

        if ($website) {
            $scopeData['scope']    = ScopeInterface::SCOPE_WEBSITES;
            $scopeData['scope_id'] = $website;
        }

        if ($store) {
            $scopeData['scope']    = ScopeInterface::SCOPE_STORES;
            $scopeData['scope_id'] = $store;
        }

        return $scopeData;
    }
}
