<?php

    use function utils\redirect;

    $LOG_DIRECTORY = __DIR__ . "/../log/";

    function errorHandler(int $errNo, string $errMsg, string $file, int $line): void {
        global $LOG_DIRECTORY;
        file_put_contents($LOG_DIRECTORY . date("Ymd") . ".txt", date("Y-m-d H:i:s") . "\t($errNo) $errMsg in $file on line $line" . PHP_EOL, FILE_APPEND);
    }

    set_error_handler("errorHandler");

    set_exception_handler(function (Throwable $exception) {
        errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());

        redirect("/error");
    });
