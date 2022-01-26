<?php

namespace PlugHacker\PlugPagamentos\Api;

use PlugHacker\PlugCore\Recurrence\Interfaces\ProductPlanInterface;
use PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface;

interface ProductPlanApiInterface
{
    /**
     * Save product plan
     *
     * @param \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface $productPlan
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface|array
     */
    public function save($productPlan, $id = null);

    /**
     * Save product plan
     *
     * @param array $form
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface|array
     */
    public function saveFormData();

    /**
     * List product plan
     *
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface[]|array
     */
    public function list();

    /**
     * Update product plan
     *
     * @param int $id
     * @param \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface $productPlan
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface|array
     */
    public function update($id, $productPlan);

    /**
     * Get a product plan
     *
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface|array
     */
    public function find($id);

    /**
     * Delete product plan
     *
     * @param int $id
     * @return \PlugHacker\PlugPagamentos\Api\ObjectMapper\ProductPlan\ProductPlanMapperInterface|array
     */
    public function delete($id);

}
