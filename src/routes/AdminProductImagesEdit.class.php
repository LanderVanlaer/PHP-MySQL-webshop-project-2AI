<?php

    namespace routes;

    use database\entities\ProductImagesRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isFileSizeLess;
    use function utils\isImage;
    use function utils\redirect;
    use function utils\saveFile;

    class AdminProductImagesEdit extends Route
    {
        private array $errors = [];
        private array $product = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/productimage\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Product-images {$this->product["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/productimage\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            /** @noinspection DuplicatedCode */
            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            $this->product = ProductRepository::findOneSimple(self::getCon(), $id);

            if (empty($this->product)) return false;


            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender()) {
                redirect("/admin/productimage/$id/edit");
            }

            if (!empty($GLOBALS["GET"]["delete-image-id"])
                    && is_numeric($GLOBALS["GET"]["delete-image-id"])
                    && ProductImagesRepository::delete(self::getCon(), $GLOBALS["GET"]["delete-image-id"], $this->product["id"])) {
                redirect($_SERVER["REDIRECT_URL"]);
            }

            return parent::preRender();
        }

        private function postPreRender(): bool {
            switch ($GLOBALS["POST"]["type"]) {
                case "reorder":
                    if (empty($GLOBALS["POST"]["product-image"]) || !is_array($GLOBALS["POST"]["product-image"])) {
                        $this->errors[] = 0;
                        return false;
                    }

                    $productImages = $GLOBALS["POST"]["product-image"];

                    foreach ($productImages as $img) {
                        if (!is_numeric($img)) {
                            $this->errors[] = 2;
                            return false;
                        }
                    }


                    foreach (ProductImagesRepository::findByProduct(self::getCon(), $this->product["id"]) as $img) {
                        if ($img["order"] != $productImages[$img["id"]]) {
                            ProductImagesRepository::update(self::getCon(), $img["id"], $productImages[$img["id"]]);
                        }
                    }

                    return true;
                case "add":
                    $image = $_FILES["image"];

                    /** @noinspection DuplicatedCode */
                    if (empty($image["tmp_name"])) {
                        $this->errors[] = 0;
                        return false;
                    }

                    if (!isImage($image)) {
                        $this->errors[] = 1500;
                        return false;
                    }

                    if (!isFileSizeLess($image, 1000000)) { //1MB
                        $this->errors[] = 1501;
                        return false;
                    }

                    $fileName = saveFile($image, __DIR__ . "/../images/product");

                    if (!ProductImagesRepository::create(self::getCon(), $this->product["id"], $fileName)) {
                        $this->mysqlError = mysqli_error(self::getCon());
                        return false;
                    }

                    return true;
                default:
                    $this->errors[] = 0;
                    return false;
            }
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/product-image/edit.css">
            <script src="/static/js/admin/product-image/images.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>
                Edit Product-Images
                <a href="/admin/product/<?= $this->product["id"] ?>/edit">
                    <?= $this->product["name"] ?>
                    (<?= $this->product["id"] ?>)
                </a>
            </h1>
            <nav class="links">
                <ul>
                    <li>
                        <a class="btn-blue" href="/admin/product/<?= $this->product["id"] ?>/edit">
                            Product
                        </a>
                    </li>
                    <li>
                        <a class="btn-blue" href="/admin/properties/<?= $this->product["id"] ?>/edit">
                            Properties
                        </a>
                    </li>
                    <li class="active">
                        <a class="btn-blue" href="/admin/productimage/<?= $this->product["id"] ?>/edit">
                            Product Images
                        </a>
                    </li>
                </ul>
            </nav>
            <form action="#" method="POST">
                <input type="hidden" name="type" value="reorder">
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
                <div id="product-images">
                    <?php foreach (ProductImagesRepository::findByProduct(self::getCon(), $this->product["id"]) as $img): //id path order product_id?>
                        <div class="product-image-wrapper">
                            <a href="?delete-image-id=<?= $img["id"] ?>"
                               onclick="return confirm('Are you sure you want to delete this image?')">
                                <svg class="w-6 h-6 trash-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                            <div class="product-image">
                                <img src="/images/product/<?= $img["path"] ?>" alt="<?= $this->product["name"] ?>">
                            </div>
                            <div class="product-image-order-wrapper">
                                <label>
                                    <input class="product-image-order-number" type="number" readonly
                                           name="product-image[<?= $img["id"] ?>]" value="<?= $img["order"] ?>">
                                </label>
                                <button class="product-image-order-button" data-type="up" type="button">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <button class="product-image-order-button" data-type="down" type="button">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
            <form action="#" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Add Image</legend>
                    <input type="hidden" name="type" value="add">
                    <label class="vertical">
                        <span class="required">Image:</span>
                        <input type="file" name="image" id="image" required>
                    </label>
                    <button type="submit" class="btn-blue">Submit</button>
                </fieldset>
            </form>
        <?php }
    }
