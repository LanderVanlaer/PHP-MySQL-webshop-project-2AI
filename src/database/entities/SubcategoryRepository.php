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
    }
