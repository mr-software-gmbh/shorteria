<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Database;

class BaseDatabase
{
    protected \PDO $db;

    protected function __construct()
    {
        global $config, $errorMessages;

        $host = $config['database']['host'];
        $dbname = $config['database']['name'];
        $dsn = "mysql:dbname=$dbname;host=$host";
        $user = $config['database']['user'];
        $password = $config['database']['password'];

        try {
            $this->db = new \PDO($dsn, $user, $password);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        } catch (\PDOException $ex) {
            die($errorMessages['invalidDatabaseConnection']);
        }
    }
}
