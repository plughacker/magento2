<?php


namespace PlugHacker\PlugPagamentos\Api\Data;

interface CardsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Cards list.
     * @return \PlugHacker\PlugPagamentos\Api\Data\CardsInterface[]
     */
    public function getItems();

    /**
     * Set content list.
     * @param \PlugHacker\PlugPagamentos\Api\Data\CardsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
