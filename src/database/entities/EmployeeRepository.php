<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class EmployeeRepository
    {
        public static function findOne(mysqli $con, string $username): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.findOne.username.sql'));
            $query->bind_param('s', $username);
            $query->execute();
            $res = $query->get_result();
            $row = $res->fetch_assoc();
            $query->close();
            $res->close();

            return $row;
        }

        public static function findAllSortById(mysqli $con): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/employee.findAll.sort-id.sql'));
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }
    }
