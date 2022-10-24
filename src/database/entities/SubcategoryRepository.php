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
