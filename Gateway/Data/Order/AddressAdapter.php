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

namespace Malga\Payments\Gateway\Data\Order;

use Malga\Payments\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\Order\AddressAdapter as MagentoAddressAdapter;
use Magento\Sales\Api\Data\OrderAddressInterface;

class AddressAdapter extends MagentoAddressAdapter implements AddressAdapterInterface
{
    /**
     * @var OrderAddressInterface
     */
    private OrderAddressInterface $address;

    /**
     * @param OrderAddressInterface $address
     */
    public function __construct(OrderAddressInterface $address)
    {
        $this->address = $address;
        parent::__construct($address);
    }

    /**
     * @inheritDoc
     */
    public function getStreet(): array
    {
        $street = $this->address->getStreet();

        return empty($street) ? [] : $street;
    }
}
