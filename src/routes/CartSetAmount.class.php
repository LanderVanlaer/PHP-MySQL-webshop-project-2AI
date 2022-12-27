<?php

    namespace routes;

    use database\entities\CartRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\redirect;

    class CartSetAmount extends Route
    {
        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function getDocumentTitle(): string {
            return "Cart Update";
        }

        public function matchesPath(string $path): bool {
            return $path === "/cart/set-amount";
        }

        public function preRender(): bool {
            $amount = $GLOBALS["POST"]["amount"];
            $productId = $GLOBALS["POST"]["product-id"];

            if ((empty($amount) && $amount !== "0") || empty($productId)) return false;

            if (!is_numeric($amount) || !is_numeric($productId)) return false;

            $amount = intval($amount);
            $productId = intval($productId);

            if (empty(ProductRepository::findOne(self::getCon(), $productId))) return false;

            CartRepository::setAmountTo(self::getCon(), $_SESSION["user"]["id"], $productId, $amount);

            redirect("/cart");
        }

        public function render(): void {
            //Will never be executed, check preRender() --> redirect
        }
    }
