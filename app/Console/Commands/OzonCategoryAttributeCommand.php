<?php

namespace App\Console\Commands;

use App\Models\OzonCategory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\HttpClient\Psr18Client;

class OzonCategoryAttributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:category-attribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find attribute of category';

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
        $categories = OzonCategory::query()
            ->whereHas('categories')
            ->get();//8229

        foreach ($categories as $category) {
            $values = $categoryService->attributeValues($category->id, '8229');
            dd($values);
        }

        return CommandAlias::SUCCESS;
    }
}
