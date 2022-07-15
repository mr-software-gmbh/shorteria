<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Component;

use App\Model\UrlModel;
use JetBrains\PhpStorm\Pure;

class ShorteriaComponent
{
    protected string $urlCharset = 'pRbl6dnvCBLXc8I0iWsHN-54DAxmzYZFP1SVEJ3k7Uhfg2K9MrtjwyeuaGoTOQq';
    protected array $config;

    public function __construct()
    {
        global $config;
        $this->config = $config;
    }

    public function getUniqueShortUrl(): string
    {
        do {
            $shortUrl = $this->generateUniqueShortUrl();
        } while ($this->checkIfShortUrlExists($shortUrl));

        return $shortUrl;
    }

    private function generateUniqueShortUrl(): string
    {
        $shortUrl = '';

        for ($i = 0; $i < $this->config['shortUrlLength']; $i++) {
            $shortUrl .= substr($this->urlCharset, rand(0, strlen($this->urlCharset) - 1), 1);
        }

        return $shortUrl;
    }

    private function checkIfShortUrlExists(string $shortUrl): bool
    {
        $urlModel = new UrlModel();
        $short = $urlModel->find()->where(['BINARY short', '=', $shortUrl])->execute();

        return !empty($short);
    }

    public function getUrlProtocol(): string
    {
        $protocol = 'http://';
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        }

        return $protocol;
    }

    #[Pure]
    public function getUrlRoot(): string
    {
        return $this->getUrlProtocol() . $_SERVER['HTTP_HOST'];
    }

    #[Pure]
    public function getUri(): string
    {
        return $this->getUrlProtocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
