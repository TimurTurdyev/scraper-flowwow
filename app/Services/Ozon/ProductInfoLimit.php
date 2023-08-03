<?php

namespace App\Services\Ozon;


class ProductInfoLimit extends \Gam6itko\OzonSeller\Service\AbstractService
{
    private $path = '/v4/product/info/limit';

    public function infoLimit()
    {
        return $this->request('POST', $this->path);
    }

}
