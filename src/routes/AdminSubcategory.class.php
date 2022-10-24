<?php

    namespace routes;

    use database\entities\SubcategoryRepository;
    use Route;

    class AdminSubcategory extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/subcategory";
        }

        public function getDocumentTitle(): string {
            return "Subcategory";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Subcategory</caption>
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>Id</th>
                        <th>Subcategory</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (SubcategoryRepository::findAllSortById(self::getCon()) as $row) : ?>
                        <tr>
                            <td class="edit">
                                <a href="/admin/subcategory/<?= $row["id"] ?>/edit">edit</a>
                            </td>
                            <td class="right"><?= $row["id"] ?></td>
                            <td><?= $row["name"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/subcategory/create">Create</a>
        <?php }
    }
