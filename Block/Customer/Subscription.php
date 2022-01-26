<?php

namespace PlugHacker\PlugPagamentos\Block\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractEntity;
use PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException;
use PlugHacker\PlugCore\Recurrence\Aggregates\Repetition;
use PlugHacker\PlugCore\Recurrence\Repositories\SubscriptionRepository;
use PlugHacker\PlugCore\Recurrence\Services\RepetitionService;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Helper\RecurrenceProductHelper;

class Subscription extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    protected $objectManager;
    /**
     * @var RecurrenceProductHelper
     */
    private $recurrenceProductHelper;

    /**
     * Link constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @throws \Exception
     */
    public function __construct(
        Context $context,
        Session $customerSession
    ) {
        parent::__construct($context, []);
        Magento2CoreSetup::bootstrap();

        $this->customerSession = $customerSession;
        $this->subscriptionRepository = new SubscriptionRepository();
        $this->objectManager = ObjectManager::getInstance();
        $this->recurrenceProductHelper = new RecurrenceProductHelper();
    }

    /**
     * @return AbstractEntity|\PlugHacker\PlugCore\Recurrence\Aggregates\Subscription[]|null
     * @throws InvalidParamException
     */
    public function getAllSubscriptionRecurrenceCoreByCustomerId()
    {
        return $this->subscriptionRepository->findByCustomerId(
            $this->customerSession->getId()
        );
    }

    public function getHighestProductCycle($subscription)
    {
        return $this->recurrenceProductHelper
            ->getHighestProductCycle(
                $subscription->getCode(),
                $subscription->getPlanIdValue()
            );

    }

    public function getInterval($subscription)
    {
        $repetition = new Repetition();
        $repetitionService = new RepetitionService();

        $repetition->setInterval($subscription->getIntervalType());
        $repetition->setIntervalCount($subscription->getIntervalCount());

        return $repetitionService->getCycleTitle($repetition);
    }

    public function getSubscriptionCreatedDate($subscription)
    {
        $subscriptionRepository = new SubscriptionRepository();
        $subscription = $subscriptionRepository->findByPlugId($subscription->getSubscriptionId());
        $createdAt = new \Datetime($subscription->getCreatedAt());
        return $createdAt->format('d/m/Y');

    }
}
