<?php

namespace PlugHacker\PlugPagamentos\Api;

interface ChargeApiInterface
{

    /**
     * @param string $id
     * @return PlugHacker\PlugPagamentos\Model\Api\ResponseMessage
     */
    public function cancel($id);
}
