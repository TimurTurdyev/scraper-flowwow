<?php

namespace App\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class ChromeWebDriver
{
    protected RemoteWebDriver $driver;

    public function __construct()
    {
        $host = config('scraper.selenium.chrome.host');
        $port = config('scraper.selenium.chrome.port');

        $caps = DesiredCapabilities::firefox();

        $this->driver = RemoteWebDriver::create("{$host}:{$port}", $caps, 3600000,3600000);
    }

    public function getDriver(): RemoteWebDriver
    {
        return $this->driver;
    }
}
