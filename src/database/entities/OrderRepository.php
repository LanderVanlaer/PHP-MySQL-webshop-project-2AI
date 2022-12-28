<?php

    namespace database\entities;

    use Generator;
    use mysqli;
    use function utils\validateStringArray;

    class OrderRepository
    {
        public static function create(mysqli $con, int $customerId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order.create.sql'));
            $query->bind_param("i", $customerId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function findAll(mysqli $con, int $customerId): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order.findAll.customer-id.sql'));
            $query->bind_param('i', $customerId);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield validateStringArray($row);

            $query->close();
            $res->close();
        }

        public static function findAllSortedByDate(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order.findAll.sort-date.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield validateStringArray($row);

            $query->close();
            $res->close();
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order.findOne.id.sql'));
            $query->bind_param('i', $id);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }
    }
