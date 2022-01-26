<?php

namespace PlugHacker\PlugPagamentos\Ui\Component\Recurrence\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use PlugHacker\PlugCore\Kernel\Services\LocalizationService;
use PlugHacker\PlugCore\Kernel\Services\MoneyService;
use PlugHacker\PlugCore\Recurrence\Aggregates\Repetition;
use PlugHacker\PlugCore\Recurrence\Services\ProductSubscriptionService;
use PlugHacker\PlugCore\Recurrence\Services\RepetitionService;
use PlugHacker\PlugCore\Recurrence\ValueObjects\DiscountValueObject;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Helper\ProductSubscriptionHelper;

class RepetitionsColumn extends Column
{
    /**
     * @var LocalizationService
     */
    protected $i18n;

    /**
     * @var ProductSubscriptionHelper
     */
    protected $productSubscriptionHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        Magento2CoreSetup::bootstrap();
        $this->i18n = new LocalizationService();
        $this->moneyService = new MoneyService();
        $this->productSubscriptionHelper = new ProductSubscriptionHelper();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item['repetitions'] = $this->getValueFormatted($item);
        }

        return $dataSource;
    }

    public function getValueFormatted($item)
    {
        $id = $item['id'];
        $productSubscriptionService = new ProductSubscriptionService();
        $repetitionSevice = new RepetitionService();

        $productSubscription = $productSubscriptionService->findById($id);
        $repetitions = [];

        foreach ($productSubscription->getRepetitions() as $repetition) {
            $repetitions[] = $repetitionSevice->getCycleTitle($repetition);
        }

        return implode(' | ', $repetitions);
    }
}
