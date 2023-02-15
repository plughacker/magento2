<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Malga\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Malga\Payments\Gateway\Data\AddressAdapterInterface;
use Malga\Sdk\Entity\CustomerInterface;

class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
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
     */
    public function build(array $buildSubject): array
    {
        $paymentData = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentData->getOrder();
        /** @var AddressAdapterInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();
        /** @var AddressAdapterInterface $billingAddress */
        $shippingAddress = $order->getShippingAddress();

        return [
            'customer' => [
                'name' => $billingAddress->getFirstname(),
                'email' => $billingAddress->getEmail(),
                'phone' => $billingAddress->getTelephone(),
                // @todo Add document type field selection on system config
                'identityType' => 'cpf',
                // @todo Get customer taxvat number
                'identity' => '97055503019',
                // @todo get customer created at
                //'registrationDate' => $customer->getCreatedAt()
            ],
            'billingAddress' => [
                'country' => $billingAddress->getCountryId(),
                'state' => $billingAddress->getRegionCode(),
                'city' => $billingAddress->getCity(),
                'district' => $billingAddress->getStreet()[3],
                'zipCode' => $billingAddress->getPostcode(),
                'street' => $billingAddress->getStreet()[0],
                'number' => $billingAddress->getStreet()[1],
                'complement' => $billingAddress->getStreet()[2]
            ],
            'deliveryAddress' => [
                'country' => $shippingAddress->getCountryId(),
                'state' => $shippingAddress->getRegionCode(),
                'city' => $shippingAddress->getCity(),
                'district' => $shippingAddress->getStreet()[3],
                'zipCode' => $shippingAddress->getPostcode(),
                'street' => $shippingAddress->getStreet()[0],
                'number' => $shippingAddress->getStreet()[1],
                'complement' => $shippingAddress->getStreet()[2]
            ]
        ];
    }
}
