<?php
namespace PlugHacker\PlugPagamentos\Model;

use Magento\Framework\DataObject;
use PlugHacker\PlugPagamentos\Api\Data\CreditCardInterface;
use Magento\Framework\Pricing\Helper\Data;

class CreditCard extends DataObject implements CreditCardInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCardHolderName()
    {
        return $this->getData(static::CARD_HOLDER_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setCardHolderName($value)
    {
        return $this->setData(static::CARD_HOLDER_NAME, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCardNumber()
    {
        return $this->getData(static::CARD_NUMBER);
    }

    /**
     * {@inheritDoc}
     */
    public function setCardNumber($value)
    {
        return $this->setData(static::CARD_NUMBER, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCardCvv()
    {
        return $this->getData(static::CARD_CVV);
    }

    /**
     * {@inheritDoc}
     */
    public function setCardCvv($value)
    {
        return $this->setData(static::CARD_CVV, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCardExpirationDate()
    {
        return $this->getData(static::CARD_EXPIRATION_DATE);
    }

    /**
     * {@inheritDoc}
     */
    public function setCardExpirationDate($value)
    {
        return $this->setData(static::CARD_EXPIRATION_DATE, $value);
    }
}
