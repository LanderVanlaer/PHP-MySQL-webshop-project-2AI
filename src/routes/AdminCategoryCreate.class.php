<?php

    namespace routes;

    use database\entities\CategoryRepository;
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
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
