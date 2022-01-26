<?php

namespace PlugHacker\PlugPagamentos\Observer;

use PlugHacker\PlugCore\Kernel\Repositories\OrderRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use PlugHacker\PlugCore\Kernel\Exceptions\AbstractPlugCoreException;
use PlugHacker\PlugCore\Kernel\Factories\OrderFactory;
use PlugHacker\PlugCore\Kernel\Services\OrderService;
use PlugHacker\PlugCore\Kernel\Services\OrderLogService;
use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use Magento\Framework\Webapi\Exception as M2WebApiException;
use Magento\Framework\Phrase;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformOrderDecorator;
use PlugHacker\PlugPagamentos\Model\PlugConfigProvider;

class OrderCancelAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return void
     * @throws M2WebApiException
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->moduleIsEnable()) {
            return $this;
        }

        $event = $observer->getEvent();

        $order = $event->getOrder();
        if (empty($order)) {
            return $this;
        }

        $payment = $order->getPayment();

        if (!in_array($payment->getMethod(), $this->plugMethods())) {
            return $this;
        }

        try {
            Magento2CoreSetup::bootstrap();

            $platformOrder = $this->getPlatformOrderFromObserver($observer);
            if ($platformOrder === null) {
                return false;
            }

            $transaction = $this->getTransaction($platformOrder);
            $chargeInfo = $this->getChargeInfo($transaction);

            if ($chargeInfo === false) {
                $this->cancelOrderByIncrementId($platformOrder->getIncrementId());
                return;
            }

            $this->cancelOrderByTransactionInfo(
                $transaction,
                $platformOrder->getIncrementId()
            );

        } catch(AbstractPlugCoreException $e) {
            throw new M2WebApiException(
                new Phrase($e->getMessage()),
                0,
                $e->getCode()
            );
        }
    }

    public function moduleIsEnable()
    {
        $objectManager = ObjectManager::getInstance();
        $plugProvider = $objectManager->get(PlugConfigProvider::class);

        return $plugProvider->getModuleStatus();
    }

    private function cancelOrderByTransactionInfo($transaction, $orderId)
    {
        $orderService = new OrderService();

        $chargeInfo = $this->getChargeInfo($transaction);

        if ($chargeInfo !== false) {

            $orderFactory = new OrderFactory();
            $order = $orderFactory->createFromPostData($chargeInfo);

            $orderService->cancelAtPlug($order);
            return;
        }

        $this->throwErrorMessage($orderId);
    }

    private function cancelOrderByIncrementId($incrementId)
    {
        $orderService = new OrderService();

        $platformOrder = new Magento2PlatformOrderDecorator();
        $platformOrder->loadByIncrementId($incrementId);
        $orderService->cancelAtPlugByPlatformOrder($platformOrder);
    }

    private function getPlatformOrderFromObserver(EventObserver $observer)
    {
        $platformOrder = $observer->getOrder();

        if ($platformOrder !== null)
        {
            return $platformOrder;
        }

        $payment = $observer->getPayment();
        if ($payment !== null) {
            return $payment->getOrder();
        }

        return null;
    }

    private function getTransaction($order)
    {
        $lastTransId = $order->getPayment()->getLastTransId();
        $paymentId = $order->getPayment()->getEntityId();
        $orderId = $order->getPayment()->getParentId();

        $objectManager = ObjectManager::getInstance();
        $transactionRepository = $objectManager->get('Magento\Sales\Model\Order\Payment\Transaction\Repository');

        return $transactionRepository->getByTransactionId(
            $lastTransId,
            $paymentId,
            $orderId
        );
    }

    private function getChargeInfo($transaction)
    {
        if ($transaction === false) {
            return false;
        }

        $chargeInfo =  $transaction->getAdditionalInformation();

        if (!empty($chargeInfo['plug_payment_module_api_response'])) {
            $chargeInfo =
                $chargeInfo['plug_payment_module_api_response'];
            return json_decode($chargeInfo,true);
        }

        return false;
    }

    private function throwErrorMessage($orderId)
    {
        $i18n = new LocalizationService();
        $message = "Can't cancel current order. Please cancel it by Plug panel";

        $ExceptionMessage = $i18n->getDashboard($message);

        $e = new \Exception($ExceptionMessage);
        $log = new OrderLogService();
        $log->orderException($e, $orderId);

        throw $e;
    }

    private function plugMethods()
    {
        return [
          'plug_creditcard',
          'plug_billet',
          'plug_two_creditcard',
          'plug_billet_creditcard',
        ];
    }
}
