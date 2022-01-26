<?php

namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use PlugHacker\PlugCore\Recurrence\Aggregates\Plan;
use PlugHacker\PlugCore\Recurrence\Interfaces\RecurrenceEntityInterface;
use PlugHacker\PlugCore\Recurrence\Services\PlanService;
use PlugHacker\PlugCore\Recurrence\Services\RecurrenceService;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Helper\ProductPlanHelper;
use PlugHacker\PlugPagamentos\Helper\RecurrenceProductHelper;

class UpdateProductPlanObserver implements ObserverInterface
{
    /**
     * @var RecurrenceProductHelper
     */
    protected $recurrenceProductHelper;

    public function __construct(RecurrenceProductHelper $recurrenceProductHelper)
    {
        Magento2CoreSetup::bootstrap();
        $this->recurrenceProductHelper = $recurrenceProductHelper;
    }

    public function execute(Observer $observer)
    {
       $event = $observer->getEvent();
       $product = $event->getProduct();

       if (!$product) {
           return $this;
       }

       $productId = $product->getEntityId();
       $recurrenceService = new RecurrenceService();
       $recurrence = $recurrenceService->getRecurrenceProductByProductId($productId);

       if (!$recurrence || $recurrence->getRecurrenceType() !== Plan::RECURRENCE_TYPE) {
           return $this;
       }

       return $this->updatePlan($recurrence, $product);
    }

    protected function updatePlan(RecurrenceEntityInterface $recurrence)
    {
        try{
            ProductPlanHelper::mapperProductPlan($recurrence);
            $service = new PlanService();
            $service->updatePlanAtPlug($recurrence);
            $service->save($recurrence);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return $this;
    }
}
