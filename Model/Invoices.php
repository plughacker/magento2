<?php

namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Invoices extends AbstractModel implements IdentityInterface
{
    protected function _construct()
    {
        $this->_init('PlugHacker\PlugPagamentos\Model\ResourceModel\Invoices');
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
