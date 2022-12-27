<?php

    namespace routes;

    use database\entities\CartRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\redirect;

    class CartAdd extends Route
    {
        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function getDocumentTitle(): string {
            return "Cart add";
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/cart\/add\/\d+/", $path);
        }

        public function preRender(): bool {
            preg_match("/^\/cart\/add\/(\d+)/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if (empty(ProductRepository::findOne(self::getCon(), $id))) {
                return false;
            }

            CartRepository::addOne(self::getCon(), $_SESSION["user"]["id"], $id);

            redirect("/cart");
        }

        public function render(): void {
            //Will never be executed, check preRender() --> redirect
        }
    }
