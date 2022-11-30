<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class ProductRepository
    {
        public static function create(mysqli $con, string $name, string $description, bool $public, int $brandId, int $categoryId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.create.sql'));
            $query->bind_param("ssiii", $name, $description, $public, $brandId, $categoryId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.findAll.sort-id.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function findOneSimple(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.findOneSimple.id.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.findOne.id.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();

            return $row;
        }

        public static function update(mysqli $con, int $id, string $name, string $brandId, string $description, string $categoryId, bool $public): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product.update.sql'));
            $query->bind_param("ssiiii",
                $name,
                $description,
                $public,
                $brandId,
                $categoryId,
                $id,
            );
            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
