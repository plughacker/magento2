<?php

namespace PlugHacker\PlugPagamentos\Model;

use PlugHacker\PlugCore\Maintenance\Services\InfoBuilderService;
use PlugHacker\PlugPagamentos\Api\MaintenanceInterface;
use PlugHacker\PlugPagamentos\Concrete\Magento2CoreSetup;

class Maintenance
    implements MaintenanceInterface
{
    /**
     * @param mixed $params
     * @return array
     */
    public function index($params)
    {
        $baseParams = explode ('&', $params);
        $coreParams = [];
        foreach ($baseParams as $baseParam) {

            $pair = explode('=' ,$baseParam);
            $key = array_shift($pair);
            $value = implode('=', $pair);

            $coreParams[$key] = $value;
        }


        Magento2CoreSetup::bootstrap();

        $infoBuilder = new InfoBuilderService();

        $info = $infoBuilder->buildInfoFromQueryArray($coreParams);

        $response = $info;
        if (is_array($info)) {
            $response = json_encode($info, JSON_PRETTY_PRINT);
        }

        echo $response;

        die(0);
    }
}
