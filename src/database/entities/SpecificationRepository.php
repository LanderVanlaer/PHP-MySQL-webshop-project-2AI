<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class SpecificationRepository
    {
        public static function findAllBySubcategoryId(mysqli $con, int $id): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.findAll.subcategory_id.sql'));
            $query->bind_param("i", $id);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function update(mysqli $con, int $id, int $subcategoryId, string $name, string $notation): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.update.sql'));
            $query->bind_param("ssii", $name, $notation, $id, $subcategoryId);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function create(mysqli $con, string $name, string $type, $notation, int $subcategoryId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.create.sql'));
            $query->bind_param("isss", $subcategoryId, $name, $type, $notation);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }
    }
