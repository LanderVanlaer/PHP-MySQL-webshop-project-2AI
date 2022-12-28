<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use Route;

    class AdminCustomer extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/customer";
        }

        public function getDocumentTitle(): string {
            return "Customer";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Customer</caption>
                <thead>
                    <tr>
                        <th scope="col" rowspan="2">Edit</th>
                        <th scope="col" rowspan="2">id</th>
                        <th scope="colgroup" colspan="2">name</th>
                        <th scope="col" rowspan="2">email</th>
                    </tr>
                    <tr>
                        <th scope="col">first</th>
                        <th scope="col">last</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (CustomerRepository::findAllSortById(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/customer/<?= $row["id"] ?>/edit">edit</a></td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["firstname"] ?></td>
                            <td><?= $row["lastname"] ?></td>
                            <td><?= $row["email"] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }
    }
