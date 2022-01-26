<?php
/**
 * Created by PhpStorm.
 * User: fabio
 * Date: 02/05/18
 * Time: 15:25
 */

namespace PlugHacker\PlugPagamentos\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use PlugHacker\PlugAPILib\Controllers;
use PlugHacker\PlugAPILib\Models\UpdateCustomerRequest;
use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Config\Config;

class CustomerUpdatePlugHelper
{

    protected $updateCustomerRequest;

    protected $config;

    protected $customerRepositoryInterface;

    /**
     * AdminCustomerSaveAfter constructor.
     */
    public function __construct(
        UpdateCustomerRequest $updateCustomerRequest,
        Config $config,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->updateCustomerRequest = $updateCustomerRequest;
        $this->config = $config;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }


    /**
     * @param $customer
     * @return void
     */
    public function updateEmailPlug($customer)
    {

        $oldCustomer = $this->customerRepositoryInterface->getById($customer->getId());

        if($oldCustomer->getCustomAttribute('customer_id_plug') && ($oldCustomer->getEmail() != $customer->getEmail())){

            $customerIdPlug = $oldCustomer->getCustomAttribute('customer_id_plug')->getValue();

            $this->updateCustomerRequest->email = $customer->getEmail();
            $this->updateCustomerRequest->name = $oldCustomer->getFirstName() . ' ' . $oldCustomer->getLastName();
            $this->updateCustomerRequest->document = preg_replace('/[\/.-]/', '', $oldCustomer->getTaxvat());
            $this->updateCustomerRequest->type = 'individual';

            $this->getApi()->getCustomers()->updateCustomer($customerIdPlug, $this->updateCustomerRequest);

        }

    }

    /**
     * Singleton access to Customers controller
     * @return Controllers\CustomersController The *Singleton* instance
     */
    public function getCustomers()
    {
        return Controllers\CustomersController::getInstance();
    }

    /**
     * @return \PlugHacker\PlugAPILib\PlugAPIClient
     */
    public function getApi()
    {
        return new \PlugHacker\PlugAPILib\PlugAPIClient($this->config->getSecretKey(), '');
    }

}
