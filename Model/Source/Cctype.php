<?php

namespace PlugHacker\PlugPagamentos\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return [
            'Visa',
            'Mastercard',
            'Amex',
            'Hipercard',
            'Diners',
            'Elo',
            'Discover',
            'Aura',
            'JCB',
            'Credz',
            'Banese',
            'Cabal'
        ];
    }
}
