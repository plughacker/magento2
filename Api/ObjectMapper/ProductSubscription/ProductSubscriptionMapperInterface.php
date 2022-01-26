<?php

namespace PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription;

interface ProductSubscriptionMapperInterface extends \PlugHacker\PlugCore\Recurrence\Interfaces\ProductSubscriptionInterface
{
    /**
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\RepetitionInterface[]|null
     */
    public function getRepetitions();

}
