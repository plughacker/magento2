<?php

namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use PlugHacker\PlugCore\Payment\Services\CustomerService;
use PlugHacker\PlugCore\Kernel\Services\LogService;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2PlatformCustomerDecorator;
use PlugHacker\PlugPagamentos\Helper\CustomerUpdatePlugHelper;
use PlugHacker\PlugPagamentos\Model\PlugConfigProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;

class AdminCustomerBeforeSave implements ObserverInterface
{
    /**
     * @var CustomerUpdatePlugHelper
     */
    protected $customerUpdatePlugHelper;

    /**
     * AdminCustomerBeforeSave constructor.
     * @param CustomerUpdatePlugHelper $customerUpdatePlugHelper
     * @throws \Exception
     */
    public function __construct(
        CustomerUpdatePlugHelper $customerUpdatePlugHelper
    )
    {
        $this->customerUpdatePlugHelper = $customerUpdatePlugHelper;
        Magento2CoreSetup::bootstrap();
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->moduleIsEnable()) {
            return $this;
        }

        $event = $observer->getEvent();
        $platformCustomer = new Magento2PlatformCustomerDecorator($event->getCustomer());

        $customerService = new CustomerService();
        try {
            $customerService->updateCustomerAtPlug($platformCustomer);
        } catch (\Exception $exception) {
            $log = new LogService('CustomerService');
            $log->info($exception->getMessage());

            if ($exception->getCode() == 404) {
                $log->info(
                    "Deleting customer {$platformCustomer->getCode()} on core table"
                );

                $customerService->deleteCustomerOnPlatform($platformCustomer);
            }

            throw new InputException(__($exception->getMessage()));
        }
    }

    /**
     * @return string
     */
    public function moduleIsEnable()
    {
        $objectManager = ObjectManager::getInstance();

        /* @var PlugConfigProvider $plugProvider */
        $plugProvider = $objectManager->get(PlugConfigProvider::class);

        return $plugProvider->getModuleStatus();
    }
}
