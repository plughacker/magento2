<?php

namespace PlugHacker\PlugPagamentos\Block\Payment\Info;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Block\Info;
use PlugHacker\PlugCore\Kernel\Aggregates\Order;
use PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException;
use PlugHacker\PlugCore\Kernel\Services\OrderService;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\OrderId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformOrderDecorator;

class Pix extends Info
{
    const TEMPLATE = 'PlugHacker_PlugPagamentos::info/pix.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getPixInfo()
    {
        $info = $this->getInfo();
        $method = $info->getMethod();

        if (strpos($method, "plug_pix") === false) {
            return null;
        }

        $lastTransId = $info->getLastTransId();
        $orderId = substr($lastTransId, 0, 36);

        Magento2CoreSetup::bootstrap();
        $orderService= new \PlugHacker\PlugCore\Payment\Services\OrderService();
        return $orderService->getPixQrCodeInfoFromOrder(new OrderId($orderId));
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    /**
     * @return mixed
     * @throws LocalizedException
     * @throws InvalidParamException
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
            return [];
        }

        /**
         * @var Order orderObject
         */
        $orderObject = $orderService->getOrderByPlugId(new OrderId($orderPlugId));
        return $orderObject->getCharges()[0]->getTransactionRequests();
    }
}
