<?php

    use database\entities\CategoryRepository;
    use database\entities\ProductRepository;

    class Search extends ApiRoute
    {

        public function matchesPath(string $path): bool {
            return $path === "/search";
        }

        public function render(): array {
            $query = $GLOBALS["GET"]["q"];

            if (empty($query))
                return [
                    "categories" => [],
                    "products" => [],
                ];

            return [
                "categories" => iterator_to_array(CategoryRepository::query(self::getCon(), $query)),
                "products" => iterator_to_array(ProductRepository::query(self::getCon(), $query)),
            ];
        }
    }
