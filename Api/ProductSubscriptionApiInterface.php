<?php

namespace PlugHacker\PlugPagamentos\Api;

use PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface;

interface ProductSubscriptionApiInterface
{
    /**
     * Save product subscription
     *
     * @param \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface $productSubscription
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface|array
     */
    public function save($productSubscription, $id = null);

    /**
     * Save product subscription
     *
     * @param array $form
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface|array
     */
    public function saveFormData();

    /**
     * List product subscription
     *
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface[]
     */
    public function list();

    /**
     * Update product subscription
     *
     * @param int $id
     * @param \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface $productSubscription
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface|array
     */
    public function update($id, $productSubscription);

    /**
     * Get a product subscription
     *
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductSubscription\ProductSubscriptionMapperInterface
     */
    public function getProductSubscription($id);

    /**
     * Delete product subscription
     *
     * @param int $id
     * @return mixed
     */
    public function delete($id);

}
