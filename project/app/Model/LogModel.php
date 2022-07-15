<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Model;

class LogModel extends BaseModel
{
    public string $table = 'log';
    protected string $pk = 'id';
    protected array $attributes = [
        'url_id',
        'user_agent',
        'created_at',
    ];

    public function __construct()
    {
        parent::__construct($this->table, $this->pk);
    }

    public function add(int $urlId): bool|int
    {
        $log = $this;
        $log->url_id = $urlId;
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];

        return $log->save();
    }
}
