<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Component;

class ResponseComponent
{
    protected mixed $data;
    protected int $statusCode;

    public function __construct(mixed $data = null, int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;

        return $this;
    }

    public function output(mixed $data = null, int $statusCode = null): static
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->data = $data ?? $this->data;
        $this->statusCode = $statusCode ?? $this->statusCode;
        http_response_code($this->statusCode);
        echo $this->data;

        return $this;
    }

    public function json(mixed $data = null, int $statusCode = null): static
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->data = $data ?? $this->data;
        $this->statusCode = $statusCode ?? $this->statusCode;
        http_response_code($this->statusCode);
        echo json_encode($this->data);

        return $this;
    }
}
