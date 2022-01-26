<?php
namespace PlugHacker\PlugPagamentos\Model\ResourceModel\ProductsSubscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'PlugHacker\PlugPagamentos\Model\ProductsSubscription',
            'PlugHacker\PlugPagamentos\Model\ResourceModel\ProductsSubscription'
        );
    }
}
