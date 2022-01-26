<?php
namespace PlugHacker\PlugPagamentos\Block\Payment\Info;

use Magento\Payment\Block\Info;
use Magento\Framework\DataObject;
use PlugHacker\PlugCore\Kernel\Repositories\OrderRepository;
use PlugHacker\PlugCore\Kernel\Services\OrderService;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\OrderId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformOrderDecorator;

class Billet extends Info
{
    const TEMPLATE = 'PlugHacker_PlugPagamentos::info/billet.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = new DataObject([
            (string)__('Print Billet') => $this->getBilletUrl()
        ]);

        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }

    public function getBilletUrl()
    {
        $method = $this->getInfo()->getMethod();

        if (strpos($method, "plug_billet") === false) {
            return;
        }

        $boletoUrl =  $this->getInfo()->getAdditionalInformation('billet_url');

        Magento2CoreSetup::bootstrap();
        $info = $this->getInfo();

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
                if ($transaction != null) {
                    $savedBoletoUrl = $transaction->getBoletoUrl();
                    if ($savedBoletoUrl !== null) {
                        $boletoUrl = $savedBoletoUrl;
                    }
                }
            }
        }

        return $boletoUrl;
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException
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
         * @var \PlugHacker\PlugCore\Kernel\Aggregates\Order orderObject
         */
        $orderObject = $orderService->getOrderByPlugId(new OrderId($orderPlugId));
        return $orderObject->getCharges()[0]->getTransactionRequests();
    }
}
