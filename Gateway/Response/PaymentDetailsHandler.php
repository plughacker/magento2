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

namespace Malga\Payments\Gateway\Response;

use Malga\Payments\Observer\DataAssignObserver;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class PaymentDetailsHandler implements HandlerInterface
{
    const AVS_POSTAL_RESPONSE_CODE = 'avsPostalCodeResponseCode';

    const AVS_STREET_ADDRESS_RESPONSE_CODE = 'avsStreetAddressResponseCode';

    const CVV_RESPONSE_CODE = 'cvvResponseCode';

    const PROCESSOR_AUTHORIZATION_CODE = 'processorAuthorizationCode';

    const PROCESSOR_RESPONSE_CODE = 'processorResponseCode';

    const PROCESSOR_RESPONSE_TEXT = 'processorResponseText';

    const TRANSACTION_SOURCE = 'transactionSource';

    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        self::AVS_POSTAL_RESPONSE_CODE,
        self::AVS_STREET_ADDRESS_RESPONSE_CODE,
        self::CVV_RESPONSE_CODE,
        self::PROCESSOR_AUTHORIZATION_CODE,
        self::PROCESSOR_RESPONSE_CODE,
        self::PROCESSOR_RESPONSE_TEXT,
    ];

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var State
     */
    private $state;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param State $state
     */
    public function __construct(
        SubjectReader $subjectReader,
        State $state
    ) {
        $this->subjectReader = $subjectReader;
        $this->state = $state;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);
        /** @var OrderPaymentInterface $payment */
        $payment = $paymentDO->getPayment();

        $payment->setCcTransId($transaction->id);
        $payment->setLastTransId($transaction->id);

        //remove previously set payment nonce
        $payment->unsAdditionalInformation(DataAssignObserver::PAYMENT_METHOD_NONCE);
        foreach ($this->additionalInformationMapping as $item) {
            if (!isset($transaction->$item)) {
                continue;
            }
            $payment->setAdditionalInformation($item, $transaction->$item);
        }

        $this->setTransactionSource($payment);
    }

    /**
     * When within admin area; assume MOTO transactionSource
     * @param OrderPaymentInterface $payment
     * @throws LocalizedException
     * @throws LocalizedException
     * @todo check if MOTO is needed or useful in Malga Integration
     */
    public function setTransactionSource(OrderPaymentInterface $payment)
    {
        if ($this->state->getAreaCode() === Area::AREA_ADMINHTML) {
            $payment->setAdditionalInformation(self::TRANSACTION_SOURCE, 'MOTO');
        }
    }
}
