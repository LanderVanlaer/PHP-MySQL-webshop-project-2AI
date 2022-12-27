<?php

    use database\entities\LikeRepository;
    use function utils\isLoggedInAsUser;

    class Like extends ApiRoute
    {
        public function matchesPath(string $path): bool {
            return "/like" == $path;
        }

        /**
         * @throws Exception
         */
        public function render(): array {
            session_start();

            if (!isLoggedInAsUser()) {
                throw new Exception("You need to be logged in");
            }

            if (empty($GLOBALS["POST"]["product-id"]) || !is_numeric($GLOBALS["POST"]["product-id"]))
                throw new Exception("product-id can not be empty");

            $productId = intval($GLOBALS["POST"]["product-id"]);

            LikeRepository::toggle(self::getCon(), $_SESSION["user"]["id"], $productId);

            return [
                "product-id" => $productId,
                "like" => !empty(LikeRepository::findOne(self::getCon(), $_SESSION["user"]["id"], $productId)),
            ];
        }
    }
