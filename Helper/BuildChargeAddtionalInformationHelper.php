<?php

namespace PlugHacker\PlugPagamentos\Helper;

use PlugHacker\PlugCore\Kernel\Aggregates\Transaction;
use PlugHacker\PlugCore\Kernel\ValueObjects\TransactionType;
use PlugHacker\PlugCore\Kernel\Aggregates\Charge;

class BuildChargeAddtionalInformationHelper
{
    /**
     * @param string $paymentMethodPlatform
     * @param Charge[] $charges
     * @return array
     */
    public static function build($paymentMethodPlatform, array $charges)
    {
        /**
         * @var string contains method name, call dynamic
         */
        $methodName = str_replace(
            ['_', 'Plug'],
            '',
            ucwords($paymentMethodPlatform, "_")
        );

        $methodName = "build{$methodName}";

        if (!method_exists(self::class, $methodName)) {
            return [];
        }

        return self::$methodName($charges);
    }

    /**
     * @param Charge[] $charges
     * @return array
     */
    private static function buildCreditcard(array $charges)
    {
        $chargeInformation = [];

        return $chargeInformation;
    }

    /**
     * @param Charge[] $charges
     * @return array
     */
    private static function buildBillet(array $charges)
    {
        $chargeInformation = [];
        return $chargeInformation;
    }
}
