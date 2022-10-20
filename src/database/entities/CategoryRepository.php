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
    }
