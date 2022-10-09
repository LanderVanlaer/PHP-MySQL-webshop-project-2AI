<?php

    namespace utils;

    /**
     * Redirect the user to another page
     *
     * @param string $location The path url to redirect the user to
     * @return never
     */
    function redirect(string $location): never {
        header("Location: $location");
        exit();
    }

    /**
     * {@link session_start()} must be executed beforehand
     *
     * @return bool Whether the user is logged in or not
     */
    function isLoggedInAsUser(): bool {
        return !empty($_SESSION["user"]["id"]);
    }

    /**
     * {@link session_start()} must be executed beforehand
     *
     * @return bool Whether the admin is logged in or not
     */
    function isLoggedInAsAdmin(): bool {
        return !empty($_SESSION["admin"]["id"]);
    }

    /**
     * This function makes a string 'safe to use'
     *
     * @param $var string The string that needs to be modified
     * @return string The new 'safe' value
     * @see stripslashes()
     * @see htmlspecialchars()
     * @see trim()
     */
    function validateString(string $var): string {
        return htmlspecialchars(trim(stripslashes($var)));
    }

    function validateStringArray(array $array): array {
        foreach ($array as &$e) {
            if (is_array($e))
                $e = validateStringArray($e);
            elseif (is_string($e))
                $e = validateString($e);
        }

        return $array;
    }

    /**
     * Checks if 1 of the given arguments is empty
     *
     * @param mixed ...$var The variables to check
     * @return bool true if at least 1 is empty, false otherwise
     * @see empty()
     */
    function isOneEmpty(...$var): bool {
        foreach ($var as $i)
            if (empty($i)) return true;
        return false;
    }

    /**
     * Creates an object consisting of the selected keys.
     *
     * @param array $arr Object to destruct
     * @param string ...$keys The selected keys
     * @return array The new object
     */
    function objectPick(array $arr, string  ...$keys): array {
        $newArr = [];

        foreach ($keys as $key)
            if (!empty($arr[$key]))
                $newArr[$key] = $arr[$key];

        return $newArr;
    }
