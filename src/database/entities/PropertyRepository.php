<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class PropertyRepository
    {
        public static function create(mysqli $con, int $specificationId, int $productId, string $value): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/property.create.sql'));
            $query->bind_param("sii", $value, $specificationId, $productId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function update(mysqli $con, int $specificationId, int $productId, string $value): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/property.update.sql'));
            $query->bind_param("sii", $value, $specificationId, $productId);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function findAllByProduct(mysqli $con, int $id): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.findAll.product_id.sql'));
            $query->bind_param("ii", $id, $id);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function delete(mysqli $con, int $id): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/property.delete.sql'));

            $query->bind_param("i", $id);

            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
