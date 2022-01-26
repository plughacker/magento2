<?php


namespace PlugHacker\PlugPagamentos\Api\Data;

interface ChargesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Charges list.
     * @return \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface[]
     */
    public function getItems();

    /**
     * Set content list.
     * @param \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
