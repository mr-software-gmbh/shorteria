<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Model;

class UrlModel extends BaseModel
{
    public string $table = 'url';
    protected string $pk = 'id';
    protected array $attributes = [
        'short',
        'redirect_to',
        'comment',
        'created_at',
        'deactivated_at',
    ];

    public function __construct()
    {
        parent::__construct($this->table, $this->pk);
    }
}
