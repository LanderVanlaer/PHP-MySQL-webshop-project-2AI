<?php

    namespace database\entities;

    use Generator;
    use mysqli;
    use function utils\isImage;

    class BrandRepository
    {
        public static function getAllBrandImages(): array {
            return array_filter(scandir(__DIR__ . "/../../images/brand"), function ($p) {
                return !preg_match("/^\.\.?$/", $p) && isImage(["name" => $p]);
            });
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.findAll.sort-id.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }


        public static function findAllByCategory(mysqli $con, int $categoryId): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.findAll.category-id.sql'));
            $query->bind_param("i", $categoryId);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function findAll(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.findAll.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }


        public static function findOneByName(mysqli $con, string $name): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.findOne.name.sql'));
            $query->bind_param("s", $name);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }

        public static function create(mysqli $con, string $name, string $path): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.create.sql'));
            $query->bind_param("ss", $name, $path);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function update(mysqli $con, int $id, string $name, string $path): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.update.sql'));
            $query->bind_param("ssi", $name, $path, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/brand.findOne.id.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }
    }
