<?php

    namespace routes;

    use database\entities\CategoryRepository;
    use database\entities\SubcategoryRepository;
    use Route;
    use function utils\getErrors;
    use function utils\redirect;

    class AdminCategorySubcategoryEdit extends Route
    {
        private array $errors = [];
        private array $category = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/categorysubcategory\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Category {$this->category["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/categorysubcategory\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/categorysubcategory");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (empty($GLOBALS["POST"]["subcategories"])) {
                $this->errors[] = 0;
                return false;
            }

            $subCategoryIds = $GLOBALS["POST"]["subcategories"];

            if (!is_array($subCategoryIds)) {
                $this->errors[] = 2;
                return parent::preRender();
            }

            foreach ($subCategoryIds as $subcategoryId) {
                if (!is_numeric($subcategoryId)) {
                    $this->errors[] = 2;
                    return parent::preRender();
                }
            }


            $toAdd = $subCategoryIds;
            $toDelete = [];

            foreach (SubcategoryRepository::findByCategory(self::getCon(), $id) as $subcat) {
                $i = array_search($subcat["id"], $toAdd);

                if (is_bool($i))
                    $toDelete[] = $subcat["id"];
                else
                    unset($toAdd[$i]);
            }


            if (!CategoryRepository::deleteLink(self::getCon(), $id, $toDelete)
                    || !CategoryRepository::addLink(self::getCon(), $id, $toAdd)) {
                $this->mysqlError = mysqli_error(self::getCon());
                return false;
            }

            return true;
        }

        private function getPreRender(int $id): bool {
            $category = CategoryRepository::findOne(self::getCon(), $id);

            if (empty($category))
                return false;

            $this->category = $category;

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/category/subcategories-editor.css">
            <script src="/static/js/admin/category/subcategories-editor.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>Edit Category "<a
                        href="/admin/category/<?= $this->category["id"] ?>/edit"><?= $this->category["name"] ?></a>"
                (<?= $this->category["id"] ?>)</h1>
            <form action="#" method="POST" enctype="multipart/form-data">
                <?php if (count($this->errors) || !empty($this->mysqlError)) { ?>
                    <div class="form-error">
                        <ul>
                            <?php foreach (getErrors($this->errors) as $error) { ?>
                                <li><?= $error ?></li>
                            <?php } ?>

                            <?php if (!empty($this->mysqlError)) { ?>
                                <li><?= $this->mysqlError ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
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
                            <?php foreach (SubcategoryRepository::findAllAndMarkLinkedToCategory(self::getCon(), $this->category["id"]) as $subcategory) : ?>
                            {
                                id: <?= $subcategory["id"] ?>,
                                name: "<?= $subcategory["name"] ?>",
                                selected: <?= $subcategory["selected"] ? "true" : "false" ?>,
                            },
                            <?php endforeach; ?>
                        ];
                    </script>
                    <div id="subcategories-hidden"></div>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
