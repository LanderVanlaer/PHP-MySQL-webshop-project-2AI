<?php

    namespace routes;

    use database\entities\BrandRepository;
    use database\entities\CategoryRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\redirect;

    class AdminProductCreate extends Route
    {
        private array $errors = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/product/create";
        }

        public function getDocumentTitle(): string {
            return "Product Create";
        }

        public function preRender(): bool {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") return parent::preRender();

            if (isOneEmpty(
                    $GLOBALS["POST"]["name"],
                    $GLOBALS["POST"]["brand"],
                    $GLOBALS["POST"]["description"],
                    $GLOBALS["POST"]["category"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            $name = $GLOBALS["POST"]["name"];
            $brandId = $GLOBALS["POST"]["brand"];
            $description = $GLOBALS["POST"]["description"];
            $categoryId = $GLOBALS["POST"]["category"];

            if (!is_numeric($brandId) || !is_numeric($categoryId)) {
                $this->errors[] = 2;
                return parent::preRender();
            }

            $brandId = intval($brandId);
            $categoryId = intval($categoryId);

            if (!BrandRepository::findOne(self::getCon(), $brandId) || !CategoryRepository::findOne(self::getCon(), $categoryId)) {
                $this->errors[] = 3;
                return parent::preRender();
            }

            $id = ProductRepository::create(self::getCon(), $name, $description, $brandId, $categoryId);

            redirect("/admin/product/$id/edit");
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Create new Product</h1>
            <form action="#" method="POST">
                <?php if (count($this->errors)) { ?>
                    <div class="form-error">
                        <ul>
                            <?php foreach (getErrors($this->errors) as $error) { ?>
                                <li><?= $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <label>
                    <span class="required">Name:</span>
                    <input type="text" name="name" id="name" required>
                </label>
                <label>
                    <span class="required">Brand:</span>
                    <select name="brand" id="brand" required>
                        <option disabled value="" selected>Choose</option>
                        <?php foreach (BrandRepository::findAll(self::getCon()) as $brand): ?>
                            <option value="<?= $brand["id"] ?>"><?= $brand["name"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="vertical">
                    <span class="required">Description:</span>
                    <textarea name="description" id="description" cols="60" rows="10"
                              placeholder="Description..." maxlength="1024" required></textarea>
                </label>
                <fieldset>
                    <legend>Category</legend>
                    <label class="vertical">
                        <span class="required">Category:</span>
                        <select name="category" id="category" required>
                            <option disabled value="" selected>Choose</option>
                            <?php foreach (CategoryRepository::findAll(self::getCon()) as $category): ?>
                                <option value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
