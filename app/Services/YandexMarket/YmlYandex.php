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

        foreach (Product::query()->with('category')->where('yandex', '=', 1)->get() as $product) {
            $value = $product->data;
            $xml .= sprintf('<offer id="%d">', $product->id);
            $xml .= sprintf('<name>%s</name>', $value['title']);
            $xml .= '<available>true</available>';

            if ($value['price'] < $value['old_price']) {
                $price = $value['old_price'] - ((5 / 100) * $value['old_price']);

                if ($value['price'] > $price) {
                    $value['price'] = $price;
                }

                $xml .= sprintf('<price>%d</price><oldprice>%d</oldprice>', $value['price'], $value['old_price']);
            } else {
                $xml .= sprintf('<price>%d</price>', $value['price']);
            }

            if ($value['dimensions']) {
                $xml .= '<dimensions>';
                $xml .= implode('/', $value['dimensions']);
                $xml .= '</dimensions>';
                $xml .= '<weight>1</weight>';
            } else {// Default
                $xml .= '<dimensions>20/20/20</dimensions>';
                $xml .= '<weight>0.3</weight>';
            }

            $xml .= sprintf('<categoryId>%s</categoryId>', $product->category->id);

            foreach ($value['images'] as $image) {
                $xml .= sprintf('<picture>%s</picture>', $image);
            }

            if ($value['video']) {
                $xml .= sprintf('<video>%s</video>', $value['video']);
            }

            $xml .= sprintf('<description>%s</description>', rtrim($value['description'], " \t\n\r\0\x0B."));
            $xml .= '</offer>';
        }

        $xml .= '</offers>';

        return $xml;
    }
}
