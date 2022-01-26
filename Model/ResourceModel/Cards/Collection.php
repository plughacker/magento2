<?php
namespace PlugHacker\PlugPagamentos\Model\ResourceModel\Cards;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'PlugHacker\PlugPagamentos\Model\Cards',
            'PlugHacker\PlugPagamentos\Model\ResourceModel\Cards'
        );
    }
}
