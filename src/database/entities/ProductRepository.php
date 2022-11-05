<?php

    namespace database\entities;

    use mysqli;

    class ProductRepository
    {
        public static function create(mysqli $con, string $name, string $description, int $brandId, int $categoryId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.create.sql'));
            $query->bind_param("ssii", $name, $description, $brandId, $categoryId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }
    }
