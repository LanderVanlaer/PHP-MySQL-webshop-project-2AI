<?php

    namespace routes;

    use database\entities\CategorySubcategoryRepository;
    use Route;

    class AdminCategorySubcategory extends Route
    {
        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/categorysubcategory";
        }

        public function getDocumentTitle(): string {
            return "Category-Subcategory";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <table>
                <caption>Category-Subcategory</caption>
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $currentData = null;

                        foreach (CategorySubcategoryRepository::findAllSortByCategoryId(self::getCon()) as $row) {
                            if (!empty($currentData)) {
                                if ($currentData["category"]["id"] == $row["categoryId"]) {
                                    $currentData["subcategories"][] = ["id" => $row["subcategoryId"], "name" => $row["subcategoryName"],];
                                    continue;
                                }

                                $this->renderCategoryWithSubcategories($currentData);
                            }
                            $currentData = [
                                    "category" => [
                                            "id" => $row["categoryId"],
                                            "name" => $row["categoryName"],
                                    ],
                                    "subcategories" => [
                                            [
                                                    "id" => $row["subcategoryId"],
                                                    "name" => $row["subcategoryName"],
                                            ],
                                    ]
                            ];
                        }

                        $this->renderCategoryWithSubcategories($currentData);
                    ?>
                </tbody>
            </table>
            <a class="btn-blue create" href="/admin/categorysubcategory/create">Create</a>
        <?php }

        private function renderCategoryWithSubcategories(array $data): void {
            foreach ($data["subcategories"] as $i => $subcategory) :?>
                <tr>
                    <?php if ($i == 0) : ?>
                        <td class="edit" rowspan="<?= count($data["subcategories"]) ?>">
                            <a href="/admin/categorysubcategory/<?= $data["category"]["id"] ?>/edit">edit</a>
                        </td>
                        <td rowspan="<?= count($data["subcategories"]) ?>">
                            <a href="/admin/category/<?= $data["category"]["id"] ?>/edit"><?= $data["category"]["name"] ?></a>
                        </td>
                    <?php endif ?>
                    <td>
                        <a href="/admin/subcategory/<?= $subcategory["id"] ?>/edit"><?= $subcategory["name"] ?></a>
                    </td>
                </tr>
            <?php endforeach;
        }
    }
