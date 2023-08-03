<?php

namespace App\Console\Commands;

use App\Models\Product;
use Gam6itko\OzonSeller\Service\V2\ProductService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpClient\Psr18Client;

class ProductImportOzonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:product-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product import to Ozon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(sprintf('[%s] Начинаем import в Ozon', now()));

        $config = [
            'clientId' => config('scraper.ozon.client_id'),
            'apiKey' => config('scraper.ozon.api_key'),
            //'host' => 'http://cb-api.ozonru.me/'
        ];

        $this->info(sprintf('[%s] Настраиваем клиент Ozon', now()));
        $client = new Psr18Client();
        $productService = new ProductService($config, $client);

        $this->info(sprintf('[%s] Всего (%d) товаров', now(), Product::query()->count()));

        Product::query()->with('category')->chunk(100, function (Collection $items, $index) use ($productService) {
            $this->info(sprintf('[%s] Получаем (%d-%d) товаров', now(), $index, $items->count()));
            $products = [];
            /**
             * @var int $i
             * @var  Product $item
             */
            foreach ($items as $i => $item) {
                $value = $item->data;
                $category = $item->category;
                if (!$category || !$category->ozon_category_id) {
                    continue;
                }

                $images = [];

                foreach ($value['images'] as $index => $image) {
                    $images[] = $image;
                }

                $description = $value['description'];

                if ($value['composition']) {
                    $description .= implode(' / ', $value['composition']);
                }

                $products[$i] = [
                    'description' => $description,
                    'category_id' => $category->ozon_category_id,
                    'name' => $value['title'],
                    'offer_id' => $item->id,
                    'price' => (string)$value['price'],
                    'vat' => '0',
                    'vendor' => 'ArenaFlowers',
                    'height' => (string)($value['dimensions'][0] ?? 10),
                    'depth' => (string)($value['dimensions'][1] ?? 10),
                    'width' => (string)($value['dimensions'][2] ?? 10),
                    'dimension_unit' => 'cm',
                    'weight' => '1000',
                    'weight_unit' => 'g',
                    'images' => $images,
                    'attributes' => [
                        [
                            'id' => 9048,
                            'value' => 'ArenaFlowers',
                        ],
                    ],
                ];
                if ($item['base'] > $item['price']) {
                    $products[$i]['old_price'] = $item['base'];
                }
            }

            if (!$products) {
                $this->info(sprintf('[%s] Нету товаров для отправки', now()));
                return;
            }

            $this->info(sprintf('[%s] Отправляем %d товаров', now(), count($products)));
            $response = $productService->import($products);
            $this->info(sprintf('[%s] Ответ %s', now(), $response['task_id'] ?? ''));
        });
    }
}
