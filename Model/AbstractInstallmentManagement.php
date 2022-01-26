<?php

namespace PlugHacker\PlugPagamentos\Model;

use PlugHacker\PlugCore\Kernel\Aggregates\Order;
use PlugHacker\PlugCore\Kernel\Exceptions\InvalidParamException;
use PlugHacker\PlugCore\Kernel\Services\InstallmentService;
use PlugHacker\PlugCore\Kernel\Services\MoneyService;
use PlugHacker\PlugCore\Kernel\ValueObjects\CardBrand;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

abstract class AbstractInstallmentManagement
{
    public function __construct()
    {
    }

    /**
     * @param Order|null $order
     * @param CardBrand|null $brand
     * @param null $value
     * @return array
     * @throws InvalidParamException
     */
    protected function getCoreInstallments(
        Order $order = null,
        CardBrand $brand = null,
        $value = null
    ) {
        Magento2CoreSetup::bootstrap();
        $installmentService = new InstallmentService();
        $moneyService = new MoneyService();

        $installments = $installmentService->getInstallmentsFor(
            $order,
            $brand,
            $value * 100
        );

        $result = [];
        foreach ($installments as $installment) {
            $result[] = [
                'id' => $installment->getTimes(),
                'interest' =>
                    $moneyService->centstoFloat(
                        $moneyService->floatToCents(
                            ($installment->getTotal() - $installment->getBaseTotal()) / 100
                        )
                    ),
                'total_with_tax' => $moneyService->centstoFloat(
                    $moneyService->floatToCents(
                        $installment->getTotal() / 100
                    )
                ),
                'label' => $installmentService->getLabelFor($installment)
            ];
        }

        return $result;
    }
}
