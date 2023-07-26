<?php

namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\Exception as M2WebApiException;
use Magento\Framework\Webapi\Rest\Request;
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
    private Request $request;
    private SerializerInterface $serializer;

    public function __construct(Request $request, SerializerInterface $serializer)
    {
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * @api
     * @return mixed
     */
    public function save()
    {
        try {
            Magento2CoreSetup::bootstrap();

            $postData = new \stdClass();
            $postData->id = '';
            $postData->type = '';
            $postData->data = '';

            $content = $this->request->getContent();

            if (!empty($content)) {
                try {
                    $json = $this->serializer->unserialize($content);

                    $postData->id = (string)$json['id'];
                    $postData->type = \sprintf('%s.%s', $json['object'], $json['event']);
                    $postData->data = $json['data'];
                } catch (\InvalidArgumentException $exception) {
                    throw new M2WebApiException(
                        new Phrase($exception->getMessage()),
                        0,
                        $exception->getCode()
                    );
                }
            }

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
