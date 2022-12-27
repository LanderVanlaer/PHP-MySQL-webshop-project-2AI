<?php

    namespace database\entities;

    use mysqli;
    use function utils\validateStringArray;

    class CartRepository
    {
        public static function addOne(mysqli $con, int $customerId, int $productId): bool {
            $cartRow = self::findByProduct($con, $customerId, $productId);

            if (empty($cartRow)) {
                return self::setAmountTo($con, $customerId, $productId, 1, false, false);
            } else {
                return self::setAmountTo($con, $customerId, $productId, $cartRow["amount"] + 1, false, true);
            }
        }

        public static function findByProduct(mysqli $con, int $customerId, int $productId): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/cart.findOne.customer-id,product-id.sql'));
            $query->bind_param('ii', $customerId, $productId);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }

        public static function setAmountTo(mysqli $con, int $customerId, int $productId, int $amount, bool $checkDuplicate = true, bool $update = true): bool {
            if (($checkDuplicate && !empty(self::findByProduct($con, $customerId, $productId))) || (!$checkDuplicate && $update)) {
                $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/cart.update.sql'));
            } else {
                $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/cart.create.sql'));
            }

            $query->bind_param("iii", $amount, $customerId, $productId);
            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
