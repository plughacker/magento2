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

namespace Malga\Payments\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader $subjectReader
     */
    private SubjectReader $subjectReader;


    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function build(array $buildSubject): array
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentData->getPayment();
        $order = $paymentData->getOrder();

        return [
            'merchantId' => 'f744c80a-4cdf-4e7e-b71e-71bd43b9876a',
            // @todo create an formatter to convert price amount
            'amount' => $this->subjectReader->readAmount($buildSubject) * 100,
            'currency' => $order->getCurrencyCode(),
            // @todo add system config value to set descriptor to show in invoice description
            'statementDescriptor' => 'Malga Magento Test',
            'orderId' => $order->getOrderIncrementId(),
            // @todo check with Marcel about this information
            'description' => 'Order description',
            'customerId' => $order->getCustomerId(),
            'paymentMethod' => [
                // !@todo check if is ok
                'paymentType' => $payment->getMethodInstance()->getCode(),
                'expiresIn' => $payment->getMethodInstance()->getConfigData('qrcode_expiration')
            ]
        ];
    }
}
