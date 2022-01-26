<?php
namespace PlugHacker\PlugPagamentos\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use PlugHacker\PlugPagamentos\Model\Source\EavBillingType;

class Billing implements ArrayInterface
{
    /*
     * Option getter
     * @return array
     */

    public function toOptionArray() {
        $eav = new EavBillingType();
        return $eav->getAllOptions();
    }

}
