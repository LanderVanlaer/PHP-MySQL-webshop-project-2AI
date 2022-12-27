<?php
    /** @noinspection PhpIncludeInspection */
    spl_autoload_register(function ($class) {
        if (file_exists("routes/{$class}.class.php")) {
            include_once "routes/{$class}.class.php"; //NOSONAR
        } elseif (file_exists("{$class}.class.php")) {
            include_once "{$class}.class.php"; //NOSONAR
        } elseif (file_exists("{$class}.php")) {
            include_once "{$class}.php"; //NOSONAR
        } elseif (file_exists("database/entities/{$class}.php")) {
            include_once "database/entities/{$class}.php"; //NOSONAR
        } elseif (file_exists("api/{$class}.class.php")) {
            include_once "api/{$class}.class.php"; //NOSONAR
        }
    });

    require_once __DIR__ . "/includes/defaultFunctions.inc.php"; //NOSONAR
    require_once __DIR__ . "/includes/connection.inc.php"; //NOSONAR
    require_once __DIR__ . "/includes/error.inc.php"; //NOSONAR
    require_once __DIR__ . "/includes/errorHandler.inc.php"; //NOSONAR
