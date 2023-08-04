<?php

namespace App\Console\Commands;

use App\Models\OzonCategory;
use App\Services\Ozon\OzonMatchCategory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\HttpClient\Psr18Client;

class OzonCategoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ozon - update the categories and match them to the current ones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = [
            'clientId' => config('scraper.ozon.client_id'),
            'apiKey' => config('scraper.ozon.api_key'),
        ];

        $this->info(sprintf('[%s] Настраиваем клиент Ozon', now()));

        $client = new Psr18Client();
        $categoryService = new \Gam6itko\OzonSeller\Service\V2\CategoryService($config, $client);

        $this->info(sprintf('[%s] Запрос на получение категорий', now()));
        $categories = $categoryService->tree();
        $this->info(sprintf('[%s] Получили %s вложенных категорий', now(), count($categories)));

        file_put_contents('./app/Services/Ozon/categories.json', json_encode($categories, JSON_UNESCAPED_UNICODE));
        $ozonMatchCategory = new OzonMatchCategory();
        $items = $ozonMatchCategory->apply($categories);

        $this->info(sprintf('[%s] Вложенных категорий %s', now(), count($items)));

        $processed = [
            'updated' => 0,
            'created' => 0,
        ];

        foreach ($items as $item) {
            $category = OzonCategory::query()->find($item['category_id']);

            if ($category) {
                if ($item['title'] === $category->name) {
                    $this->info(sprintf('[%s] Категория [%d] существует, имя не изменено', now(), $category->id));
                    continue;
                }
                $this->info(sprintf('[%s] Категория [%d] существует, изменилось название [%s]', now(), $category->id, $item['title']));
                $category->update(['name' => $item['title']]);
                $processed['updated'] += 1;
                continue;
            }

            $this->info(sprintf('[%s] Добавлена новая категория [%d], название [%s]', now(), $item['category_id'], $item['title']));
            $category = new OzonCategory(['id' => $item['category_id'], 'name' => $item['title']]);
            $category->save();
            $processed['created'] += 1;
        }

        $this->info(sprintf('[%s] Процесс закончен обновлено [%d], создано [%d]', now(), $processed['updated'], $processed['created']));

        return CommandAlias::SUCCESS;
    }
}
