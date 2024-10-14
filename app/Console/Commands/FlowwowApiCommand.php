<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\FlowwowApiParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FlowwowApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flowwow:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение всех товаров из Flowwow API';

    /**
     * Execute the console command.
     */
    public function handle(FlowwowApiParser $parser): int
    {
        $timer = now();
        $this->info(sprintf('[%s] Начинаем парсинг', now()));

        $contents = $parser->contents();
        $this->info(sprintf('[%s] Получили категорий %s, продуктов %s', now(), count($contents['categories']), count($contents['products'])));

        $categoryExists = Category::query()->pluck('name', 'id')->toArray();
        $productExists = Product::query()
            ->select(['id', DB::raw("JSON_EXTRACT(data, '$.title') AS title")])
            ->pluck(DB::raw('title'), 'id')
            ->toArray();

        foreach ($contents['categories'] as $category) {
            if (array_key_exists($category['id'], $categoryExists)) {
                unset($categoryExists[$category['id']]);
            }

            Category::query()->updateOrCreate(['id' => $category['id']], ['name' => $category['name']]);
            $this->info(sprintf('[%s] Обновили категорию %d - %s', now(), $category['id'], $category['name']));
        }

        foreach ($contents['products'] as $product) {
            if (array_key_exists($product['id'], $productExists)) {
                unset($productExists[$product['id']]);
            }

            Product::query()->updateOrCreate(['id' => $product['id']], $product);
            $this->info(sprintf('[%s] Обновили продукт %d - %s', now(), $product['id'], $product['data']['title']));
        }

        if (!empty($categoryExists)) {
            Category::query()->whereIn('id', array_keys($categoryExists))->delete();
            foreach ($categoryExists as $id => $name) {
                $this->info(sprintf('[%s] Удалили категорию %d - %s', now(), $id, $name));
            }
        }

        if (!empty($productExists)) {
            Product::query()->whereIn('id', array_keys($productExists))->delete();
            foreach ($productExists as $id => $title) {
                $this->info(sprintf('[%s] Удалили продукт %d - %s', now(), $id, $title));
            }
        }

        $this->info(sprintf('[%s] Парсинг закончен %s', now(), $timer->diffForHumans()));

        return CommandAlias::SUCCESS;
    }
}
