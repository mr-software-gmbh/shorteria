<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * Defined paths.
 */
require __DIR__ . '/paths.php';

/**
 * Application configs.
 */
$errorMessages = require CONFIG_PATH . 'error_messages.php';
$configFilePath = ROOT . DS . 'config.php';
if (!file_exists($configFilePath)) {
    die($errorMessages['configError']);
}
$config = require $configFilePath;
$corsConfig = $config['cors'] ?? [];

/*
 * Autoload classes
 */
spl_autoload_register(function ($className) {
    $classSrc = 'app';
    $className = $classSrc . substr($className, 3);
    $fileName = sprintf('%s%s%s.php', ROOT, DS, str_replace('\\', '/', $className));

    if (file_exists($fileName)) {
        require $fileName;
    }
});

/**
 * Load routes.
 */
require_once CONFIG_PATH . 'routes.php';
