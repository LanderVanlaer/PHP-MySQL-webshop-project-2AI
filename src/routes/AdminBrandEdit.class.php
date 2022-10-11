<?php

    namespace routes;

    use database\entities\BrandRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isFileSizeLess;
    use function utils\isImage;
    use function utils\redirect;
    use function utils\saveFile;

    class AdminBrandEdit extends Route
    {
        private array $errors = [];
        private array $brand = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/brand\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Brand {$this->brand["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/brand\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/brand");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (empty($GLOBALS["POST"]["name"])) {
                $this->errors[] = 0;
                return false;
            }

            $brandName = $GLOBALS["POST"]["name"];

            switch ($_POST["type"]) {
                case "existing":
                    if (empty($GLOBALS["POST"]["logo-image"])) {
                        $this->errors[] = 0;
                        return false;
                    }

                    $logoImageName = $GLOBALS["POST"]["logo-image"];

                    if (!in_array($logoImageName, BrandRepository::getAllBrandImages())) {
                        $this->errors[] = 2;
                        return false;
                    }

                    //file exists

                    if (!BrandRepository::update(self::getCon(), $id, $brandName, $logoImageName)) {
                        $this->mysqlError = mysqli_error(self::getCon());
                        return false;
                    }

                    return true;
                case "upload":
                    $logoImage = $_FILES["logo"];

                    if (empty($logoImage["tmp_name"])) {
                        $this->errors[] = 0;
                        return false;
                    }

                    if (!isImage($logoImage)) {
                        $this->errors[] = 1500;
                        return false;
                    }

                    if (!isFileSizeLess($logoImage, 1000000)) { //1MB
                        $this->errors[] = 1501;
                        return false;
                    }

                    $newFileName = saveFile($logoImage, __DIR__ . "/../images/brand");

                    if (!BrandRepository::update(self::getCon(), $id, $brandName, $newFileName)) {
                        $this->mysqlError = mysqli_error(self::getCon());
                        return false;
                    }

                    return true;
                default:
                    $this->errors[] = 0;
                    return false;
            }
        }

        private function getPreRender(int $id): bool {
            $brand = BrandRepository::findOne(self::getCon(), $id);

            if (empty($brand))
                return false;

            $this->brand = $brand;

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/brand/edit.css">
            <script src="/static/js/admin/brand/edit.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>Edit Brand (<?= $this->brand["id"] ?>)</h1>
            <form action="#" method="POST" enctype="multipart/form-data" name="brand-edit">
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
                    <input type="text" name="name" id="name" value="<?= $this->brand["name"] ?>" required>
                </label>
                <fieldset>
                    <legend>Logo</legend>
                    <div>
                        <input type="radio" name="type" id="logo-type-choose-existing" value="existing" checked>
                        <label for="logo-type-choose-existing">Geüploade afbeelding uitkiezen</label>
                        <input type="radio" name="type" id="logo-type-upload" value="upload">
                        <label for="logo-type-upload">Nieuwe afbeelding uploaden</label>
                    </div>
                    <hr>
                    <div id="logo-type-choose-existing-div">
                        <div>
                            <div>
                                <ul>
                                    <?php foreach (BrandRepository::getAllBrandImages() as $i => $img) { ?>
                                        <li>
                                            <input type="radio" name="logo-image" id="logo-image-<?= $i ?>"
                                                   value="<?= $img ?>" <?= $this->brand["logo"] === $img ? "checked" : null ?>>
                                            <label for="logo-image-<?= $i ?>"><?= $img ?></label>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div>
                                <img src="/images/brand/<?= $this->brand["logo"] ?>" alt="" id="preview">
                            </div>
                        </div>
                    </div>
                    <div id="logo-type-choose-upload-div" style="display: none">
                        <label class="vertical">
                            <span class="required">Logo:</span>
                            <input type="file" name="logo" id="logo">
                        </label>
                    </div>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
