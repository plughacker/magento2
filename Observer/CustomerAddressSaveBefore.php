<?php

namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Exception\InputException;
use PlugHacker\PlugPagamentos\Model\PlugConfigProvider;
use Magento\Framework\App\ObjectManager;
use PlugHacker\PlugCore\Kernel\Services\LogService;
use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class CustomerAddressSaveBefore implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->moduleIsEnable()) {
            return $this;
        }

        $customerAddress = $observer->getCustomerAddress();

        $this->addressValidation($customerAddress);

        return $this;
    }

    public function moduleIsEnable()
    {
        $objectManager = ObjectManager::getInstance();
        $plugProvider = $objectManager->get(PlugConfigProvider::class);

        return $plugProvider->getModuleStatus();
    }

    /**
     * @param $customerAddress
     * @throws InputException
     */
    public function addressValidation($customerAddress)
    {
        $allStreetLines = $customerAddress->getStreet();

        if (empty($customerAddress->getCity())) {
            throw new InputException(__("Please check your address. City is required."));
        }

        if (empty($customerAddress->getRegionCode())) {
            throw new InputException(__("Please check your address. Region is required."));
        }

        if (empty($customerAddress->getPostcode())) {
            throw new InputException(__("Please check your address. Postcode is required."));
        }

        if (empty($customerAddress->getCountryId())) {
            throw new InputException(__("Please check your address. Country is required."));
        }

        if (empty($customerAddress->getName()) || \mb_strlen($customerAddress->getName()) > 65) {
            throw new InputException(__("Please check your address. Name and firstname are required."));
        }

        if (!is_array($allStreetLines) || count($allStreetLines) < 3) {

            Magento2CoreSetup::bootstrap();

            $i18n = new LocalizationService();
            $message = "Invalid address. Please fill the street lines and try again.";
            $ExceptionMessage = $i18n->getDashboard($message);
            $incorrectAddress = json_encode($allStreetLines, JSON_PRETTY_PRINT);
            $ExceptionMessage .= ' ' . $incorrectAddress;

            $e = new \Exception($ExceptionMessage );
            $log = new LogService('Order', true);
            $log->exception($e);

            //Magento accepts only Phrase() exceptions in this case
            throw new InputException(__($message));
        }
    }
}
