<?php

namespace PlugHacker\PlugPagamentos\Api;

interface InstallmentsByBrandManagementInterface
{
    /**
     * @param mixed $brand
     * @return mixed
     */
    public function getInstallmentsByBrand($brand);

}
