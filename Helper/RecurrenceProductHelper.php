<?php

namespace PlugHacker\PlugPagamentos\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractModuleCoreSetup as MPSetup;
use PlugHacker\PlugCore\Kernel\Interfaces\PlatformOrderInterface;
use PlugHacker\PlugCore\Recurrence\Repositories\RepetitionRepository;
use PlugHacker\PlugCore\Recurrence\Services\PlanService;
use PlugHacker\PlugCore\Recurrence\Services\RecurrenceService;
use PlugHacker\PlugCore\Recurrence\Services\SubscriptionService;
use PlugHacker\PlugCore\Recurrence\ValueObjects\PlanId;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class RecurrenceProductHelper extends AbstractHelper
{
    /**
     * @var RepetitionRepository
     */
    protected $repetitionRepository;
    protected $objectManager;

    public function __construct()
    {
        Magento2CoreSetup::bootstrap();
        $this->repetitionRepository = new RepetitionRepository();
        $this->objectManager = ObjectManager::getInstance();
    }

    public function getHighestProductCycle($code, $planId = null)
    {
        if (!empty($planId)) {
            return $this->getTotalCyclesFromPlan($planId);
        }
        return $this->getTotalCyclesFromProductRecurrence($code);
    }

    public function getTotalCyclesFromPlan($planID)
    {
        $planService = new PlanService();
        $plan = $planService->findByPlugId(new PlanId($planID));
        $cycles = [];

        if (empty($plan)) {
            return;
        }

        $items = $plan->getItems();
        foreach ($items as $item) {
            $cycles[] = $item->getCycles();
        }

        return $this->returnHighestCycle($cycles);
    }

    /**
     * @param $code
     * @return float|int|null
     */
    public function getTotalCyclesFromProductRecurrence($code)
    {
        $magentoOrder =
            $this->objectManager
                ->get('Magento\Sales\Model\Order')
                ->loadByIncrementId($code);
        $products = $magentoOrder->getAllItems();

        $cycles = [];
        foreach ($products as $product) {
            $cycles[] = $this->getSelectedRepetitionByProduct($product);
        }

        return $this->returnHighestCycle($cycles);
    }

    public function getSelectedRepetition($item)
    {
        $productOptions = $item->getProduct()
            ->getTypeInstance(true)
            ->getOrderOptions($item->getProduct());

        if (empty($productOptions['options'])) {
            return null;
        }

        $optionValue = $this->getOptionValue($productOptions);

        if (empty($optionValue)) {
            return null;
        }

        $repetitionId = $optionValue->getSortOrder();

        $repetition = $this->repetitionRepository->find($repetitionId);
        if ($repetition) {
            return $repetition;
        }

        return null;
    }

    public function getSelectedRepetitionByProduct($product)
    {
        $options = $product->getProductOptions();
        $optionValue = $this->getOptionValue($options);

        if (empty($optionValue) || empty($optionValue->getSortOrder())) {
            return null;
        }

        $sortOrder = $optionValue->getSortOrder();
        $selectedRepetition = $this->repetitionRepository->find($sortOrder);
        return $selectedRepetition->getCycles();
    }

    /**
     * @param array $cycles
     * @return float|int|null
     */
    public function returnHighestCycle(array $cycles)
    {
        if (empty($cycles)) {
            return null;
        }

        return max($cycles);
    }

    public function getOptionValue($productOptions)
    {
        if (empty($productOptions['options'])) {
            return null;
        }
        $optionValue = null;

        foreach ($productOptions['options'] as $option) {
            $productOption = $this->objectManager
                ->get('Magento\Catalog\Model\Product\Option')
                ->load($option['option_id']);

            if (
                !empty($productOption) &&
                $productOption->getSku() === "recurrence"
            ) {
                $optionValue = $this->objectManager
                    ->get('Magento\Catalog\Model\Product\Option\Value')
                    ->load($option['option_value']);
            }

        }
        return $optionValue;
    }
}
