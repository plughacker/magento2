<?php

namespace PlugHacker\PlugPagamentos\Concrete;

use PlugHacker\PlugCore\Kernel\Interfaces\PlatformCustomerInterface;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\CustomerId;
use PlugHacker\PlugCore\Payment\ValueObjects\CustomerType;
use PlugHacker\PlugCore\Payment\Repositories\CustomerRepository;

class Magento2PlatformCustomerDecorator implements PlatformCustomerInterface
{
    protected $platformCustomer;

    /** @var CustomerId */
    protected $plugId;

    public function __construct($platformCustomer = null)
    {
        $this->platformCustomer = $platformCustomer;
    }

    public function getCode()
    {
        return $this->platformCustomer->getId();
    }

    /**
     * @return CustomerId|null
     */
    public function getPlugId()
    {
        if ($this->plugId !== null) {
            return $this->plugId;
        }

        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findByCode($this->platformCustomer->getId());

        if ($customer !== null) {
            $this->plugId = $customer->getPlugId()->getValue();
            return $this->plugId;
        }

        /** @var  $mpIdLegado deprecated */
        $mpIdLegado = $this->platformCustomer->getCustomAttribute('customer_id_plug');
        if (!empty($mpIdLegado->getValue())) {
            $this->plugId = $mpIdLegado;
            return $this->plugId;
        }

        return null;
    }

    public function getName()
    {
        $fullname = [
            $this->platformCustomer->getFirstname(),
            $this->platformCustomer->getMiddlename(),
            $this->platformCustomer->getLastname()
        ];

        return implode(" ", $fullname);
    }

    public function getEmail()
    {
        return $this->platformCustomer->getEmail();
    }

    public function getDocument()
    {
        if (!empty($this->platformCustomer->getTaxvat())) {
            return $this->platformCustomer->getTaxvat();
        }
        return null;
    }

    public function getType()
    {
        return CustomerType::individual();
    }

    public function getAddress()
    {
        /** @TODO */
    }

    public function getPhones()
    {
        /** @TODO */
    }

    public function getRegistrationDate()
    {
        return $this->platformCustomer->getCreatedAt();
    }
}
