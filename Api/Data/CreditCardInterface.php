<?php
namespace PlugHacker\PlugPagamentos\Api\Data;

interface CreditCardInterface
{
    const CARD_HOLDER_NAME   = 'card_holder_name';
    const CARD_NUMBER = 'card_number';
    const CARD_CVV = 'card_cvv';
    const CARD_EXPIRATION_DATE = 'card_expiration_date';

    /**
     * @return string
     */
    public function getCardHolderName();

    /**
     * @param string $value
     * @return self
     */
    public function setCardHolderName($value);

    /**
     * @return string
     */
    public function getCardNumber();

    /**
     * @param string $value
     * @return self
     */
    public function setCardNumber($value);

    /**
     * @return string
     */
    public function getCardCvv();

    /**
     * @param string $value
     * @return string
     */
    public function setCardCvv($value);

    /**
     * @return string
     */
    public function getCardExpirationDate();

    /**
     * @param string $value
     * @return self
     */
    public function setCardExpirationDate($value);
}
