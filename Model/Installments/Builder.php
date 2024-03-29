<?php
namespace PlugHacker\PlugPagamentos\Model\Installments;

use Magento\Framework\Api\AbstractSimpleObjectBuilder;
use Magento\Framework\Api\ObjectFactory;
use PlugHacker\PlugPagamentos\Api\Data\InstallmentInterface;
use PlugHacker\PlugPagamentos\Api\Data\InstallmentInterfaceFactory;
use PlugHacker\PlugPagamentos\Model\Installments\Config\ConfigInterface;
use Magento\Checkout\Model\Session;

class Builder extends AbstractSimpleObjectBuilder
{
    protected $config;
    protected $installmentFactory;
    protected $session;
    protected $grandTotal;
    protected $installmentsNumber;

    /**
     * @param InstallmentInterfaceFactory $installmentFactory
     * @param ConfigInterface $config
     * @param Session $session
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        InstallmentInterfaceFactory $installmentFactory,
        ConfigInterface $config,
        Session $session,
        ObjectFactory $objectFactory
    )
    {
        parent::__construct($objectFactory);
        $this->setInstallmentFactory($installmentFactory);
        $this->setConfig($config);
        $this->setSession($session);
    }

    /**
     * @return $this
     */
    public function create()
    {
        $installmentItems = $this->getInstallmentsNumber();

        for ($i = 1; $i < $installmentItems; $i++) {
            if (!$this->canProcessInstallment($i)) {
                break;
            }
            $this->addInstallment($i);
        }

        return $this;
    }

    /**
     * @param int $qty
     * @return $this
     */
    protected function addInstallment($qty)
    {
        $installmentAmount = $this->getGrandTotal() / $qty;
        $interest = false;
        $interestLabel = __('without interest');
        $installment = $this->getNewInstallmentInstance();
        $interestRateTotalSend = 0;

        if ($this->getConfig()->isInterestByIssuer() && ($qty > $this->getConfig()->getinstallmentsMaxWithoutInterest())) {
            $interestRate = $this->calcInterestRate($qty);
            $installmentAmount = $this->calcPriceWithInterest($qty, $interestRate);
            $interest = true;
            $interestRateTotal = $interestRate * 100;
            $labelInterestRate = ' ' . $interestRateTotal . '% a.m. ';
            $interestRateTotalSend = ($this->calcPriceWithInterestNoFormated($qty, $interestRate) * $qty) - $this->getGrandTotal();
            $interestLabel = __('with interest') . $labelInterestRate;
        }

        $grandTotal = $installmentAmount * $qty;


        $installment->setQty($qty);
        $installment->setPrice($installmentAmount);
        $installment->setHasInterest($interest);
        $installment->setGrandTotal($grandTotal);
        $installment->setInterest($interestRateTotalSend);
        $installment->setLabel($installment->getQty() . 'x ' . $installment->getPrice(true, false) . ' ' . $interestLabel . ' (Total ' .$installment->getGrandTotal(true, false) . ') ' );

        $this->data[] = $installment;
        return $this;
    }

    /**
     * @param int $qty
     * @return string
     */
    protected function calcPriceWithInterest($qty, $interestRate)
    {
        $price = ( $this->getGrandTotal() * (1 + $interestRate) ) / $qty ;

        return sprintf("%01.2f", $price);
    }

    /**
     * @param int $qty
     * @return string
     */
    protected function calcPriceWithInterestNoFormated($qty, $interestRate)
    {
        $price = ( $this->getGrandTotal() * (1 + $interestRate) ) / $qty ;

        return $price;
    }

    /**
     * @param int $qty
     * @return int
     */
    protected function calcInterestRate($qty)
    {
        $interestRate = $this->getConfig()->getInterestRate();
        $installmentsMaxWithoutInterest = $this->getConfig()->getinstallmentsMaxWithoutInterest();
        $diff = $qty - $installmentsMaxWithoutInterest;
        if ($diff > 1) {
            $interestRateIncremental = $this->getConfig()->getInterestRateIncremental();
            $interestRate = ( ($diff - 1) * $interestRateIncremental) + $interestRate;
        }

        return $interestRate;
    }

    /**
     * @return InstallmentInterface
     */
    protected function getNewInstallmentInstance()
    {
        return $this->getInstallmentFactory()->create();
    }

    /**
     * @param int $i
     * @return bool
     */
    protected function canProcessInstallment($i)
    {
        $installmentAmount = $this->getGrandTotal() / $i;
        return !($i > 1 && $installmentAmount < $this->getConfig()->getInstallmentMinAmount());
    }

    /**
     * @return int
     */
    protected function getInstallmentsNumber()
    {
        if (! $this->installmentsNumber) {
            $this->installmentsNumber = (int) $this->getConfig()->getInstallmentsNumber();
            $this->installmentsNumber++;
        }

        return $this->installmentsNumber;
    }

    /**
     * @return float
     */
    protected function getGrandTotal()
    {
        if (!$this->grandTotal) {
            $this->grandTotal = $this->getSession()->getQuote()->getGrandTotal();
        }
        return $this->grandTotal;
    }

    /**
     * @return ConfigInterface
     */
    protected function getConfig()
    {
        return $this->config;
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
     * @return InstallmentInterfaceFactory
     */
    protected function getInstallmentFactory()
    {
        return $this->installmentFactory;
    }

    /**
     * @param InstallmentInterfaceFactory $installmentFactory
     * @return $this
     */
    protected function setInstallmentFactory(InstallmentInterfaceFactory $installmentFactory)
    {
        $this->installmentFactory = $installmentFactory;
        return $this;
    }

    /**
     * @return Session
     */
    public function getSession()
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
}
