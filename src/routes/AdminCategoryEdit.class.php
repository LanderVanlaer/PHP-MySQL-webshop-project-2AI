<?php

    namespace routes;

    use database\entities\CategoryRepository;
    use Route;
    use function utils\getErrors;
    use function utils\redirect;

    class AdminCategoryEdit extends Route
    {
        private array $errors = [];
        private array $category = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/category\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Category {$this->category["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/category\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/category");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (empty($GLOBALS["POST"]["name"])) {
                $this->errors[] = 0;
                return false;
            }

            if (!CategoryRepository::update(self::getCon(), $id, $GLOBALS["POST"]["name"])) {
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
        <?php }

        public function render(): void { ?>
            <h1>Edit Category (<?= $this->category["id"] ?>)</h1>
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
                <label class="vertical">
                    <span class="required">Name:</span>
                    <input type="text" name="name" id="name" value="<?= $this->category["name"] ?>" required>
                </label>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
