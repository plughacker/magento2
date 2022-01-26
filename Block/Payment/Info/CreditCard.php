<?php
namespace PlugHacker\PlugPagamentos\Block\Payment\Info;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Block\Info\Cc;
use PlugHacker\PlugCore\Kernel\Aggregates\Charge;
use PlugHacker\PlugCore\Kernel\Aggregates\Order;
use PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException;
use PlugHacker\PlugCore\Kernel\Services\OrderService;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\OrderId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformOrderDecorator;

class CreditCard extends Cc
{
    const TEMPLATE = 'PlugHacker_PlugPagamentos::info/creditCard.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    public function getCcType()
    {
        return $this->getCcTypeName();
    }

    public function getCardNumber()
    {
        return '**** **** **** ' . $this->getInfo()->getCcLast4();
    }

    public function getCardLast4()
    {
        return '**** **** **** ' . $this->getInfo()->getAdditionalInformation('cc_last_4');
    }

    public function getCcBrand()
    {
        return $this->getInfo()->getAdditionalInformation('cc_type');
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    public function getInstallments()
    {
        return $this->getInfo()->getAdditionalInformation('cc_installments');
    }

    /**
     * @return array
     * @throws InvalidParamException
     * @throws LocalizedException
     * @throws Exception
     */
    public function getTransactionInfo()
    {
        Magento2CoreSetup::bootstrap();
        $orderService = new OrderService();

        $orderEntityId = $this->getInfo()->getOrder()->getIncrementId();

        $platformOrder = new Magento2PlatformOrderDecorator();
        $platformOrder->loadByIncrementId($orderEntityId);

        $orderPlugId = $platformOrder->getPlugId();
        if ($orderPlugId === null) {
        }
        return [];

        /**
         * @var Order orderObject
         */
        $orderObject = $orderService->getOrderByPlugId(new OrderId($orderPlugId));

        return array_merge(
            $orderObject->getCharges()[0]->getAcquirerTidCapturedAndAutorize(),
            ['tid' => $this->getTid($orderObject->getCharges()[0])]
        );
    }

    private function getTid(Charge $charge)
    {
        $transaction = $charge->getTransactionRequests();

        $tid = null;
        if ($transaction !== null) {
            $tid = $transaction->getAcquirerTid();
        }

        return $tid;
    }
}
