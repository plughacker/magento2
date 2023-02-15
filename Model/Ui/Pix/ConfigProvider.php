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

namespace Malga\Payments\Model\Ui\Pix;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Gateway\ConfigInterface;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'malga_pix';

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var Repository
     */
    private Repository $repository;

    /**
     * ConfigProvider constructor.
     * @param ConfigInterface $config
     * @param Repository $repository
     */
    public function __construct(ConfigInterface $config, Repository $repository)
    {
        $this->config = $config;
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->getValue('active'),
                    'title' => $this->config->getValue('title'),
                    'image' => $this->repository->createAsset('Malga_Payments::images/logo-pix.png')->getUrl()
                ]
            ]
        ];
    }
}
