<?php

    namespace utils;

    $xml = simplexml_load_file(__DIR__ . "/../resources/errors.xml") or die("Error: Cannot read errors.xml");
    /** @noinspection PhpArrayUsedOnlyForWriteInspection */
    $errors = array();

    foreach ($xml->children() as $error) {
        $errors[intval($error["id"])] = $error;
    }

    function getErrors(array $errCodes): array {
        $arr = [];

        foreach ($errCodes as $errCode)
            $arr[] = getError($errCode);

        return $arr;
    }

    function getError(int $errCode): string|int {
        global $errors;

        return empty($errors[$errCode]) ? $errCode : validateString($errors[$errCode]);
    }
