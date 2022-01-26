<?php

namespace PlugHacker\PlugPagamentos\Plugin\Princing\Render;

use PlugHacker\PlugCore\Kernel\Abstractions\AbstractModuleCoreSetup as MPSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use Magento\Catalog\Pricing\Render\FinalPriceBox;

class FinalPricePlugin
{
    /**
     * FinalPricePlugin constructor.
     */
    public function __construct()
    {
        Magento2CoreSetup::bootstrap();
    }

    /**
     * @param FinalPriceBox $subject
     * @param $template
     * @return array
     */
    public function beforeSetTemplate(FinalPriceBox $subject, $template)
    {
        $moduleConfiguration = MPSetup::getModuleConfiguration();

        if (
            $moduleConfiguration->isEnabled() &&
            $moduleConfiguration->getRecurrenceConfig()->isEnabled() &&
            $moduleConfiguration->getRecurrenceConfig()->isShowRecurrenceCurrencyWidget()
        ) {
            return ['PlugHacker_PlugPagementos::product/priceFinal.phtml'];
        }

        return [$template];
    }

    /**
     * @return int|null
     */
    public static function getMaxInstallments()
    {
        $list‌cardConfig = MPSetup::getModuleConfiguration()->getCardConfigs();

        $installment = null;
        foreach ($list‌cardConfig as $cardConfig) {
            if (
                $cardConfig->getBrand()->getName() != 'noBrand' ||
                !$cardConfig->isEnabled()
            ) {
                continue;
            }

            $installment = $cardConfig->getMaxInstallment();
        }

        return $installment;
    }
}
