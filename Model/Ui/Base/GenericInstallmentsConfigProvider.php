<?php
namespace PlugHacker\PlugPagamentos\Model\Ui\Base;

use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use PlugHacker\PlugPagamentos\Model\Installments\Config\ConfigInterface;
use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config\ConfigInterface as BaseConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Store\Model\StoreManagerInterface;

abstract class GenericInstallmentsConfigProvider implements ConfigProviderInterface
{
    const CODE = null;

    protected $installments = [];
    protected $installmentsBuilder;
    protected $installmentsConfig;
    protected $config;
    protected $_assetRepo;
    protected $baseConfig;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        ConfigInterface $config,
        BaseConfig $baseConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->baseConfig = $baseConfig;
        $this->storageManager = $storeManager;
        $this->setConfig($config);
    }

    public function getConfig()
    {
        return [
            'payment' => [
                'plugccform' => [
                    'base_url' => $this->storageManager->getStore()->getBaseUrl(),
                    'api_url' => $this->baseConfig->getBaseUrl(),
                    'installments' => [
                        'active' => [$this::CODE => $this->_getConfig()->isActive()],
                        'value' => 0,
                    ],
                    'pk_token' => $this->baseConfig->getClientId(),
                    'icons' => [
                        'Visa' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Visa.png")
                        ],
                        'Elo' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Elo.png")
                        ],
                        'Discover' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Discover.png")
                        ],
                        'Diners' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Diners.png")
                        ],
                        'Credz' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Credz.png")
                        ],
                        'Hipercard' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Hipercard.png")
                        ],
                        'HiperCard' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Hipercard.png")
                        ],
                        'Mastercard' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Mastercard.png")
                        ],
                        'Sodexo' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Sodexo.png")
                        ],
                        'SodexoAlimentacao' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoAlimentacao.png")
                        ],
                        'SodexoCombustivel' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoCombustivel.png")
                        ],
                        'SodexoCultura' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoCultura.png")
                        ],
                        'SodexoGift' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoGift.png")
                        ],
                        'SodexoPremium' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoPremium.png")
                        ],
                        'SodexoRefeicao' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/SodexoRefeicao.png")
                        ],
                        'Cabal' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Cabal.png")
                        ],
                        'Aura' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Aura.png")
                        ],
                        'Amex' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Amex.png")
                        ],
                        'Alelo' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Alelo.png")
                        ],
                        'VR' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/VR.png")
                        ],
                        'Banese' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/Banese.png")
                        ],
                        'JCB' => [
                            'height' => 30,
                            'width' => 46,
                            'url' => $this->_assetRepo->getUrl("PlugHacker_PlugPagamentos::images/cc/JCB2.png")
                        ],
                    ],
                ]
            ],
            'is_multi_buyer_enabled' => $this->_getConfig()->getMultiBuyerActive(),
            'region_states' => $this->getRegionStates()
        ];
    }


    /**
     * @param ConfigInterface $config
     * @return $this
     */
    protected function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return ConfigInterface
     */
    protected function _getConfig()
    {
        return $this->config;
    }

    protected function getRegionStates()
    {
        /** @fixme Get current country **/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $states = $objectManager
            ->create('Magento\Directory\Model\RegionFactory')
            ->create()->getCollection()->addFieldToFilter('country_id','BR');

        return $states->getData();
    }
}
