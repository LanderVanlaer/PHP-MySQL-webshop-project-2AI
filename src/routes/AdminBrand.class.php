<?php

    namespace routes;

    use database\entities\BrandRepository;
    use Route;

    class AdminBrand extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/brand";
        }

        public function getDocumentTitle(): string {
            return "Brand";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Brand</caption>
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>id</th>
                        <th>name</th>
                        <th>logo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (BrandRepository::findAllSortById(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/brand/<?= $row["id"] ?>/edit">edit</a></td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["name"] ?></td>
                            <td><a href="/images/brand/<?= $row["logo"] ?>"><?= $row["logo"] ?></a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/brand/create">Create</a>
        <?php }
    }
