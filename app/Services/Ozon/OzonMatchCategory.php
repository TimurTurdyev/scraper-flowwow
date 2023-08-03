<?php

namespace App\Services\Ozon;

class OzonMatchCategory
{
    public function apply(array $categories): array
    {
        $items = [];

        foreach ($categories as $category) {
            foreach ($this->categoriesParse($category['children'], $category['title']) as $value) {
                $items[] = $value;
            }
        }

        return $items;
    }

    private function categoriesParse(array $children, string $pathName): array
    {
        $items = [];

        foreach ($children as $item) {
            $item['title'] = sprintf('%s / %s', $pathName, $item['title']);
            $values = $this->categoriesParse($item['children'], $item['title']);
            if ($values) {
                foreach ($values as $value) {
                    $items[] = $value;
                }
                continue;
            }
            $items[] = $item;
        }

        return $items;
    }
}
