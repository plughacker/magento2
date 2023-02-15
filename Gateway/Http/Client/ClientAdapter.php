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

namespace Malga\Payments\Gateway\Http\Client;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Malga\Sdk\Exception\ConfigException;

class ClientAdapter implements ClientInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;
    private Sdk $sdk;

    /**
     * @param ConfigInterface $config
     * @throws ConfigException
     */
    public function __construct(ConfigInterface $config)
    {
        $sdkConfig = new \Malga\Sdk\Config([
            'client_id' => $config->getValue('client_id_sandbox'),
            'api_key' => $config->getValue('api_id_sandbox'),
            'merchant_id' => $config->getValue('merchant_id_sandbox'),
            'sandbox_mode' => $config->getValue('sandbox_mode')
        ]);
        $this->sdk = new Sdk($sdkConfig);
    }

    /**
     * @inheritDoc
     */
    public function placeRequest(TransferInterface $transferObject): array
    {

    }
}
