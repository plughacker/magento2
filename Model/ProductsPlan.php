<?php

namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ProductsPlan extends AbstractModel implements IdentityInterface
{
    protected function _construct()
    {
        $this->_init('PlugHacker\PlugPagamentos\Model\ResourceModel\ProductsPlan');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return $this->getId();
    }
}
