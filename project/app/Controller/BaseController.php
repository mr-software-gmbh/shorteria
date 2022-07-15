<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Component\CorsComponent;
use App\Model\LogModel;
use App\View\BaseView;

class BaseController extends BaseView
{
    protected array $config;
    protected array $errorMessages;
    protected LogModel $log;

    public function __construct()
    {
        parent::__construct();

        global $config, $errorMessages;
        $this->config = $config;
        $this->errorMessages = $errorMessages;
        $this->log = new LogModel();

        (new CorsComponent())->handle();
    }
}
