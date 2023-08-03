<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\ScrapperFactory;
use DiDom\Exceptions\InvalidSelectorException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ScrapFlowwowCommand extends Command
{
    protected $signature = 'scrap:flowwow';

    protected $description = 'Scrap flowwow.com';

    /**
     * @throws InvalidSelectorException
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function handle(ScrapperFactory $scrapper): int
    {
        $timer = now();

        $sc = $scrapper->getScrapper();

        $this->info(sprintf('[%s] Начинаем парсинг', now()));
        $sc->visitPage();
        $this->info(sprintf('[%s] Переход на страницу %s', now(), $sc->getShopUrl()));

        $this->info(sprintf('[%s] Поиск ссылок [показать еще]', now()));
        $links = $sc->clickLinks();
        $this->info(sprintf('[%s] %s ссылок [показать еще]', now(), $links));

        $this->info(sprintf('[%s] Поиск id товаров', now()));
        $data = $sc->products();
        $this->info(sprintf('[%s] (Уникальных %s, всего %s товаров) в %s категориях', now(), $data['uniqueIdTotal'], $data['productTotal'], $data['categoryTotal']));

        $this->info(sprintf('[%s] Удаление всех товаров', now()));
        Product::query()->truncate();

        foreach ($data['data'] as $item) {
            $this->info(sprintf('[%s] Получение информации по id товара из [%s] категории', now(), $item['categoryName']));
            $category = Category::query()
                ->where('name', $item['categoryName'])
                ->firstOrNew(['name' => $item['categoryName']]);

            $category->save();

            foreach ($item['products'] as $id) {
                $product = Product::query()->find($id);

                if ($product) {
                    $this->info(sprintf('[%s] Товар [%s] существует, добавлена категория [%s]', now(), $id, $item['categoryName']));
                    $value = $product->data;
                    $value['categories'] = [...$product->data['categories'], ...[$item['categoryName']]];
                    $product->update(['data' => $value, 'category_id' => $category->id]);
                    continue;
                }

                $this->info(sprintf('[%s] Обращение к [%s]', now(), $sc->getProductUrlDetail($id)));

                $value = $sc->productDetail($item['categoryName'], $id);
                (new Product(['id' => $id, 'data' => $value, 'category_id' => $category->id]))->save();
            }
        }

        $this->info(sprintf('[%s] Время затраченное на парсинг %s секунд', now(), now()->diff($timer)->s));
        $this->info(sprintf('[%s] Очистка кеша yml_yandex', now()));

        Cache::forget('yml_yandex');

        return CommandAlias::SUCCESS;
    }
}
