<?php

namespace PlugHacker\PlugPagamentos\Plugin\Admin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use PlugHacker\PlugPagamentos\Helper\CustomerUpdatePlugHelper;

class CustomerPlugin
{

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerUpdatePlugHelper
     */
    protected $customerUpdatePlugHelper;

    /**
     * CustomerPlugin constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerFactory $customerFactory
     * @param CustomerUpdatePlugHelper $customerUpdatePlugHelper
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerFactory $customerFactory,
        CustomerUpdatePlugHelper $customerUpdatePlugHelper
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory = $customerFactory;
        $this->customerUpdatePlugHelper = $customerUpdatePlugHelper;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function beforeExecute($subject)
    {

        $user = $subject->getRequest()->getPost()->get('items');
        $userData = array_shift($user);

        $customer = $this->customerFactory->create();
        $customer->setId($userData['entity_id']);
        $customer->setEmail($userData['email']);

        $this->customerUpdatePlugHelper->updateEmailPlug($customer);

        return $subject;

    }

}
