<?php

namespace PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan;

use PlugHacker\PlugCore\Recurrence\Interfaces\ProductPlanInterface;

interface ProductPlanMapperInterface extends \PlugHacker\PlugCore\Recurrence\Interfaces\ProductPlanInterface
{
    /**
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\SubProduct[]
     */
    public function getItems();
}
