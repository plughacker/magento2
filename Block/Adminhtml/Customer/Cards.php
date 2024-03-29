<?php
namespace PlugHacker\PlugPagamentos\Block\Adminhtml\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PlugHacker\PlugCore\Payment\Aggregates\SavedCard;
use PlugHacker\PlugCore\Payment\Repositories\CustomerRepository;
use PlugHacker\PlugCore\Payment\Repositories\SavedCardRepository;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Concrete\Magento2SavedCardAdapter;
use PlugHacker\PlugPagamentos\Model\CardsRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Model\Session;

class Cards extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CardsRepository
     */
    protected $cardsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteria;

    /**
     * Link constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        CardsRepository $cardsRepository,
        SearchCriteriaBuilder $criteria
    ) {
        parent::__construct($context, []);
        $this->setCardsRepository($cardsRepository);
        $this->setCriteria($criteria);
    }

    private function addFilterCriteria($fieldName, $fieldValue, $filterType = 'eq')
    {
        $searchCriteria = $this->getCriteria()
            ->addFilter($fieldName, $fieldValue, $filterType)->create();
        return $searchCriteria;
    }

    /**
     * @return array|Magento2SavedCardAdapter[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCardsList()
    {
        $searchCriteria = $this->addFilterCriteria(
            'id',
            '',
            'notnull'
        );

        $listCards = $this->getCardsRepository()->getList($searchCriteria);

        /* @var \PlugHacker\PlugPagamentos\Model\Cards[] $cards */
        $cards = $listCards->getItems();
        foreach ($cards as &$card) {
            $card->setMaskedNumber('****.****.****.' . $card->getLastFourNumbers());
        }

        $cards = array_values($cards);


        return array_merge($cards, $this->getCoreCards());
    }

    /**
     * @return array|SavedCard[]
     * @throws \Exception
     */
    private function getCoreCards()
    {
        Magento2CoreSetup::bootstrap();

        $savedCardRepository = new SavedCardRepository();
        $customerRepository = new CustomerRepository();

        $listSavedCoreCard = $savedCardRepository->listEntities(0, false);

        /* @var Magento2SavedCardAdapter[]|array $cards */
        $cards = [];
        foreach ($listSavedCoreCard as $savedCoreCard) {
            $customerObject = $customerRepository->findByPlugId(
                $savedCoreCard->getOwnerId()
            );

            $magento2SavedCardAdapter = new Magento2SavedCardAdapter($savedCoreCard);

            if(!is_null($customerObject)) {
                $magento2SavedCardAdapter->setCustomer($customerObject);
            }

            $cards[] = $magento2SavedCardAdapter;
        }

        return $cards;
    }

    /**
     * @return \PlugHacker\PlugPagamentos\Model\CardsRepository
     */
    public function getCardsRepository()
    {
        return $this->cardsRepository;
    }

    /**
     * @param mixed $cardsRepository
     *
     * @return self
     */
    public function setCardsRepository($cardsRepository)
    {
        $this->cardsRepository = $cardsRepository;

        return $this;
    }

    /**
     * @return \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     *
     * @return self
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }
}
