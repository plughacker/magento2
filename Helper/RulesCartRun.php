<?php

namespace PlugHacker\PlugPagamentos\Helper;

use Magento\Framework\Exception\LocalizedException;
use PlugHacker\PlugCore\Kernel\Abstractions\AbstractModuleCoreSetup as MPSetup;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\CompatibleRecurrenceProducts;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\CurrentProduct;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\JustOneProductPlanInCart;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\JustProductPlanInCart;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\JustSelfProductPlanInCart;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\MoreThanOneRecurrenceProduct;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\NormalWithRecurrenceProduct;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\ProductListInCart;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\RuleInterface;

class RulesCartRun
{
    /**
     * @return RuleInterface[]
     */
    private function getRulesProductPlan()
    {
        return [
            new JustProductPlanInCart(),
            new JustSelfProductPlanInCart(),
            new JustOneProductPlanInCart()
        ];
    }

    /**
     * @return RuleInterface[]
     */
    private function getRulesProductSubscription()
    {
        $recurrenceConfiguration = MPSetup::getModuleConfiguration()
            ->getRecurrenceConfig();

        return [
            new NormalWithRecurrenceProduct($recurrenceConfiguration),
            new MoreThanOneRecurrenceProduct($recurrenceConfiguration),
            new CompatibleRecurrenceProducts()
        ];
    }

    public function runRulesProductPlan(
        CurrentProduct $currentProduct,
        ProductListInCart $productListInCart
    ) {
        foreach ($this->getRulesProductPlan() as $rule) {
            $rule->run($currentProduct, $productListInCart);

            if (!empty($rule->getError())) {
                throw new LocalizedException(__($rule->getError()));
            }
        }
    }

    public function runRulesProductSubscription(
        CurrentProduct $currentProduct,
        ProductListInCart $productListInCart
    ) {
        foreach ($this->getRulesProductSubscription() as $rule) {
            $rule->run($currentProduct, $productListInCart);

            if (!empty($rule->getError())) {
                throw new LocalizedException(__($rule->getError()));
            }
        }
    }
}
