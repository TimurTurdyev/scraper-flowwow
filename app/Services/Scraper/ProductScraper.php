<?php

namespace App\Services\Scraper;

use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

final class ProductScraper
{
    private string $shopUrl;
    private string $productUrl = 'https://flowwow.com/moscow/data/getProductInfo/?id={product_id}&from=direct&lang=ru&currency=RUB';
    protected WebDriver $driver;

    public function getShopUrl()
    {
        return $this->shopUrl;
    }

    public function __construct(WebDriver $driver)
    {
        $this->shopUrl = config('scraper.flowwow_shop');
        $this->driver = $driver;
    }

    public function getProductUrlDetail($id): string
    {
        return str_replace('{product_id}', $id, $this->productUrl);
    }

    public function visitPage(): void
    {
        $this->driver->navigate()->to($this->shopUrl);
    }

    public function products(): array
    {
        $data = [
            'productTotal' => 0,
            'uniqueIdTotal' => 0,
            'categoryTotal' => 0,
            'data' => []
        ];

        $idExists = [];

        $elements = $this->driver->findElements(WebDriverBy::cssSelector('.shop-category .category-wrapper'));

        foreach ($elements as $index => $element) {
            $categoryName = $element->findElement(WebDriverBy::cssSelector('.category-name'))->getText();

            $data['categoryTotal'] += 1;

            $data['data'][$index] = [
                'categoryName' => $categoryName,
                'products' => [],
            ];

            $products = $element->findElements(WebDriverBy::cssSelector('.product-card'));

            foreach ($products as $product) {
                $id = $product->getAttribute('data-id');

                if ($id) {
                    $data['productTotal'] += 1;
                    $data['data'][$index]['products'][] = $id;

                    if (!isset($idExists[$id])) {
                        $data['uniqueIdTotal'] += 1;
                    }

                    $idExists[$id] = 1;
                }
            }
        }

        return $data;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function clickLinks(): int
    {
        $links = 0;

        $this->driver->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('.shop-category'))
        );

        while (true) {
            $elements = $this->driver->findElements(WebDriverBy::cssSelector('.shop-category .category-wrapper > .wrapper > button'));

            if (!count($elements)) {
                break;
            }

            foreach ($elements as $element) {
                $element->click();
                $links++;
            }
        }

        return $links;
    }

    /**
     * @throws InvalidSelectorException
     */
    public function productsDetail(string $categoryName, array $items): array
    {
        $products = [];

        foreach ($items as $id) {
            $product = $this->productDetail($categoryName, $id);
            if (!$product) {
                continue;
            }

            $products[$id] = $product;
        }

        return $products;
    }

    /**
     * @throws InvalidSelectorException
     */
    public function productDetail(string $categoryName, int $id): ?array
    {
        $productUrl = $this->getProductUrlDetail($id);
        $value = $this->driver->get($productUrl)->findElement(WebDriverBy::tagName('pre'))->getText();
        $data = json_decode($value, true)['data'] ?? null;

        if (!$data) {
            return null;
        }

        $images = [];
        $videos = [];

        foreach ($data['photos'] as $photo) {
            if ($photo['img'] ?? null) {
                $images[] = $photo['img'];
                continue;
            }

            if ($photo['html'] ?? null) {
                $video = preg_replace('/.+href="(.+?)".+/', '$1', $photo['html']);
                if ($video) {
                    $videos[] = $video;
                }
                continue;
            }
        }

        $price = $data['cost'] ?? $data['base'] ?? 0;
        $basePrice = $data['base'] ?? $price;

        $fullInfo = str_replace('\n', '', $data['fullInfo'] ?? '');

        $dom = new Document($fullInfo);;

        $title = str($dom->first('.pp-title')->text())->trim()->value();

        $composition = [];

        $dimensions = [];

        foreach ($dom->find('.product-desc-line:not(.product-desc-line-mobile)') as $node) {
            $compositionTitle = str($node->first('.title')->text())->trim()->value();

            $descHtml = $node->first('.desc')->innerHtml();

            $compositionDescription = str($descHtml)
                ->replaceMatches('/<a href.+<\/a>/', '')
                ->replace([
                    PHP_EOL, '<br>', '<br/>', '<span class="hide">', '</span>', '  ', ',.'
                ], [
                    '', ', ', ', ', '', '', ' ', ','
                ])
                ->rtrim(',')
                ->trim();

            if (str($compositionTitle)->contains('размер', true)) {
                $dimensions = str($compositionDescription)
                    ->trim(',')
                    ->explode(',')
                    ->map(fn($v) => preg_replace('/[^0-9]/', '', $v))
                    ->filter(fn($v) => $v);
                if ($dimensions->count()) {
                    $dimensions->add($dimensions->last());
                }
            }

            $composition[] = sprintf('%s: %s', $compositionTitle, $compositionDescription);
        }

        $description = str($dom->first('.product-describe')?->text())->trim()->value();

        return [
            'categories' => [$categoryName],
            'title' => $title,
            'price' => $price,
            'dimensions' => $dimensions,
            'base' => $basePrice,
            'images' => $images,
            'videos' => $videos,
            'description' => $description,
            'composition' => $composition,
            'url' => str($title)->slug('-', 'ru')->value(),
        ];
    }

    public function __destruct()
    {
        $this->driver->close();
    }
}
