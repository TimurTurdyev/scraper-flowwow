<?php


namespace App\Services;

use App\Services\Scraper\ProductScraper;
use Facebook\WebDriver\WebDriver;

class ScrapperFactory
{
    public function getScrapper(): ProductScraper
    {
        return new ProductScraper($this->getDriver());
    }

    public function getDriver(): WebDriver
    {
        return (new ChromeWebDriver())->getDriver();
    }
}
