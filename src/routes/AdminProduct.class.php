<?php

    namespace routes;

    use database\entities\ProductRepository;
    use Route;

    class AdminProduct extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/product";
        }

        public function getDocumentTitle(): string {
            return "Product";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Product</caption>
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>id</th>
                        <th title="public">p</th>
                        <th>name</th>
                        <th>brand</th>
                        <th>category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (ProductRepository::findAllSortById(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/product/<?= $row["product_id"] ?>/edit">edit</a></td>
                            <td class="right"><?= $row["product_id"] ?></td>
                            <td class="center"><?= $row["product_public"] ?></td>
                            <td><?= $row["product_name"] ?></td>
                            <td>
                                <a href="/admin/brand/<?= $row["brand_id"] ?>/edit"><?= $row["brand_name"] ?></a>
                            </td>
                            <td>
                                <a href="/admin/category/<?= $row["category_id"] ?>/edit"><?= $row["category_name"] ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/product/create">Create</a>
        <?php }
    }
