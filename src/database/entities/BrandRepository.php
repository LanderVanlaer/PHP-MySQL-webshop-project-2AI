<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class BrandRepository
    {
        public static function findAllSortById(mysqli $con): Generator {
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
    }
