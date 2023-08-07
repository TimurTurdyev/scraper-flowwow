<?php

namespace App\Services\Ozon;

use App\Models\Product;

final class YmlOzon
{
    public function apply(): string
    {
        $xml = '<yml_catalog date="' . now()->format('c') . '">';
        $xml .= '<shop>';
        $xml .= $this->products();
        $xml .= '</shop>';
        $xml .= '</yml_catalog>';

        return $xml;
    }

    public function products(): string
    {
        $xml = '<offers>';

        foreach (Product::query()->where('ozon', '=', 1)->get() as $product) {
            $value = $product->data;
            $xml .= sprintf('<offer id="%d">', $product->id);

            if ($value['price'] < $value['base']) {
                $xml .= sprintf('<price>%d</price><oldprice>%d</oldprice>', $value['price'], $value['base']);
            } else {
                $xml .= sprintf('<price>%d</price>', $value['price']);
            }
            $xml .= sprintf('<outlets><outlet instock="%d"></outlet></outlets>', 100);

            $xml .= '</offer>';
        }

        $xml .= '</offers>';

        return $xml;
    }
}
