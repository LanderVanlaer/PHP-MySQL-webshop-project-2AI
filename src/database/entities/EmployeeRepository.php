<?php

    namespace database\entities;

    use Generator;
    use mysqli;
    use function utils\validateStringArray;

    class EmployeeRepository
    {
        public static function findOneByUsername(mysqli $con, string $username): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.findOne.username.sql'));
            $query->bind_param('s', $username);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }


        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.findOne.id.sql'));
            $query->bind_param('i', $id);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return validateStringArray($row);
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.findAll.sort-id.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield validateStringArray($row);

            $query->close();
            $res->close();
        }

        public static function create(mysqli $con, string $firstname, string $lastname, string $password, string $username): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.create.sql'));
            $query->bind_param("ssss", $firstname, $lastname, $password, $username);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function update(mysqli $con, int $id, mixed $firstname, mixed $lastname, string|null $passwordHash, mixed $username): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.update.sql'));
            $query->bind_param("sssi", $firstname, $lastname, $username, $id);
            $val = $query->execute();
            $query->close();

            if (!$val || $passwordHash == null)
                return $val;

            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.update.password.sql'));
            $query->bind_param("si", $passwordHash, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }
    }
