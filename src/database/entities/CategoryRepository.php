<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class CategoryRepository
    {
        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category.findAll.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function update(mysqli $con, int $id, string $name): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category.update.sql'));
            $query->bind_param("si", $name, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category.findOne.id.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }

        public static function findOneByName(mysqli $con, string $name): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category.findOne.name.sql'));
            $query->bind_param("s", $name);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }

        public static function create(mysqli $con, string $name, array $subcategories): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category.create.sql'));
            $query->bind_param("s", $name);
            $query->execute();
            $query->close();

            $categoryId = $con->insert_id;

            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category-subcategory.create.sql'));
            foreach ($subcategories as $subcategoryId) {
                $query->bind_param("ii", $categoryId, $subcategoryId);
                $query->execute();
            }
            $query->close();

            return $categoryId;
        }
    }
