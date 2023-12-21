<?php

namespace PlugHacker\PlugPagamentos\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;
use PlugHacker\PlugCore\Payment\Repositories\SavedCardRepository;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;
use PlugHacker\PlugPagamentos\Model\Cards;
use PlugHacker\PlugPagamentos\Model\CardsRepository;

class CreditCardDataAssignObserver extends AbstractDataAssignObserver
{
    private $cardsRepository;

    /**
     * CreditCardDataAssignObserver constructor.
     * @param CardsRepository $cardsRepository
     */
    public function __construct(
        CardsRepository $cardsRepository
    )
    {
        $this->cardsRepository = $cardsRepository;
    }

    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $info = $method->getInfoInstance();
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_object($additionalData)) {
            $additionalData = new DataObject($additionalData ?: []);
        }

        if ($additionalData->getCcSavedCard() === 'new') {
            $additionalData->setCcSavedCard('');
        }

        $info->setAdditionalInformation('cc_saved_card', '0');

        if ($additionalData->getCcSavedCard()) {
            $cardId = $additionalData->getCcSavedCard();
            $card = $this->cardsRepository->getById($cardId);

            $info->setAdditionalInformation('cc_saved_card', $additionalData->getCcSavedCard());
            $info->setAdditionalInformation('cc_type', $card->getBrand());
            $info->setAdditionalInformation(
                'cc_last_4',
                (string) $card->getLastFourNumbers()
            );
            $info->addData([
                'cc_type' => $card->getBrand(),
                'cc_owner' => $card->getCardHolderName(),
                'cc_last_4' => (string) $card->getLastFourNumbers()
            ]);
        }else{
            $info->setAdditionalInformation('cc_saved_card', $additionalData->getCcSavedCard());
            $info->setAdditionalInformation('cc_type', $additionalData->getCcType());
            $info->setAdditionalInformation('cc_last_4', substr((string)$additionalData->getCcLast4(),-4));
            $info->setAdditionalInformation('cc_token_credit_card', $additionalData->getCcTokenCreditCard());
            $info->addData([
                'cc_type' => $additionalData->getCcType(),
                'cc_owner' => $additionalData->getCcOwner(),
                'cc_last_4' => $additionalData->getCcLast4(),
                'cc_exp_month' => $additionalData->getCcExpMonth(),
                'cc_exp_year' => $additionalData->getCcExpYear(),
                'cc_token_credit_card' => $additionalData->getCcTokenCreditCard(),
            ]);

            $info->setAdditionalInformation('cc_savecard', $additionalData->getCcSavecard());
        }

        $this->setMultiBuyer($info, $additionalData);
        $info->setAdditionalInformation('cc_installments', 1);

        if ($additionalData->getCcInstallments()) {
            $info->setAdditionalInformation('cc_installments', (int) $additionalData->getCcInstallments());
        }

        return $this;
    }

    /**
     * @param $info
     * @param $additionalData
     */
    protected function setMultiBuyer($info, $additionalData)
    {
        $info->setAdditionalInformation('cc_buyer_checkbox', $additionalData->getCcBuyerCheckbox());
        if ($additionalData->getCcBuyerCheckbox()) {
            $info->setAdditionalInformation('cc_buyer_name', $additionalData->getCcBuyerName());
            $info->setAdditionalInformation('cc_buyer_email', $additionalData->getCcBuyerEmail());
            $info->setAdditionalInformation('cc_buyer_document', $additionalData->getCcBuyerDocument());
            $info->setAdditionalInformation('cc_buyer_street_title', $additionalData->getCcBuyerStreetTitle());
            $info->setAdditionalInformation('cc_buyer_street_number', $additionalData->getCcBuyerStreetNumber());
            $info->setAdditionalInformation('cc_buyer_street_complement', $additionalData->getCcBuyerStreetComplement());
            $info->setAdditionalInformation('cc_buyer_zipcode', $additionalData->getCcBuyerZipcode());
            $info->setAdditionalInformation('cc_buyer_neighborhood', $additionalData->getCcBuyerNeighborhood());
            $info->setAdditionalInformation('cc_buyer_city', $additionalData->getCcBuyerCity());
            $info->setAdditionalInformation('cc_buyer_state', $additionalData->getCcBuyerState());
            $info->setAdditionalInformation('cc_buyer_home_phone', $additionalData->getCcBuyerHomePhone());
            $info->setAdditionalInformation('cc_buyer_mobile_phone', $additionalData->getCcBuyerMobilePhone());
        }
    }
}
