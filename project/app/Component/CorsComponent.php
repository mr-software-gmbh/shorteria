<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Component;

class CorsComponent
{
    protected array $corsConfig;

    public function __construct()
    {
        global $corsConfig;
        $this->corsConfig = $corsConfig;
    }

    public function handle()
    {
        header('Access-Control-Allow-Origin: ' . $this->corsConfig['allowOrigin'] ?? '*');
        $allowMethods = $this->arrayToString($this->corsConfig['allowMethods']) ?? 'GET, POST, PUT, PATCH';
        header('Access-Control-Allow-Methods: ' . $allowMethods);
        $allowHeaders = $this->arrayToString($this->corsConfig['allowHeaders']) ?? 'Content-Type, Authorization';
        header('Access-Control-Allow-Headers: ' . $allowHeaders);
        $exposeHeaders = $this->arrayToString($this->corsConfig['exposeHeaders']) ?? 'Content-Length, Content-Range';
        header('Access-Control-Expose-Headers: ' . $exposeHeaders);
        header('Access-Control-Max-Age: ' . $this->corsConfig['maxAge'] ?? 600);
    }

    private function arrayToString(array $data): string
    {
        return implode(',', $data);
    }
}
