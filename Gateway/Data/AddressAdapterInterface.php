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

namespace Malga\Payments\Gateway\Data;

use Magento\Payment\Gateway\Data\AddressAdapterInterface as MagentoAddressAdapterInterface;

/**
 * @api
 */
interface AddressAdapterInterface extends MagentoAddressAdapterInterface
{
    /**
     * Gets the street values
     *
     * @return string[]|null
     */
    public function getStreet(): ?array;
}
