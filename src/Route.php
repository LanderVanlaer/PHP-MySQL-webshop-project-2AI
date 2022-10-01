<?php

    abstract class Route
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
                self::$con->close();
            }
        }

        abstract public function matchesPath(string $path): bool;

        abstract public function getDocumentTitle(): string;

        /**
         * Render HTML that should be in `<main>` tag
         *
         * ```html
         * <html>
         *   <head> ... </head>
         *   <body>
         *     <Header/>
         *     <main>
         *       === RENDERS HERE ===
         *     </main>
         *     <Footer/>
         *   </body>
         * </html>
         * ```
         *
         * @see Route::preRender()
         * @see Route::renderHead()
         */
        abstract public function render(): void;

        /**
         * Render HTML that should be in `<head>` tag
         * <p>This is the place where you can add stylesheets and JavaScript files</p>
         *
         * ```html
         * <html>
         *   <head>
         *     ...
         *     === RENDERS HERE ===
         *   </head>
         *   <body> ... </body>
         * </html>
         * ```
         *
         * @see Route::preRender()
         * @see Route::render()
         */
        public function renderHead(): void {
        }

        /**
         * This function is executed before {@link render} & {@link renderHead}
         *
         * Says whether the page should return an error 404.
         * For example if the dynamic route data isn't valid
         *
         * You can use the {@link $_SERVER} variable
         * ```
         * [REQUEST_METHOD] => GET
         * [REDIRECT_URL] => /abc/dev
         * [REQUEST_URI] => /abc/dev?query=123&one=two
         * [QUERY_STRING] => query=123&one=two
         * ```
         *
         * @return bool `true` => runs the rest of the program, `false` => return error 404 page
         * @see $_SERVER
         * @see Route::render()
         * @see Route::renderHead()
         */
        public function preRender(): bool {
            return true;
        }
    }
