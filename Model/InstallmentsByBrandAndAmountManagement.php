<?php
namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\Api\SimpleBuilderInterface;
use PlugHacker\PlugCore\Kernel\Services\MoneyService;
use PlugHacker\PlugCore\Kernel\ValueObjects\CardBrand;
use PlugHacker\PlugPagamentos\Api\InstallmentsByBrandAndAmountManagementInterface;
use Magento\Checkout\Model\Session;
use PlugHacker\PlugPagamentos\Helper\RecurrenceProductHelper;
use PlugHacker\PlugPagamentos\Model\Installments\Config\ConfigByBrand as Config;

class InstallmentsByBrandAndAmountManagement
    extends AbstractInstallmentManagement
    implements InstallmentsByBrandAndAmountManagementInterface
{
    protected $builder;
    protected $session;
    protected $cardBrand;
    /**
     * @var RecurrenceProductHelper
     */
    protected $recurrenceProductHelper;

    /**
     * @param SimpleBuilderInterface $builder
     */
    public function __construct(
        SimpleBuilderInterface $builder,
        Session $session,
        Config $config,
        RecurrenceProductHelper $recurrenceProductHelper
    )
    {
        $this->setBuilder($builder);
        $this->setSession($session);
        $this->setConfig($config);
        $this->recurrenceProductHelper = $recurrenceProductHelper;

        parent::__construct();
    }

    /**
     * @param mixed $brand
     * @param mixed $amount
     * @return mixed
     */
    public function getInstallmentsByBrandAndAmount($brand = null, $amount = null)
    {
        $baseBrand = 'nobrand';
        if (
            strlen($brand) > 0 &&
            $brand !== "null" &&
            method_exists(CardBrand::class, $brand)
        ) {
            $baseBrand = strtolower($brand);
        }

        $quote = $this->builder->getSession()->getQuote();

        $baseAmount = $quote->getGrandTotal();
        if ($amount !== null) {
            $baseAmount = $amount;
        }

        $moneyService = new MoneyService();

        $baseAmount = str_replace(
            [",", "."],
            "",
            $baseAmount
        );

        $baseAmount = $moneyService->centsToFloat($baseAmount);

        return $this->getCoreInstallments(
            null,
            CardBrand::$baseBrand(),
            $baseAmount
        );
    }

    /**
     * @param $brand
     * @return string
     */
    protected function formatCardBrand($brand){

        $cardBrand = '_' . strtolower($brand);

        return $cardBrand;

    }

    /**
     * @param SimpleBuilderInterface $builder
     * @return $this
     */
    protected function setBuilder(SimpleBuilderInterface $builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @return SimpleBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     * @return $this
     */
    protected function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return $this
     */
    protected function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

}
