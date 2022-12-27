<?php

    namespace database\entities;

    use mysqli;

    class OrderRepository
    {
        public static function create(mysqli $con, int $customerId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order.create.sql'));
            $query->bind_param("i", $customerId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }
    }
