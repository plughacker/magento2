<?php

namespace PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Command;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Model\Order\Payment;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractModuleCoreSetup as MPSetup;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractPlatformOrderDecorator;
use PlugHacker\PlugCore\Kernel\Interfaces\PlatformOrderInterface;
use PlugHacker\PlugCore\Kernel\Services\OrderLogService;
use PlugHacker\PlugCore\Kernel\Services\OrderService;
use PlugHacker\PlugCore\Recurrence\Services\RecurrenceService;
use PlugHacker\PlugCore\Recurrence\Services\SubscriptionService;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformPaymentMethodDecorator;
use PlugHacker\PlugPagamentos\Model\Ui\CreditCard\ConfigProvider;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception as M2WebApiException;
use PlugHacker\PlugPagamentos\Helper\RecurrenceProductHelper;

class InitializeCommand implements CommandInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $mageOrderRepository;

    /**
     * @param OrderRepositoryInterface $mageOrderRepository
     */
    public function __construct(
        OrderRepositoryInterface $mageOrderRepository
    ) {
        $this->mageOrderRepository = $mageOrderRepository;
    }

    /**
     * @param array $commandSubject
     * @return $this
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Framework\DataObject $stateObject */
        $stateObject = $commandSubject['stateObject'];
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof Payment) {
            throw new \LogicException('Order Payment should be provided');
        }
        $orderResult = $this->doCoreDetour($payment);
        if ($orderResult !== false) {
            $order = $payment->getOrder();
            $orderResult->loadByIncrementId(
                $orderResult->getIncrementId()
            );

            $stateObject->setData(
                OrderInterface::STATE,
                $orderResult->getState()->getState()
            );
            $stateObject->setData(
                OrderInterface::STATUS,
                $orderResult->getStatus()
            );

            $order->setState($orderResult->getState()->getState());
            $order->setStatus($orderResult->getStatus());

            $this->mageOrderRepository->save($order);
            return $this;
        }

        $payment->getOrder()->setCanSendNewEmailFlag(true);
        $baseTotalDue = $payment->getOrder()->getBaseTotalDue();
        $totalDue = $payment->getOrder()->getTotalDue();
        $payment->authorize(true, $baseTotalDue);
        $payment->setAmountAuthorized($totalDue);
        $payment->setBaseAmountAuthorized($payment->getOrder()->getBaseTotalDue());
        $customStatus = $payment->getData('custom_status');

        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);

        if ($payment->getMethod() === ConfigProvider::CODE) {
            $stateObject->setData(OrderInterface::STATE, $customStatus->getData('state'));
            $stateObject->setData(OrderInterface::STATUS, $customStatus->getData('status'));
        }

        if ($payment->getMethod() != ConfigProvider::CODE) {
            $stateObject->setData(OrderInterface::STATUS, $payment->getMethodInstance()->getConfigData('order_status'));
        }

        $stateObject->setData('is_notified', false);

        return $this;
    }

     /** @return AbstractPlatformOrderDecorator */
    private function doCoreDetour($payment)
    {
        $order =  $payment->getOrder();

        $log = new OrderLogService();

        Magento2CoreSetup::bootstrap();

        $platformOrderDecoratorClass = MPSetup::get(
            MPSetup::CONCRETE_PLATFORM_ORDER_DECORATOR_CLASS
        );

        $platformPaymentMethodDecoratorClass = MPSetup::get(
            MPSetup::CONCRETE_PLATFORM_PAYMENT_METHOD_DECORATOR_CLASS
        );

        /** @var PlatformOrderInterface $orderDecorator */
        $orderDecorator = new $platformOrderDecoratorClass();
        $orderDecorator->setPlatformOrder($order);

        $paymentMethodDecorator = new $platformPaymentMethodDecoratorClass();
        $paymentMethodDecorator->setPaymentMethod($orderDecorator);

        $orderDecorator->setPaymentMethod($paymentMethodDecorator->getPaymentMethod());

        $quote = $orderDecorator->getQuote();

        try {
            $quoteSuccess = $quote->getCustomerNote();
            if ($quoteSuccess === 'plug-processing' && 0) {
                $log->orderInfo(
                    $orderDecorator->getCode(),
                    "Quote already used, order id duplicated. Customer Note: {$quoteSuccess}"
                );
                throw new \Exception("Quote already used, order id duplicated.");
            }

            $quote->setCustomerNote('plug-processing');
            $quote->save();

            $log->orderInfo(
                $orderDecorator->getCode(),
                "Changing status quote to processing."
            );

            $orderService = new OrderService();
            $orderService->createOrderAtPlug($orderDecorator);

            $orderDecorator->save();

            return $orderDecorator;
        } catch (\Exception $e) {

            $quote->setCustomerNote('');
            $quote->save();

            $message = "Order failed, changing status quote to failed. \n";
            $message .= "Error message: " . $e->getMessage();
            $log->orderInfo(
                $orderDecorator->getCode(),
                $message
            );

            throw new M2WebApiException(
                new Phrase($e->getMessage()),
                0,
                $e->getCode()
            );
        }
    }
}
