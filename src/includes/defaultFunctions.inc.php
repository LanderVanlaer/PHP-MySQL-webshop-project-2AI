<?php

    namespace utils;

    /**
     * The file extensions that are allowed to be used for uploading photos (images)
     */
    const UPLOAD_IMAGE_EXTENSIONS = array('jpg', 'jpeg', 'png', 'svg', 'jfif');

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

    /**
     * Check whether a given file is of type image
     *
     * @param $file array A file object
     * @return bool whether the file is an image
     * @see UPLOAD_IMAGE_EXTENSIONS
     */
    function isImage($file) {
        $extension = getFileExtension($file);
        return in_array($extension, UPLOAD_IMAGE_EXTENSIONS);
    }

    /**
     * Returns the extension of a file
     *
     * @param array $file A file object
     * @return string The extension of the file
     */
    function getFileExtension(array $file): string {
        $f = explode(".", $file["name"]);
        return end($f);
    }

    /**
     * Says whether the file is less than or equal to the given bytes.
     *
     * @param array $file a file object
     * @param int $bytes The number of bytes the file should be less than or equal to
     * @return bool whether the given array is a file and whether the file is smaller than or equal to the given bytes
     */
    function isFileSizeLess(array $file, int $bytes): bool {
        return $file['size'] <= $bytes;
    }

    /**
     * Stores a file, on the hard drive
     *
     * @param array $file The file to be saved
     * @param string $d The directory to which the file is to be saved
     * @return string The new name of the file
     */
    function saveFile(array $file, string $d): string {
        $fileNameEx = getFileExtension($file);
        $newFileName = uniqid('', true) . '.' . strtolower($fileNameEx);
        $fileDestination = "$d/$newFileName";
        move_uploaded_file($file['tmp_name'], $fileDestination);
        return $newFileName;
    }

    /**
     * Checks whether the given password can be used. If not, gets the errors as error-id {@link array}
     *
     * @param $p string The password to be tested
     * @return int[] Returns an array of all errors, if there are no errors an empty array;
     */
    function passwordPossible(string $p): array {
        $arr = array();
        if (!preg_match("/.{8,32}/", $p))
            $arr[] = 1601;
        if (!preg_match("/[a-z]/", $p))
            $arr[] = 1602;
        if (!preg_match("/[A-Z]/", $p))
            $arr[] = 1603;
        if (!preg_match("/\d/", $p))
            $arr[] = 1604;
        return $arr;
    }
