<?php

    class DatabaseImplementedObject
    {
        private static ?mysqli $con = null;

        /**
         * Get database connection
         *
         * @return mysqli
         */
        public static function getCon(): mysqli {
            if (is_null(self::$con)) {
                self::$con = utils\database\createConnection();
            }
            return self::$con;
        }

        public function __destruct() {
            if (!is_null(self::$con)) {
                try {
                    self::$con->close();
                } catch (Throwable) {
                    //DELETE
                }
            }
        }
    }
