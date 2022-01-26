<?php
namespace PlugHacker\PlugPagamentos\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use PlugHacker\PlugPagamentos\Model\Source\EavInterval;

class Interval implements ArrayInterface
{

    public function toOptionArray() {
        $eav = new EavInterval();
        return $eav->getAllOptions();
    }

}
