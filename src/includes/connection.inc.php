<?php

    namespace utils\database;

    use mysqli;

    /**
     * Creates and returns database {@link mysqli} connection
     *
     * @return mysqli
     * @see https://www.php.net/manual/en/mysqlinfo.api.choosing.php
     */
    function createConnection(): mysqli {
        $con = new mysqli("localhost:3306", "application", "password", "webshop");
        if ($con->connect_errno)
            die("Error with connection to database {$con->connect_error}");

        $con->set_charset("utf8mb4");

        return $con;
    }
