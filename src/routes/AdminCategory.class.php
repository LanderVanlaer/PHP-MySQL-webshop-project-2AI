<?php

    namespace routes;

    use database\entities\CategoryRepository;
    use Route;

    class AdminCategory extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/category";
        }

        public function getDocumentTitle(): string {
            return "Category";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Category</caption>
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>id</th>
                        <th>name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (CategoryRepository::findAllSortById(self::getCon()) as $row) { ?>
                        <tr>
                            <td class="edit"><a href="/admin/category/<?= $row["id"] ?>/edit">edit</a></td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["name"] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/category/create">Create</a>
        <?php }
    }
