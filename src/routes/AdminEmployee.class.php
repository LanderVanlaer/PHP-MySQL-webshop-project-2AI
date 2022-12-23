<?php

    namespace routes;

    use database\entities\EmployeeRepository;
    use Route;

    class AdminEmployee extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/employee";
        }

        public function getDocumentTitle(): string {
            return "Employee";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Employee</caption>
                <thead>
                    <tr>
                        <th scope="col" rowspan="2">Edit</th>
                        <th scope="col" rowspan="2">id</th>
                        <th scope="colgroup" colspan="3">name</th>
                    </tr>
                    <tr>
                        <th scope="col">first</th>
                        <th scope="col">last</th>
                        <th scope="col">user</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (EmployeeRepository::findAllSortById(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/employee/<?= $row["id"] ?>/edit">edit</a></td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["firstname"] ?></td>
                            <td><?= $row["lastname"] ?></td>
                            <td><?= $row["username"] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/employee/create">Create</a>
        <?php }
    }
