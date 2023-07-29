<?php

namespace App\Services\YandexMarket;

use App\Models\Category;
use App\Models\Product;

final class YmlYandex
{
    private array $categories = [];

    public function apply(): string
    {
        $xml = '<yml_catalog date="' . now()->format('c') . '">';
        $xml .= '<shop>';
        $xml .= $this->shop();
        $xml .= $this->categories();
        $xml .= $this->products();
        $xml .= '</shop>';
        $xml .= '</yml_catalog>';

        return $xml;
    }

    public function shop(): string
    {
        $name = config('scraper.shop.name');
        $company = config('scraper.shop.company');
        $phone = config('scraper.shop.phone');

        return "
            <name>$name</name>
            <company>$company</company>
            <phone>$phone</phone>
            <platform>Yandex.YML server</platform>
            <version>1.0</version>
        ";
    }

    public function categories(): string
    {
        $xml = '<categories>';
        foreach (Category::query()->get() as $category) {
            $xml .= sprintf('<category id="%d">%s</category>', $category->id, $category->name);
        }
        $xml .= '</categories>';

        return $xml;
    }

    public function products(): string
    {
        $xml = '<offers>';

        foreach (Product::query()->with('category')->get() as $product) {
            $value = $product->data;
            $xml .= sprintf('<offer id="%d"></offer>', $product->id);
            $xml .= sprintf('<name>%s</name>', $value['title']);
            $xml .= '<available>true</available>';

            if ($value['price'] < $value['base']) {
                $xml .= sprintf('<price>%d</price><oldprice>%d</oldprice>', $value['price'], $value['base']);
            } else {
                $xml .= sprintf('<price>%d</price>', $value['price']);
            }

            $xml .= sprintf('<categoryId>%s</categoryId>', $product->category->id);

            foreach ($value['images'] as $image) {
                $xml .= sprintf('<picture>%s</picture>', $image);
            }

            $xml .= sprintf('<description>%s</description>', rtrim($value['description'], " \t\n\r\0\x0B.") . '. ' . implode('. ', $value['composition']));
        }

        $xml .= '</offers>';

        return $xml;
    }
}
