<?php

namespace PlugHacker\PlugPagamentos\Concrete;

use PlugHacker\PlugCore\Payment\Aggregates\Customer;
use PlugHacker\PlugCore\Payment\Aggregates\SavedCard;

final class Magento2SavedCardAdapter
{
    private $adaptee;

    /**
     * @var Customer
     */
    private $customer;

    public function __construct(SavedCard $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    public function getBrand()
    {
        return $this->adaptee->getBrand()->getName();
    }

    public function getLastFourNumbers()
    {
        return $this->adaptee->getLastFourDigits()->getValue();
    }

    public function getCreatedAt()
    {
        $createdAt = $this->adaptee->getCreatedAt();
        if ($createdAt !== null) {
            return $createdAt->format(SavedCard::DATE_FORMAT);
        }

        return null;
    }

    public function getId()
    {
        return 'plug_core_' . $this->adaptee->getId();
    }

    public function getFirstSixDigits()
    {
        return $this->adaptee->getFirstSixDigits()->getValue();
    }

    public function getMaskedNumber()
    {
        $firstSix = $this->getFirstSixDigits();
        $lastFour = $this->getLastFourNumbers();

        $firstSix = number_format($firstSix/100, 2, '.', '');

        return $firstSix . '**.****.' . $lastFour;
    }

    public function setCustomer(Customer $customerObject)
    {
        $this->customer = $customerObject;
    }

    public function getCardId()
    {
        return $this->adaptee->getPlugId()->getValue();
    }

    /**
     * int|null
     */
    public function getCustomerId()
    {
        if (is_null($this->customer)) {
            return null;
        }

        return $this->customer->getCode();
    }
}
