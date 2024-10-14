<?php

namespace App\Services;

use Gam6itko\OzonSeller\ProductValidator;
use Gam6itko\OzonSeller\Service\V2\ProductService;
use Gam6itko\OzonSeller\Utils\ArrayHelper;

class FixProductImportService extends ProductService
{
    protected $path = '/v3/product';

    public function import(array $income, bool $validateBeforeSend = true)
    {
        if (!array_key_exists('items', $income)) {
            $income = $this->ensureCollection($income);
            $income = ['items' => $income];
        }

        $income = ArrayHelper::pick($income, ['items']);

        if ($validateBeforeSend) {
            $pv = new ProductValidator('create', 2);
            foreach ($income['items'] as &$item) {
                $item = $pv->validateItem($item);
            }
        }

        return $this->request('POST', "{$this->path}/import", $income);
    }
}
