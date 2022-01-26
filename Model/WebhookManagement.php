<?php

namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception as M2WebApiException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Service\OrderService;
use PlugHacker\PlugCore\Kernel\Exceptions\AbstractPlugCoreException;
use PlugHacker\PlugCore\Webhook\Exceptions\WebhookAlreadyHandledException;
use PlugHacker\PlugCore\Webhook\Exceptions\WebhookHandlerNotFoundException;
use PlugHacker\PlugCore\Webhook\Services\WebhookReceiverService;
use PlugHacker\PlugPagamentos\Api\WebhookManagementInterface;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class WebhookManagement implements WebhookManagementInterface
{
    /**
     * @api
     * @param mixed $id
     * @param mixed $type
     * @param mixed $data
     * @return array|bool
     */
    public function save($id, $type, $data)
    {
        try {
            Magento2CoreSetup::bootstrap();

            $postData = new \stdClass();
            $postData->id = $id;
            $postData->type = $type;
            $postData->data = $data;

            $webhookReceiverService = new WebhookReceiverService();
            return $webhookReceiverService->handle($postData);
        } catch (WebhookHandlerNotFoundException $e) {
            return [
                "message" => $e->getMessage(),
                "code" => 200
            ];
        } catch (WebhookAlreadyHandledException $e)  {
            return  [
                "message" => $e->getMessage(),
                "code" => 200
            ];
        } catch(AbstractPlugCoreException $e) {
            throw new M2WebApiException(
                new Phrase($e->getMessage()),
                0,
                $e->getCode()
            );
        }
    }
}
