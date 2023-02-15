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

namespace Malga\Payments\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset as FieldsetFramework;
use Magento\Config\Model\Config;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Fieldset extends FieldsetFramework
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var SecureHtmlRenderer
     */
    private SecureHtmlRenderer $secureRenderer;

    /**
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param Config $config
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $config,
        array $data = [],
        SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->config = $config;
        $this->secureRenderer = $secureRenderer;
        parent::__construct($context, $authSession, $jsHelper, $data, $secureRenderer);
    }

    /**
     * @inheritDoc
     */
    protected function _getFrontendClass($element): string
    {
        return parent::_getFrontendClass($element) . ' with-button enabled';
    }

    /**
     * @inheritDoc
     */
    protected function _getHeaderTitleHtml($element): string
    {
        $htmlId = $element->getHtmlId();
        $configureLabel = __('Configure');
        $closeLabel = __('Close');
        $stateUrl = $this->getUrl('adminhtml/*/state');
        /** @noinspection PhpUndefinedMethodInspection */
        $legend = $element->getLegend();
        /** @noinspection PhpUndefinedMethodInspection */
        $comment = $element->getComment();
        $eventListTag = $this->secureRenderer->renderEventListenerAsTag(
            'onclick',
            "window.malgaToggleSolution.call(this, '$htmlId', '$stateUrl');event.preventDefault();",
            "button#$htmlId-head"
        );

        return <<<HTML
<div class="config-heading">
    <div class="button-container">
        <button type="button" class="button action-configure" id="$htmlId-head">
            <span class="state-closed">$configureLabel</span>
            <span class="state-opened">$closeLabel</span>
        </button>
        $eventListTag
    </div>
    <div class="heading">
        <strong>$legend</strong>
        <span class="heading-intro">$comment</span>
        <div class="config-alt"></div>
    </div>
</div>
HTML;
    }

    /**
     * @inheritDoc
     */
    protected function _getHeaderCommentHtml($element): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function _isCollapseState($element): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function _getExtraJs($element): string
    {
        $script = <<<JS
require(['jquery', 'prototype'], function () {
    window.malgaToggleSolution = function (id, url) {
        let doScroll = false;
        Fieldset.toggleCollapse(id, url);
        if ($(this).hasClassName('open')) {
            $$('.with-button button.button').each(function (anotherButton) {
                if (anotherButton !== this && $(anotherButton).hasClassName('open')) {
                    $(anotherButton).click();
                    doScroll = true;
                }
            }.bind(this));
        }
        if (doScroll) {
            let pos = Element.cumulativeOffset($(this));
            window.scrollTo(pos[0], pos[1] - 45);
        }
    }
})
JS;

        return $this->_jsHelper->getScript($script);
    }
}
