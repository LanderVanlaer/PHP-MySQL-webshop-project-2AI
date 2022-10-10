<?php

    namespace routes;

    use database\entities\BrandRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isFileSizeLess;
    use function utils\isImage;
    use function utils\isOneEmpty;
    use function utils\redirect;
    use function utils\saveFile;

    class AdminBrandCreate extends Route
    {
        private array $errors = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/brand/create";
        }

        public function getDocumentTitle(): string {
            return "Brand Create";
        }

        public function preRender(): bool {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") return parent::preRender();

            $logoImage = $_FILES["logo"];

            if (isOneEmpty($GLOBALS["POST"]["name"], $logoImage["tmp_name"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            $brandName = $GLOBALS["POST"]["name"];

            if (!isImage($logoImage)) {
                $this->errors[] = 1500;
                return parent::preRender();
            }

            if (!isFileSizeLess($logoImage, 1000000)) { //1MB
                $this->errors[] = 1501;
                return parent::preRender();
            }

            //Check if not duplicate
            $duplicatedBrand = BrandRepository::findOneByName(self::getCon(), $brandName);
            if (!empty($duplicatedBrand)) {
                $this->errors[] = 1000;
                return parent::preRender();
            }

            $newFileName = saveFile($logoImage, __DIR__ . "/../images/brand");

            $id = BrandRepository::create(self::getCon(), $brandName, $newFileName);

            redirect("/admin/brand/$id/edit");
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Create new Brand</h1>
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
                <label class="vertical">
                    <span class="required">Logo:</span>
                    <input type="file" name="logo" id="logo" required>
                </label>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
