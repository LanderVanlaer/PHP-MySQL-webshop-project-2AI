<?php

    namespace routes;

    use database\entities\OrderRepository;
    use Route;

    class AdminOrder extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/order";
        }

        public function getDocumentTitle(): string {
            return "Orders";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Order</caption>
                <thead>
                    <tr>
                        <th>Details</th>
                        <th>id</th>
                        <th>creation date</th>
                        <th>customer</th>
                        <th>amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (OrderRepository::findAllSortedByDate(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/order/<?= $row["id"] ?>/details">Details</a></td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["creationDate"] ?></td>
                            <td><?= $row["customer_id"] ?></td>
                            <td><?= $row["amount"] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }
    }
