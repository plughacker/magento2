<?php
namespace PlugHacker\PlugPagamentos\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use PlugHacker\PlugPagamentos\Model\Source\EavPaymentMethods;

class PaymentMethods implements ArrayInterface
{

    public function toOptionArray()
    {
        $eav =  new EavPaymentMethods();
        return $eav->getAllOptions();

    }
}
