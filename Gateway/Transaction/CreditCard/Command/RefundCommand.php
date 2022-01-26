<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\CreditCard\Command;

use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Command\AbstractApiCommand;

class RefundCommand extends AbstractApiCommand
{
    /**
     * @param $request
     * @return mixed
     */
    protected function sendRequest($request)
    {
        if (!isset($request)) {
            throw new \InvalidArgumentException('Plug Request object should be provided');
        }
        return $request;
    }

}


