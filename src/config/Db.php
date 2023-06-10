<?php

namespace App\Config;

use \PDO;

/**
* Db class for database connection.
*
* @return mixed
*/

class Db
{
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'slim_user_rest';

    public function connect()
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}