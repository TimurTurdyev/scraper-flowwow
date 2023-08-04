<?php

namespace App\Services\Ozon;

class OzonMatchCategory
{
    private array $categories = [];

    public function apply(array $categories): array
    {
        $this->categoriesParse($categories);
        return $this->categories;
    }

    private function categoriesParse(array $categories, string $pathName = ''): void
    {
        foreach ($categories as $item) {
            $currentPath = $item['title'];

            if ($pathName) {
                $currentPath = sprintf('%s / %s', $pathName, $currentPath);
            }

            $item['title'] = $currentPath;

            if (!$item['children']) {
                $this->categories[] = $item;
                continue;
            }

            $this->categoriesParse($item['children'], $item['title']);
        }
    }
}
