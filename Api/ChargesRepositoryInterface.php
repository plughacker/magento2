<?php


namespace PlugHacker\PlugPagamentos\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChargesRepositoryInterface
{


    /**
     * Save Charges
     * @param \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface $charges
     * @return \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface $charges
    );

    /**
     * Retrieve Charges
     * @param string $chargesId
     * @return \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($chargesId);

    /**
     * Retrieve Charges matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \PlugHacker\PlugPagamentos\Api\Data\ChargesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Charges
     * @param \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface $charges
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \PlugHacker\PlugPagamentos\Api\Data\ChargesInterface $charges
    );

    /**
     * Delete Charges by ID
     * @param string $chargesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($chargesId);
}
