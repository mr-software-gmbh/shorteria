<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Component;

class DebugComponent
{
    public static function dd($data, $output = 'text', $isDie = true)
    {
        if ($output === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        http_response_code(200);

        return $isDie ? die : false;
    }
}
