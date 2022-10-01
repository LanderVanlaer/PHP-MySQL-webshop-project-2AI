<?php
    /** @noinspection PhpIncludeInspection */
    spl_autoload_register(function ($class) {
        if (file_exists("routes/{$class}.class.php")) {
            include_once "routes/{$class}.class.php"; //NOSONAR
        } elseif (file_exists("{$class}.class.php")) {
            include_once "{$class}.class.php"; //NOSONAR
        } elseif (file_exists("{$class}.php")) {
            include_once "{$class}.php"; //NOSONAR
        }
    });
