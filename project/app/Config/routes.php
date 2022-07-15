<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use App\Component\RouterComponent;

$router = new RouterComponent();

try {
    $router->get('/*', 'UrlController@shortRedirect');
    $router->get('/__new', 'UrlController@store');
    $router->post('/__new', 'UrlController@store');
    $router->get('/__details', 'UrlController@details');
    $router->post('/__details', 'UrlController@details');
    $router->get('/__edit', 'UrlController@edit');
    $router->post('/__edit', 'UrlController@edit');

    $router->run();
} catch (Exception $e) {
}
