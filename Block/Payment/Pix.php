<?php
namespace PlugHacker\PlugPagamentos\Block\Payment;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderPaymentInterface as Payment;
use PlugHacker\PlugCore\Kernel\Repositories\OrderRepository;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\OrderId;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\SubscriptionId;
use PlugHacker\PlugCore\Recurrence\Repositories\SubscriptionRepository;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugCore\Recurrence\Repositories\ChargeRepository as SubscriptionChargeRepository;

class Pix extends Template
{
    protected $checkoutSession;
    /**
     * Link constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(Context $context, CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, []);
    }

    /**
     * @return CheckoutSession
     */
    protected function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @return Order
     */
    protected function getLastOrder()
    {
        if (! ($this->checkoutSession->getLastRealOrder()) instanceof Order) {
            throw new \InvalidArgumentException;
        }
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * @return Payment
     */
    protected function getPayment()
    {
        if (! ($this->getLastOrder()->getPayment()) instanceof Payment) {
            throw new \InvalidArgumentException;
        }
        return $this->getLastOrder()->getPayment();
    }

    /**
     * @return string
     */
    public function getPixInfo()
    {
        $info = $this->getPayment();
        $method = $info->getMethod();

        if (strpos($method, "plug_pix") === false) {
            return null;
        }

        $lastTransId = $info->getLastTransId();
        $orderId = substr($lastTransId, 0, 36);
        Magento2CoreSetup::bootstrap();
        $orderService = new \PlugHacker\PlugCore\Payment\Services\OrderService();
        return $orderService->getPixQrCodeInfoFromOrder(new OrderId($orderId));
    }
}
