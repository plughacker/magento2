<?php
namespace PlugHacker\PlugPagamentos\Api;

use PlugHacker\PlugPagamentos\Api\Data\CreditCardInterface;

interface CreditCardManagementInterface
{
    /**
     * @param CreditCardInterface $creditCard
     * @return mixed
     */
    public function getCreditCardToken(CreditCardInterface $creditCard);
}
