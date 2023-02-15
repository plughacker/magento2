<?php
/**
 * This file is part of Malga Payment Extension for Adobe Commerce / Magento Open Source Payment Extension. For the
 * full copyright and license information please view the LICENSE.md file that was distributed with this source code.
 *
 * @copyright 2023 Malga
 * @author Malga Team <engineer@malga.io>
 * @link https://docs.malga.io/ Documentation of Malga
 */

declare(strict_types=1);

namespace Malga\Payments\Gateway\Validator;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\Transaction;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ResponseValidator extends GeneralResponseValidator
{
    /**
     * @return array
     */
    protected function getResponseValidators(): array
    {
        return array_merge(
            parent::getResponseValidators(),
            [
                static function ($response) {
                    return [
                        $response instanceof Successful
                        && isset($response->transaction)
                        && in_array(
                            $response->transaction->status,
                            [
                                Transaction::AUTHORIZED,
                                Transaction::SUBMITTED_FOR_SETTLEMENT,
                                Transaction::SETTLING,
                                Transaction::SETTLEMENT_PENDING
                            ],
                            true
                        ),
                        [__('Wrong transaction status')]
                    ];
                }
            ]
        );
    }
}
