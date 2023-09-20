<?php

namespace PlugHacker\PlugPagamentos\Concrete;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractDataService;
use PlugHacker\PlugCore\Kernel\Aggregates\Order;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\ChargeId;

class Magento2DataService extends AbstractDataService
{
    public function updateAcquirerData(Order $order)
    {
        $platformOrder = $order->getPlatformOrder()->getPlatformOrder();

        $objectManager = ObjectManager::getInstance();
        $transactionRepository = $objectManager->get(Repository::class);
        $lastTransId = $platformOrder->getPayment()->getLastTransId();
        $paymentId = $platformOrder->getPayment()->getEntityId();
        $orderId = $platformOrder->getPayment()->getParentId();

        $transactionAuth = $transactionRepository->getByTransactionId(
            str_replace('-capture', '', $lastTransId),
            $paymentId,
            $orderId
        );

        if ($transactionAuth === false) {
            $transactionAuth =
                $transactionRepository->getByTransactionId(
                    $lastTransId,
                    $paymentId,
                    $orderId
                );
        }

        $additionalInfo = [];
        if ($transactionAuth !== false) {
            $currentCharges = $order->getCharges();

            foreach($currentCharges as $charge) {
                $baseKey = $this->getChargeBaseKey($transactionAuth, $charge);
                if ($baseKey === null) {
                    continue;
                }

                $lastPlugTransaction = $charge->getTransactionRequests();

                $additionalInfo[$baseKey . '_acquirer_nsu'] =
                    $lastPlugTransaction->getAcquirerNsu();

                $additionalInfo[$baseKey . '_acquirer_tid'] =
                    $lastPlugTransaction->getAcquirerTid();

                $additionalInfo[$baseKey . '_acquirer_auth_code'] =
                    $lastPlugTransaction->getAcquirerAuthCode();

                $additionalInfo[$baseKey . '_acquirer_name'] =
                    $lastPlugTransaction->getAcquirerName();

                $additionalInfo[$baseKey . '_acquirer_message'] =
                    $lastPlugTransaction->getAcquirerMessage();

                $additionalInfo[$baseKey . '_brand'] =
                    $lastPlugTransaction->getBrand();

                $additionalInfo[$baseKey . '_installments'] =
                    $lastPlugTransaction->getInstallments();
            }

            $this->OLDcreateCaptureTransaction(
                $platformOrder,
                $transactionAuth,
                $additionalInfo
            );
        }
    }

    private function getChargeBaseKey($transactionAuth, $charge)
    {
        $orderCreationResponse =
            $transactionAuth->getAdditionalInformation('plug_payment_module_api_response');

        if ($orderCreationResponse === null) {
            return null;
        }

        $orderCreationResponse = json_decode($orderCreationResponse);

        $authCharges = $orderCreationResponse->charges;

        $outdatedCharge = null;
        foreach ($authCharges as $authCharge) {
            if ($charge->getPlugId()->equals(new ChargeId($authCharge->id)))
            {
                $outdatedCharge = $authCharge;
            }
        }

        if ($outdatedCharge === null) {
            return null;
        }

        try {
            //if it have no nsu, then it isn't a credit_card transaction;
            $lastNsu = $outdatedCharge->transactionRequests->acquirer_nsu;
        }catch (\Throwable $e) {
            return null;
        }

        $additionalInformation = $transactionAuth->getAdditionalInformation();
        foreach ($additionalInformation as $key => $value) {
            if ($value == $lastNsu) {
                return str_replace('_acquirer_nsu', '', $key);
            }
        }

        return null;
    }

    /** This method is to mantain compatibility with legacy orders */
    private function OLDcreateCaptureTransaction($order, $transactionAuth, $additionalInformation)
    {
        $objectManager = ObjectManager::getInstance();
        $transactionRepository = $objectManager->get(Repository::class);

        /** @var Order\Payment $payment */
        $payment = $order->getPayment();

        $transaction = $transactionRepository->create();
        $transaction->setParentId($transactionAuth->getTransactionId());
        $transaction->setOrderId($order->getEntityId());
        $transaction->setPaymentId($payment->getEntityId());
        $transaction->setTxnId($transactionAuth->getTxnId() . '-capture');
        $transaction->setParentTxnId($transactionAuth->getTxnId(), $transactionAuth->getTxnId() . '-capture');
        $transaction->setTxnType('capture');
        $transaction->setIsClosed(true);

        foreach ( $additionalInformation as $key => $value ) {
            $transaction->setAdditionalInformation($key, $value);
        }

        $transactionRepository->save($transaction);
    }

    public function createCaptureTransaction(Order $order)
    {
        $this->createTransaction($order, parent::TRANSACTION_TYPE_CAPTURE, false);
    }

    public function createAuthorizationTransaction(Order $order)
    {
        $this->createTransaction($order, parent::TRANSACTION_TYPE_AUTHORIZATION, false);
    }

    public function createVoidTransaction(Order $order)
    {
        $this->createTransaction($order, parent::TRANSACTION_TYPE_VOID, true);
    }

    private function createTransaction(Order $order, $transactionType, $closed)
    {
        $platformOrder = $order->getPlatformOrder()->getPlatformOrder();
        $platformPayment = $platformOrder->getPayment();

        $objectManager = ObjectManager::getInstance();

        /**
         * @var Repository $transactionRepository
         */
        $transactionRepository = $objectManager->get(Repository::class);

        $transaction = $transactionRepository->create();

        $transaction->setOrderId($platformOrder->getEntityId());
        $transaction->setPaymentId($platformPayment->getEntityId());
        $transaction->setTxnType($transactionType);
        $transaction->setIsClosed($closed);
        $transaction->setTxnId($order->getPlugId()->getValue() . '-' . $transactionType);

        $charges = $order->getCharges();
        $additionalInformation = [];
        foreach ($charges as $charge) {
            //@todo verify behavior for other types of charge, like boleto.
            $chargeId = $charge->getPlugId()->getValue();
            $transactionRequests = $charge->getTransactionRequests();

            $additionalInformation[$chargeId] = [];
        }

        foreach ($additionalInformation as $key => $value) {
            $transaction->setAdditionalInformation($key, $value);
        }

        try {
            $transactionRepository->save($transaction);
        } catch (\Exception $e) {
            // nothing
        }

        $platformPayment->setLastTransId($transaction->getTxnId());
        $platformPayment->save();
    }
}
