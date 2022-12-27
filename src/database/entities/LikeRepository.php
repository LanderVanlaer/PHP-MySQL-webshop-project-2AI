<?php

    namespace database\entities;

    use mysqli;
    use function utils\validateStringArray;

    class LikeRepository
    {
        public static function toggle(mysqli $con, int $customerId, int $productId): void {
            if (empty(self::findOne($con, $customerId, $productId)))
                self::create($con, $customerId, $productId);
            else
                self::delete($con, $customerId, $productId);
        }

        public static function findOne(mysqli $con, int $customerId, int $productId): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/like.findOne.sql'));
            $query->bind_param("ii", $customerId, $productId);
            $query->execute();

            $res = $query->get_result();
            $data = $res->fetch_assoc();

            $query->close();
            $res->close();

            return validateStringArray($data);
        }

        public static function create(mysqli $con, int $customerId, int $productId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/like.create.sql'));
            $query->bind_param("ii", $customerId, $productId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function delete(mysqli $con, int $customerId, int $productId): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/like.delete.sql'));
            $query->bind_param("ii", $customerId, $productId);
            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
