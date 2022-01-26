<?php
namespace PlugHacker\PlugPagamentos\Concrete;

use PlugHacker\PlugCore\Kernel\Interfaces\PlatformPaymentMethodInterface;

class Magento2PlatformPaymentMethodDecorator implements PlatformPaymentMethodInterface
{
    const CREDIT_CARD = 'credit_card';
    const BOLETO = 'boleto';
    const PIX = "pix";

    private $paymentMethod;

    /**
     * @param $platformOrder
     * @return array
     * @throws \Exception
     */
    public function setPaymentMethod($platformOrder)
    {
        $platformOrder = $platformOrder->getPlatformOrder();
        $payment = $platformOrder->getPayment();
        $paymentMethod = explode('_', $payment->getMethod());

        if (!isset($paymentMethod[1])) {
            return $this->paymentMethod = $paymentMethod;
        }

        if (!method_exists($this, end($paymentMethod))) {
            throw new \Exception('Payment method not found');
        }

        $this->paymentMethod = $this->{end($paymentMethod)}();
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    private function creditcard()
    {
        return self::CREDIT_CARD;
    }

    /**
     * @return string
     * @uses Magento2PlatformPaymentMethodDecorator::setPaymentMethod()
     */
    private function billet()
    {
        return self::BOLETO;
    }


    private function pix()
    {
        return self::PIX;
    }
}
