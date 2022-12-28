<?php

    namespace database\entities;

    use Generator;
    use mysqli;
    use function utils\validateStringArray;

    class OrderProductRepository
    {
        public static function findAll(mysqli $con, int $orderId): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/order-product.findAll.order-id.sql'));
            $query->bind_param('i', $orderId);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield validateStringArray($row);

            $query->close();
            $res->close();
        }
    }
