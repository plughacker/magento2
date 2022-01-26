<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\Billet\Command;


use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\Command\AbstractApiCommand;

use PlugHacker\PlugAPILib\Models\CreateOrderRequest;

class AuthorizeCommand extends AbstractApiCommand
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
