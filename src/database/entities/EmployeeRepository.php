<?php

    namespace database\entities;

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
    }
