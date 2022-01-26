<?php

namespace PlugHacker\PlugPagamentos\Model\Api;

use Magento\Framework\Webapi\Rest\Request;
use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use PlugHacker\PlugCore\Kernel\Services\MoneyService;
use PlugHacker\PlugCore\Recurrence\Services\SubscriptionService;
use PlugHacker\PlugPagamentos\Api\SubscriptionApiInterface;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class Subscription implements SubscriptionApiInterface
{

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    public function __construct(Request $request)
    {
        $this->request = $request;
        Magento2CoreSetup::bootstrap();
        $this->i18n = new LocalizationService();
        $this->moneyService = new MoneyService();
        $this->subscriptionService = new SubscriptionService();
    }

    /**
     * List product subscription
     *
     * @return mixed
     */
    public function list()
    {
        $result = $this->subscriptionService->listAll();
        return json_decode(json_encode($result), true);
    }

    /**
     * Cancel subscription
     *
     * @param int $id
     * @return mixed
     */
    public function cancel($id)
    {
        try {
            $response = $this->subscriptionService->cancel($id);
            return $response;
        } catch (\Exception $exception) {
            return [
                "code" => $exception->getCode(),
                "message" => $exception->getMessage()
            ];
        }
    }

    /**
     * List product subscription
     *
     * @param string $customerId
     * @return \PlugHacker\PlugCore\Recurrence\Interfaces\SubscriptionInterface[]
     */
    public function listByCustomerId($customerId)
    {
        // TODO: Implement listByCustomerId() method.
    }
}
