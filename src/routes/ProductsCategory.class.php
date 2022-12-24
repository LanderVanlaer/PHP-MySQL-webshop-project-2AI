<?php

    namespace routes;

    use database\entities\BrandRepository;
    use database\entities\CategoryRepository;
    use database\entities\ProductRepository;
    use database\entities\PropertyRepository;
    use database\entities\SpecificationRepository;
    use database\entities\SubcategoryRepository;
    use Route;
    use function utils\redirect;

    class ProductsCategory extends Route
    {
        private array $category;

        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/products/category.css">
        <?php }


        public function matchesPath(string $path): bool {
            return preg_match("/^\/products\/\d+/", $path);
        }

        public function preRender(): bool {
            preg_match("/^\/products\/(\d+)(?:\/(.*))?/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            $category = CategoryRepository::findOne(self::getCon(), $id);
            if (empty($category)) return false;

            if (empty($matches[2]) || urlencode($category["name"]) != $matches[2])
                redirect("/products/$id/" . urlencode($category["name"]));

            $this->category = $category;

            return parent::preRender();
        }


        public function render(): void { ?>
            <h1>Category <?= $this->category["name"] ?></h1>
            <div id="specifications-products-wrapper">
                <section id="specifications">
                    <form>
                        <ul>
                            <li id="brands">
                                <fieldset>
                                    <legend>Brand</legend>
                                    <ul>
                                        <?php foreach (BrandRepository::findAllByCategory(self::getCon(), $this->category["id"]) as $brand): ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox"
                                                           name="brand"
                                                           value="<?= $brand["id"] ?>">
                                                    <span class="checkbox-custom"></span>
                                                    <?= $brand["name"] ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </fieldset>
                            </li>
                            <?php foreach (SubcategoryRepository::findByCategory(self::getCon(), $this->category["id"]) as $subcategory): ?>
                                <li data-subcategory-id="<?= $subcategory["id"] ?>">
                                    <fieldset>
                                        <legend><?= $subcategory["name"] ?></legend>
                                        <ul>
                                            <?php foreach (SpecificationRepository::findAllBySubcategoryId(self::getCon(), $subcategory["id"]) as $specification): ?>
                                                <li data-specification-id="<?= $specification["id"] ?>">
                                                    <span class="name"><?= $specification["name"] ?></span>
                                                    <?php $this->specificationToFilterOption($specification) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </fieldset>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </form>
                </section>
                <section id="products">
                    <ul>
                        <?php foreach (ProductRepository::findAllByCategoryAndThumbnail(self::getCon(), $this->category["id"]) as $product):
                            $link = "/product/{$product["id"]}"; ?>
                            <li data-brand-id="<?= $product["brand_id"] ?>"
                                data-specifications="<?= $this->specificationsToJsonConverter($product["id"]) ?>">
                                <a class="thumbnail-wrapper" href="<?= $link ?>">
                                    <?php if (empty($product["image_path"])): ?>
                                        <div class="no-thumbnail">No image</div>
                                    <?php else: ?>
                                        <img src="/images/product/<?= $product["image_path"] ?>"
                                             alt="<?= $product["name"] ?> thumbnail">
                                    <?php endif; ?>
                                    <div class="product-brand">
                                        <img src="/images/brand/<?= $product["brand_path"] ?>"
                                             alt="<?= $product["brand_name"] ?>">
                                    </div>
                                </a>
                                <a href="<?= $link ?>" class="product-name-description-wrapper">
                                    <p class="product-name"><?= $product["name"] ?></p>
                                    <div class="product-description"><?= $product["description"] ?>...</div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        <?php }

        private function specificationToFilterOption(array $specification) {
            switch ($specification["type"]):
                case "number":
                    [$min, $max] = $this->specificationsMinMaxNumber($specification); ?>
                    <div class="number">
                        <label>
                            <span class="min">Min</span>
                            <input type="number" value="<?= $min ?>" min="<?= $min ?>" max="<?= $max ?>">
                        </label>
                        <label>
                            <span class="max">Max</span>
                            <input type="number" value="<?= $max ?>" min="<?= $min ?>" max="<?= $max ?>">
                        </label>
                    </div>
                    <?php break;
                case "string":
                    $properties = PropertyRepository::findAllBySpecificationAndCategory(self::getCon(), $specification["id"], $this->category["id"]); ?>
                    <div class="string">
                        <ul>
                            <?php if ($properties->key()): ?>
                                <?php foreach ($properties as $property): ?>
                                    <li>
                                        <label>
                                            <input type="checkbox" value="<?= $property["specification_id"] ?>">
                                            <span class="checkbox-custom"></span>
                                            <?= $property["value"] ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="empty">nothing to see here</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php break;
                case "boolean": ?>
                    <div class="boolean">
                        <label>
                            <input type="checkbox"
                                   name="<?= $specification["id"] ?>">
                            <span class="checkbox-custom"></span>
                            <span class="name"><?= $specification["notation"] ?></span>
                        </label>
                    </div>
                    <?php break;
                case "list": ?>
                    <div class="list">
                        <ul>
                            <?php foreach (explode(",", $specification["notation"]) as $listValue): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="" value="<?= $listValue ?>">
                                        <span class="checkbox-custom"></span>
                                        <?= $listValue ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php break;
                default:
            endswitch;
        }

        private function specificationsMinMaxNumber(array $specification): array {
            $properties = PropertyRepository::findAllBySpecificationAndCategory(self::getCon(), $specification["id"], $this->category["id"]);
            $max = -INF;
            $min = INF;
            foreach ($properties as $property) {
                $max = max(floatval($property["value"]), $max);
                $min = min(floatval($property["value"]), $min);
            }

            return [$min, $max];
        }

        private function specificationsToJsonConverter(int $productId): string {
            $specificationsObject = [];

            foreach (PropertyRepository::findAllByProduct(self::getCon(), $productId) as $specification)
                $specificationsObject[$specification["specification_id"]] = $specification["property_value"];

            return htmlspecialchars(json_encode($specificationsObject), ENT_QUOTES);
        }

        public function getDocumentTitle(): string {
            return "Homepage";
        }
    }
