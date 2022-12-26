<?php

    namespace routes;

    use database\entities\BrandRepository;
    use database\entities\ProductImagesRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\redirect;
    use function utils\validateUrlValue;

    class Product extends Route
    {
        private array $product;
        private array $brand;
        private array $images;

        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/product/style.css">
            <script defer src="/static/js/product/image-chooser.js"></script>
        <?php }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/product\/\d+/", $path);
        }

        public function preRender(): bool {
            preg_match("/^\/product\/(\d+)(?:\/(.*))?/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            $product = ProductRepository::findOne(self::getCon(), $id);
            if (empty($product)) return false;

            if (empty($matches[2]) || validateUrlValue($product["name"]) != urlencode(urldecode($matches[2])))
                redirect("/product/$id/" . validateUrlValue($product["name"]));

            $this->product = $product;
            $this->brand = BrandRepository::findOne(self::getCon(), $this->product["brand_id"]);
            $this->images = iterator_to_array(ProductImagesRepository::findByProduct(self::getCon(), $this->product["id"]));

            return parent::preRender();
        }

        public function render(): void { ?>
            <div class="title-wrapper">
                <div class="product-name">
                    <h1><?= $this->product["name"] ?></h1>
                </div>
                <div class="brand">
                    <img src="/images/brand/<?= $this->brand["logo"] ?>"
                         alt="<?= $this->brand["name"] ?>">
                </div>
            </div>
            <section id="product-wrapper">
                <h2 class="none">Product</h2>
                <div id="product-wrapper-flex">
                    <div id="images-wrapper">
                        <div id="big-image">
                            <?php if (count($this->images) > 0): ?>
                                <img src="/images/product/<?= $this->images[0]["path"] ?>"
                                     alt="<?= $this->product["name"] ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    No Image
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="images">
                            <?php foreach ($this->images as $image): ?>
                                <div tabindex="0">
                                    <img src="/images/product/<?= $image["path"] ?>"
                                         alt="<?= $this->product["name"] ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div id="price-wrapper">
                        <div>
                            <div id="price-with-sign">
                                <span id="sign">&euro;</span>
                                <span id="price"><?= $this->product["price"] ?></span>

                                <button id="like" class="btn-blue">
                                    <img src="/static/images/Icon_love_outline.svg" alt="">
                                    <!-- <img src="/static/images/Icon_love_solid.svg" alt=""> -->
                                </button>
                            </div>
                            <button id="add" class="btn-blue">
                                <img src="/static/images/Icon_basket-white.svg" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <section id="properties-wrapper">
                <h2 class="none">Properties</h2>
            </section>
        <?php }

        public function getDocumentTitle(): string {
            return $this->product["name"];
        }
    }
