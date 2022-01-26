<?php

namespace PlugHacker\PlugPagamentos\Model\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\LocalizedException;
use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use PlugHacker\PlugCore\Recurrence\Interfaces\ProductPlanInterface;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\CurrentProduct;
use PlugHacker\PlugCore\Recurrence\Services\RepetitionService;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\MoreThanOneRecurrenceProduct;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\NormalWithRecurrenceProduct;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\ProductListInCart;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugCore\Recurrence\Aggregates\Repetition;
use Magento\Catalog\Model\Product\Interceptor;
use PlugHacker\PlugPagamentos\Helper\RecurrenceProductHelper;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Model\Product\Option\Value;
use PlugHacker\PlugCore\Recurrence\Services\PlanService;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\JustProductPlanInCart;
use PlugHacker\PlugCore\Recurrence\Services\CartRules\JustSelfProductPlanInCart;
use PlugHacker\PlugPagamentos\Helper\RulesCartRun;
use PlugHacker\PlugCore\Kernel\Aggregates\Configuration;

class CartConflict
{
    /**
     * @var RepetitionService
     */
    private $repetitionService;

    /**
     * @var RecurrenceProductHelper
     */
    private $recurrenceProductHelper;

    /**
     * @var PlanService
     */
    private $planService;

    /**
     * @var Configuration
     */
    private $plugConfig;

    /**
     * CartConflict constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        Magento2CoreSetup::bootstrap();
        $this->repetitionService = new RepetitionService();
        $this->recurrenceProductHelper = new RecurrenceProductHelper();
        $this->planService = new PlanService();
        $this->rulesCartRun = new RulesCartRun();
        $this->plugConfig = Magento2CoreSetup::getModuleConfiguration();
    }

    /**
     * @param Cart $cart
     * @param $dataQty
     * @throws LocalizedException
     */
    public function beforeUpdateItems(Cart $cart, $dataQty)
    {
        if (
            !$this->plugConfig->isEnabled() ||
            !$this->plugConfig->getRecurrenceConfig()->isEnabled()
        ) {
            return;
        }

        $items = $cart->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if (!isset($dataQty[$item->getItemId()]['qty'])) {
                continue;
            }

            $productPlan = $this->planService->findByProductId(
                $item->getProduct()->getId()
            );

            if (($productPlan !== null) && ($dataQty[$item->getItemId()]['qty'] > 1)) {
                $i18n = new LocalizationService();
                $message = $i18n->getDashboard(
                    'You must have only one product plan in the cart'
                );

                throw new LocalizedException(__($message));
            }
        }
    }

    /**
     * @param Cart $cart
     * @param Interceptor $productInfo
     * @param mixed|null $requestInfo
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        Cart $cart,
        Interceptor $productInfo,
        $requestInfo = null
    ) {
        if (
            !$this->plugConfig->isEnabled() ||
            !$this->plugConfig->getRecurrenceConfig()->isEnabled()
        ) {
            return [$productInfo, $requestInfo];
        }

        $currentProduct = $this->buildCurrentProduct(
            $productInfo,
            $requestInfo
        );

        $productListInCart = $this->buildProductListInCart($cart);

        $this->rulesCartRun->runRulesProductPlan(
            $currentProduct,
            $productListInCart
        );

        $this->rulesCartRun->runRulesProductSubscription(
            $currentProduct,
            $productListInCart
        );

        return [$productInfo, $requestInfo];
    }

    /**
     * @param Interceptor $productInfo
     * @param mixed|null $requestInfo
     * @return CurrentProduct
     */
    protected function buildCurrentProduct(
        Interceptor $productInfo,
        $requestInfo = null
    ) {
        $productPlan = $this->planService->findByProductId($requestInfo['product']);

        $currentProduct = new CurrentProduct();

        $quantity = 1;
        if (isset($requestInfo['qty'])) {
            $quantity = $requestInfo['qty'];
        }

        $currentProduct->setQuantity($quantity);

        if ($productPlan !== null) {
            $currentProduct->setIsNormalProduct(false);
            $currentProduct->setProductPlanSelected($productPlan);
            return $currentProduct;
        }

        $isNormalProduct = $this->checkIsNormalProduct($requestInfo);
        if ($isNormalProduct) {
            $currentProduct->setIsNormalProduct($isNormalProduct);
            return $currentProduct;
        }

        $repetitionSelected = $this->getOptionRecurrenceSelected(
            $productInfo->getOptions(),
            $requestInfo['options']
        );

        if (!$repetitionSelected) {
            $currentProduct->setIsNormalProduct(true);
            return $currentProduct;
        }

        $currentProduct->setRepetitionSelected($repetitionSelected);

        return $currentProduct;
    }

    /**
     * @param Cart $cart
     * @return ProductListInCart
     */
    protected function buildProductListInCart(Cart $cart)
    {
        $productList = new ProductListInCart();

        $itemQuoteList = $cart->getQuote()->getAllVisibleItems();
        foreach ($itemQuoteList as $item) {

            $productPlan = $this->planService->findByProductId(
                $item->getProduct()->getId()
            );

            if ($productPlan !== null) {
                $productList->addProductPlan($productPlan);
                continue;
            }

            $repetitionInCart = $this->recurrenceProductHelper->getSelectedRepetition(
                $item
            );

            if (is_null($repetitionInCart)) {
                $productList->addNormalProducts($item);
                continue;
            }

            $productList->setRepetition($repetitionInCart);
        }

        return $productList;
    }

    /**
     * @param array $requestInfo
     * @return bool
     */
    public function checkIsNormalProduct($requestInfo)
    {
        if (!isset($requestInfo['options'])) {
            return true;
        }

        return false;
    }

    /**
     * @param Option[] $optionsList
     * @param array $optionsSelected
     * @return Repetition|null
     */
    public function getOptionRecurrenceSelected(array $optionsList, array $optionsSelected)
    {
        $productOptionValue = null;
        foreach ($optionsList as $option) {
            if ($option->getSku() != 'recurrence') {
                continue;
            }

            /* @var Value[]|ProductCustomOptionValuesInterface[] $valueList */
            $valueList = $option->getValues();
            $productOptionValue = $this->getOptionsValues($valueList, $optionsSelected);
        }

        if (is_null($productOptionValue)) {
            return null;
        }

        return $this->repetitionService->getRepetitionById(
            $productOptionValue->getSortOrder()
        );
    }

    /**
     * @param Value[] $valueList
     * @param array $optionsSelected
     * @return Value|null
     */
    private function getOptionsValues(array $valueList, array $optionsSelected)
    {
        $optionValueSelected = null;
        foreach ($valueList as $value) {
            $optionValueSelected = $this->getOptionValueSelected($value, $optionsSelected);
            if (!is_null($optionValueSelected)) {
                return $optionValueSelected;
            }
        }

        return $optionValueSelected;
    }

    /**
     * @param Value $value
     * @param array $optionsSelected
     * @return Value|null
     */
    private function getOptionValueSelected(Value $value, array $optionsSelected)
    {
        $optionValueSelected = null;
        foreach ($optionsSelected as $optionId => $optionTypeId) {
            if (($value->getOptionTypeId() == $optionTypeId) &&
                ($value->getData()['option_id'] == $optionId)) {
                $optionValueSelected = $value;
            }
        }

        return $optionValueSelected;
    }
}
