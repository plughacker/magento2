<?php

namespace PlugHacker\PlugCore\Recurrence\Aggregates;

use PlugHacker\PlugCore\Recurrence\Interfaces\SubscriptionInterface;
use PlugHacker\PlugCore\Recurrence\ValueObjects\IntervalValueObject;
use PlugHacker\PlugCore\Recurrence\ValueObjects\PricingSchemeValueObject;
use PlugHacker\PlugCore\Recurrence\ValueObjects\SubscriptionItemValueObject;
use PlugHacker\PlugCore\Recurrence\ValueObjects\SubscriptionStatusValueObject;

class Subscription implements SubscriptionInterface
{
    private $id;
    private $code;
    private $status;
    private $interval;
    private $billingType;
    private $paymentMethod;
    private $items;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id,
        $code,
        SubscriptionStatusValueObject $status,
        IntervalValueObject $interval,
        $billingType,
        $paymentMethod,
        array $items,
        \DateTime $createdAt,
        \DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->status = $status;
        $this->interval = $interval;
        $this->billingType = $billingType;
        $this->paymentMethod = $paymentMethod;
        $this->items = $items;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function getBillingType()
    {
        return $this->billingType;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function addItem(SubscriptionItemValueObject $item)
    {
        $this->items[] = $item;
    }

    public function removeItem(SubscriptionItemValueObject $item)
    {
        foreach ($this->items as $key => $existingItem) {
            if ($existingItem->equals($item)) {
                unset($this->items[$key]);
                break;
            }
        }
    }

    public function setStatus(SubscriptionStatusValueObject $status)
    {
        $this->status = $status;
    }

    public function setInterval(IntervalValueObject $interval)
    {
        $this->interval = $interval;
    }

    public function setBillingType($billingType)
    {
        $this->billingType = $billingType;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
