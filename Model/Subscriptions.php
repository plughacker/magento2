<?php

namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Subscriptions extends AbstractModel implements IdentityInterface
{
    protected function _construct()
    {
        $this->_init('PlugHacker\PlugPagamentos\Model\ResourceModel\Subscriptions');
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
