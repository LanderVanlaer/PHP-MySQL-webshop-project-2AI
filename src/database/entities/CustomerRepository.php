<?php

    namespace database\entities;

    use Generator;
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

        public static function create(mysqli $con, string $email, string $passwordHash, string $lastname, string $firstname): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.create.sql'));
            $query->bind_param("ssss", $firstname, $lastname, $email, $passwordHash);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.findAll.sort-id.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield validateStringArray($row);

            $query->close();
            $res->close();
        }


        public static function update(mysqli $con, int $id, mixed $firstname, mixed $lastname, string|null $passwordHash, mixed $email): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.update.sql'));
            $query->bind_param("sssi", $firstname, $lastname, $email, $id);
            $val = $query->execute();
            $query->close();

            if (!$val || $passwordHash == null)
                return $val;

            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/customer.update.password.sql'));
            $query->bind_param("si", $passwordHash, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
