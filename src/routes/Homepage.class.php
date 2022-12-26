<?php

    namespace routes;

    use database\entities\CategoryRepository;
    use Route;
    use function utils\validateUrlValue;

    class Homepage extends Route
    {
        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/homepage.css">
        <?php }


        public function matchesPath(string $path): bool {
            return $path === "";
        }

        public function render(): void { ?>
            <ul class="categories">
                <?php foreach (CategoryRepository::findAll(self::getCon()) as $category) { ?>
                    <li>
                        <a class="btn-blue"
                           href="/products/<?= $category["id"] ?>/<?= validateUrlValue($category["name"]) ?>"><?= $category["name"] ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php }

        public function getDocumentTitle(): string {
            return "Homepage";
        }
    }
