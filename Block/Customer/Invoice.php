<?php
namespace PlugHacker\PlugPagamentos\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\SubscriptionId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugCore\Recurrence\Repositories\SubscriptionRepository;
use PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractEntity;
use PlugHacker\PlugCore\Recurrence\Repositories\ChargeRepository;
use PlugHacker\PlugCore\Recurrence\Aggregates\Charge;

class Invoice extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ChargeRepository
     */
    protected $chargeRepository;

    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Link constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @throws InvalidParamException
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Registry $coreRegistry
    ) {
        parent::__construct($context, []);
        Magento2CoreSetup::bootstrap();

        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->chargeRepository = new ChargeRepository();
        $this->subscriptionRepository = new SubscriptionRepository();

        $this->validateUserInvoice($this->coreRegistry->registry('code'));
    }

    /**
     * @return AbstractEntity|Charge[]
     * @throws InvalidParamException
     */
    public function getAllChargesByCodeOrder()
    {
        $orderCode = $this->coreRegistry->registry('code');
        $subscriptionId =
            new SubscriptionId(
                $orderCode
            );

        return $this->chargeRepository->findBySubscriptionId($subscriptionId);
    }

    public function getSubscriptionPaymentMethod()
    {
        $codeOrder = $this->coreRegistry->registry('code');

        $plugId = new SubscriptionId($codeOrder);
        $subscription = $this->subscriptionRepository->findByPlugId($plugId);
        if (!$subscription) {
            return null;
        }
        return $subscription->getPaymentMethod();
    }

    /**
     * @param string $codeOrder
     * @throws InvalidParamException
     */
    private function validateUserInvoice($codeOrder)
    {
        $subscriptionList = $this->subscriptionRepository->findByCustomerId(
            $this->customerSession->getId()
        );

        /* @var string[] $listSubscriptionCode */
        $listSubscriptionCode = [];
        foreach ($subscriptionList as $subscription) {
            $listSubscriptionCode[] = $subscription->getPlugId()->getValue();
        }

        if (!in_array($codeOrder, $listSubscriptionCode)) {
            throw new \Exception(
                'Esse pedido não pertence a esse usuário',
                403
            );
        }
    }
}
