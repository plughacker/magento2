<?php

namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PaymentConfigSaveAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $_request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->_request = $request;
    }

    public function execute(Observer $observer)
    {
        $params = $this->_request->getParams();

        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Params');
        $logger->info(print_r($params, true));
    }
}
