<?php

    namespace routes;

    use database\entities\CategoryRepository;
    use database\entities\SubcategoryRepository;
    use Route;
    use function utils\getErrors;
    use function utils\redirect;

    class AdminCategoryCreate extends Route
    {
        private array $errors = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/category/create";
        }

        public function getDocumentTitle(): string {
            return "Category Create";
        }

        public function preRender(): bool {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") return parent::preRender();

            if (empty($GLOBALS["POST"]["name"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            $categoryName = $GLOBALS["POST"]["name"];

            //Check if not duplicate
            if (!empty(CategoryRepository::findOneByName(self::getCon(), $categoryName))) {
                $this->errors[] = 1000;
                return parent::preRender();
            }

            $id = CategoryRepository::create(self::getCon(), $categoryName);

            redirect("/admin/category/$id/edit");
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/category/subcategories-editor.css">
            <script src="/static/js/admin/category/subcategories-editor.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>Create new Category</h1>
            <form action="#" method="POST" enctype="multipart/form-data">
                <?php if (count($this->errors)) { ?>
                    <div class="form-error">
                        <ul>
                            <?php foreach (getErrors($this->errors) as $error) { ?>
                                <li><?= $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <label class="vertical">
                    <span class="required">Name:</span>
                    <input type="text" name="name" id="name" required>
                </label>
                <fieldset>
                    <legend>Subcategories:</legend>
                    <div class="split">
                        <div>
                            <label for="subcategories-search">
                                <input type="search" placeholder="Search" id="subcategories-search"
                                       name="subcategories-search"><img
                                        src="/static/images/Icon_search.svg" alt="">
                            </label>
                            <ul id="subcategories-search-list"></ul>
                        </div>
                        <div>
                            <h2>Selected: <em>(delete)</em></h2>
                            <ul id="subcategories-selected"></ul>
                        </div>
                    </div>
                    <script>
                        const subcategories = [
                            <?php foreach (SubcategoryRepository::findAllSortById(self::getCon()) as $subcategory) : ?>
                            { id: <?= $subcategory["id"] ?>, name: "<?= $subcategory["name"] ?>" },
                            <?php endforeach; ?>
                        ];
                    </script>
                    <div id="subcategories-hidden"></div>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
