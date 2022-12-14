<?php

    namespace routes;

    use database\entities\BrandRepository;
    use database\entities\CategoryRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\redirect;

    class AdminProductEdit extends Route
    {
        private array $errors = [];
        private array $product = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/product\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Product {$this->product["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/product\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/product");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (isOneEmpty(
                    $GLOBALS["POST"]["name"],
                    $GLOBALS["POST"]["brand"],
                    $GLOBALS["POST"]["description"],
                    $GLOBALS["POST"]["category"],
                    $GLOBALS["POST"]["price"])) {
                $this->errors[] = 0;
                return false;
            }

            /** @noinspection DuplicatedCode */
            $name = $GLOBALS["POST"]["name"];
            $brandId = $GLOBALS["POST"]["brand"];
            $description = $GLOBALS["POST"]["description"];
            $categoryId = $GLOBALS["POST"]["category"];
            $public = !empty($GLOBALS["POST"]["public"]);
            $price = $GLOBALS["POST"]["price"];

            if (!is_numeric($brandId) || !is_numeric($categoryId) || !is_numeric($price)) {
                $this->errors[] = 2;
                return parent::preRender();
            }

            $brandId = intval($brandId);
            $categoryId = intval($categoryId);
            $price = floatval($GLOBALS["POST"]["price"]);

            if (!BrandRepository::findOne(self::getCon(), $brandId)
                    || !CategoryRepository::findOne(self::getCon(), $categoryId)) {
                $this->errors[] = 3;
                return parent::preRender();
            }

            if (!ProductRepository::update(self::getCon(), $id, $name, $brandId, $description, $categoryId, $price, $public)) {
                $this->mysqlError = mysqli_error(self::getCon());
                return false;
            }

            return true;
        }

        private function getPreRender(int $id): bool {
            $this->product = ProductRepository::findOne(self::getCon(), $id);

            if (empty($this->product))
                return false;

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Edit Product (<?= $this->product["id"] ?>)</h1>
            <nav class="links">
                <ul>
                    <li class="active">
                        <a class="btn-blue" href="/admin/product/<?= $this->product["id"] ?>/edit">
                            Product
                        </a>
                    </li>
                    <li>
                        <a class="btn-blue" href="/admin/properties/<?= $this->product["id"] ?>/edit">
                            Properties
                        </a>
                    </li>
                    <li>
                        <a class="btn-blue" href="/admin/productimage/<?= $this->product["id"] ?>/edit">
                            Product Images
                        </a>
                    </li>
                </ul>
            </nav>
            <form action="#" method="POST">
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
                <label>
                    <span class="required">Name:</span>
                    <input type="text" name="name" id="name" value="<?= $this->product["name"] ?>" required>
                </label>
                <label>
                    <span class="required">Brand:</span>
                    <select name="brand" id="brand" required>
                        <?php foreach (BrandRepository::findAll(self::getCon()) as $brand): ?>
                            <option
                                    value="<?= $brand["id"] ?>"
                                    <?=
                                        $brand["id"] == $this->product["brand_id"]
                                                ? "selected"
                                                : null
                                    ?>>
                                <?= $brand["name"] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span class="required">Price:</span>
                    <input type="number"
                           name="price"
                           min="0.00"
                           max="10000.00"
                           step="0.01"
                           value="<?= $this->product["price"] ?>"
                    />
                </label>
                <label class="vertical">
                    <span class="required">Description:</span>
                    <textarea name="description" id="description" cols="60" rows="10"
                              placeholder="Description..." maxlength="1024"
                              required><?= $this->product["description"] ?></textarea>
                </label>
                <label>
                    Public:
                    <input type="checkbox" <?= empty($this->product["public"]) ? null : "checked" ?>
                           name="public">
                    <span class="checkbox-custom"></span>
                </label>
                <fieldset>
                    <legend>Category</legend>
                    <label class="vertical">
                        <span class="required">Category:</span>
                        <select name="category" id="category" required>
                            <option disabled value="" selected>Choose</option>
                            <?php foreach (CategoryRepository::findAll(self::getCon()) as $category): ?>
                                <option
                                        value="<?= $category["id"] ?>"
                                        <?=
                                            $category["id"] == $this->product["category_id"]
                                                    ? "selected"
                                                    : null
                                        ?>>
                                    <?= $category["name"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
