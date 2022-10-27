<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class SubcategoryRepository
    {
        public static function findAllSortByCategoryId(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/category-subcategory.findAll.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/subcategory.findAll.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/subcategory.findOne.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $row = $res->fetch_assoc();

            $query->close();
            $res->close();
            return $row;
        }

        public static function update(mysqli $con, int $id, string $name): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/subcategory.update.sql'));
            $query->bind_param("si", $name, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        /**
         * @param mysqli $con
         * @param array $data
         * <pre>
         * Array
         * (
         *   [name] => SubcategoryName
         *   [specifications] => Array
         *   (
         *      [0] => Array
         *      (
         *          [name] => specName
         *          [type] => list, boolean, ...
         *          [notation] => {} GB
         *      )
         *   )
         * )
         * @return int
         */
        public static function createWithSpecifications(mysqli $con, array $data): int {
            $id = SubcategoryRepository::create($con, $data["name"]);

            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/subcategory-specifications.create.sql'));


            foreach ($data["specifications"] as $specification) {
                $query->bind_param("isss", $id, $specification["name"], $specification["type"], $specification["notation"]);
                $query->execute();
            }

            $query->close();

            return $id;
        }

        public static function create(mysqli $con, string $name): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/subcategory.create.sql'));
            $query->bind_param("s", $name);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }
    }
