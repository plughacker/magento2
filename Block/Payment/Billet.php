<?php
namespace PlugHacker\PlugPagamentos\Block\Payment;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderPaymentInterface as Payment;
use PlugHacker\PlugCore\Kernel\Repositories\OrderRepository;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\OrderId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class Billet extends Template
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
    public function getBilletUrl()
    {
        $method = $this->getPayment()->getMethod();

        if (strpos($method, "plug_billet") === false) {
            return;
        }
        $info = $this->getPayment();

        $boletoUrl = $this->getPayment()->getAdditionalInformation('billet_url');

        Magento2CoreSetup::bootstrap();
        $boletoUrl = $this->getBoletoLinkFromOrder($info);

        return $boletoUrl;
    }

    private function getBoletoLinkFromOrder($info)
    {
        $lastTransId = $info->getLastTransId();
        $orderId = substr($lastTransId, 0, 36);

        if (!$orderId) {
            return null;
        }

        $orderRepository = new OrderRepository();
        $order = $orderRepository->findByPlugId(new OrderId($orderId));
        $boletoUrl = null;
        if ($order !== null) {
            $charges = $order->getCharges();
            foreach ($charges as $charge) {
                $transaction = $charge->getTransactionRequests();
                if ($transaction) {
                    $savedBoletoUrl = $transaction->getBoletoUrl();
                    if ($savedBoletoUrl !== null) {
                        $boletoUrl = $savedBoletoUrl;
                    }
                }
            }
        }

        return $boletoUrl;
    }
}
