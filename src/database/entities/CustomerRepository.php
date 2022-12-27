<?php

    namespace database\entities;

    use mysqli;
    use function utils\validateStringArray;

    class CustomerRepository
    {
        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.findOne.id.sql'));
            $query->bind_param('i', $id);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }

        public static function findOneByEmail(mysqli $con, string $email): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.findOne.username.sql'));
            $query->bind_param('s', $email);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }
    }
