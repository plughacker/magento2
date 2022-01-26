<?php

namespace PlugHacker\PlugPagamentos\Ui\Component\Recurrence\Column;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use PlugHacker\PlugCore\Kernel\ValueObjects\Id\CustomerId;
use PlugHacker\PlugCore\Payment\Repositories\CustomerRepository;
use PlugHacker\PlugCore\Recurrence\Aggregates\Repetition;
use PlugHacker\PlugCore\Recurrence\Services\RepetitionService;
use PlugHacker\PlugCore\Recurrence\ValueObjects\IntervalValueObject;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class CustomerName extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        Magento2CoreSetup::bootstrap();
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {

            $customerRepository = new CustomerRepository();
            $customerId = new CustomerId($item['customer_id']);

            $plugCustomer =
                $customerRepository->findByPlugId($customerId);
            if (!$plugCustomer) {
                continue;
            }

            $magentoCustomerId = $plugCustomer->getCode();

            $objectManager = ObjectManager::getInstance();
            $customer = $objectManager->get(
                'Magento\Customer\Model\Customer')->load($magentoCustomerId
            );
            $item[$fieldName] = $customer->getName();
        }

        return $dataSource;
    }
}
