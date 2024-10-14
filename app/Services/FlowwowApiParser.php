<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class FlowwowApiParser
{
    private int $shopId;
    private array $headers = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:131.0) Gecko/20100101 Firefox/131.0',
        'Accept' => 'application/json',
        'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3'
    ];

    public function __construct()
    {
        $this->shopId = config('scraper.flowwow_shop_id');
    }

    public function contents(): array
    {
        $categories = $this->categories();
        $products = $this->products(array_keys($categories));

        return [
            'categories' => $categories,
            'products' => $products,
        ];
    }

    private function categories(): array
    {
        $response = Http::withHeaders($this->headers)
            ->retry(3, $this->retryFunc())
            ->get("https://apis.flowwow.com/apiuser/shop/categories/?shopId={$this->shopId}&lang=ru");

        if ($response->failed()) {
            dd($response->headers(), $response->body());
        }

        $categories = [];

        foreach ($response->json('data') as $category) {
            $categories[$category['id']] = [
                'id' => $category['id'],
                'name' => $category['name'],
            ];
        }

        return $categories;
    }

    private function products(array $categories): array
    {
        $page = 1;
        $products = [];

        while (true) {
            $property = urlencode(sprintf('{"owner_shop_ids":[%d],"range_type_ids":[%s],"currency":"RUB","city":1}', $this->shopId, implode(',', $categories)));
            $property .= sprintf('&lang=ru&currency=RUB&limit=60&filters={}&page=%s', $page);

            $response = Http::withHeaders($this->headers)
                ->retry(3, $this->retryFunc())
                ->get("https://apis.flowwow.com/apiuser/products/search/?property=$property");

            if ($response->failed()) {
                dd($response->headers(), $response->body());
            }

            $responseProduct = $response->json('data');
            $total = (int)$responseProduct['total'];
            $currentTotal = $page * 60;

            $page++;

            foreach ($responseProduct['items'] as $product) {
                $productDetail = $this->productDetail($product['id']);
                $products[] = $productDetail;
            }

            if ($currentTotal >= $total) {
                break;
            }
        }

        return $products;
    }

    private function productDetail(int $id): array
    {
        $response = Http::withHeaders($this->headers)
            ->retry(3, $this->retryFunc())
            ->get("https://apis.flowwow.com/apiuser/products/info/?id={$id}&city_id=1&lang=ru&currency=RUB&locale=ru");

        if ($response->failed()) {
            dd($response->headers(), $response->body());
        }

        $productDetail = $response->json('data');

        $photos = [];

        foreach ($productDetail['photos'] as $photo) {
            $photos[] = $photo['url'];
        }

        $dimensions = [];

        foreach ($productDetail['size'] as $value) {
            $dimensions[] = $value;
        }

        $description = [];

        foreach ($productDetail['rangeProperties'] as $property) {
            $title = $property['title'] . ': ';
            $desc = [];
            foreach ($property['items'] as $value) {
                $desc[] = $value;
            }
            $separator = ', ';

            if ($property['type'] === 'description') {
                $separator = '. ';
            }

            $description[] = $title . str_replace(['.,', '..'], ',', implode($separator, $desc));
        }

        return [
            'id' => $productDetail['id'],
            'category_id' => $productDetail['range_type_id'],
            'data' => [
                'title' => $productDetail['name'],
                'category_id' => $productDetail['range_type_id'],
                'price' => $productDetail['price'],
                'old_price' => $productDetail['old_price'],
                'video' => $productDetail['video'],
                'images' => $photos,
                'dimensions' => $dimensions,
                'description' => implode(PHP_EOL, $description),
            ],
        ];
    }

    private function retryFunc(): \Closure
    {
        return function (int $attempt, \Exception $exception) {
            return $attempt * 100;
        };
    }
}
