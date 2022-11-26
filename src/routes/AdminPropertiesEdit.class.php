<?php

    namespace routes;

    use database\entities\ProductRepository;
    use database\entities\PropertyRepository;
    use database\entities\SpecificationRepository;
    use Exception;
    use Route;
    use function utils\getErrors;
    use function utils\redirect;

    class AdminPropertiesEdit extends Route
    {
        private array $errors = [];
        private array $product = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/properties\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Properties Product {$this->product["name"]} edit";
        }

        /**
         * @throws Exception
         */
        public function preRender(): bool {
            preg_match("/^\/admin\/properties\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            $this->product = ProductRepository::findOneSimple(self::getCon(), $id);

            if (empty($this->product)) {
                return false;
            }

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender()) {
                redirect("/admin/product/$id/edit");
            }

            return parent::preRender();
        }

        /**
         * @throws Exception
         */
        private function postPreRender(): bool {
//            Array
//            (
//                    [properties] => Array
//                    (
//                    [29] => 10
//                    [27] =>
//                    [30] =>
//                    [28] =>
//                    [8] =>
//                    [9] => on
//                    [10] =>
//                    [11] =>
//                    [12] => test
//                )
//            )

            if (empty($GLOBALS["POST"]["properties"]) || !is_array($GLOBALS["POST"]["properties"])) {
                $this->errors[] = 0;
                return false;
            }

            $properties = $GLOBALS["POST"]["properties"];

            foreach (PropertyRepository::findAllByProduct(self::getCon(), $this->product["id"]) as $prop) {
                if (isset($properties[$prop["specification_id"]])) {
                    $value = $properties[$prop["specification_id"]];
                    $delete = false;
                    //validation checks | skip if error
                    switch ($prop["specification_type"]) {
                        case "number":
                            $delete = !is_numeric($value);
                            break;
                        case "boolean":
                            $delete = empty($value);
                            $value = "1";
                            break;
                        case "list":
                            if (empty($prop["specification_notation"])) {
                                $delete = true;
                                break;
                            }

                            //check if in list
                            $list = explode(",", $prop["specification_notation"]);
                            $exists = false;
                            foreach ($list as $item)
                                if ($item == $value) $exists = true;
                            $delete = !$exists;

                            break;
                        case "string": //skip
                            $delete = empty($properties[$prop["specification_id"]]);
                            break;
                        default:
                            throw new Exception("Database invalid for specification of id {$prop["specification_id"]}");
                    }

                    if ($delete) {
                        if (!is_null($prop["property_id"]) && !PropertyRepository::delete(self::getCon(), $prop["property_id"])) {
                            $this->mysqlError = mysqli_error(self::getCon());
                            return false;
                        }
                    } elseif (is_null($prop["property_id"])) {
                        PropertyRepository::create(
                                self::getCon(),
                                $prop["specification_id"],
                                $this->product["id"],
                                $value,
                        );
                    } else {
                        if (!PropertyRepository::update(
                                self::getCon(),
                                $prop["specification_id"],
                                $this->product["id"],
                                $value,
                        )) {
                            $this->mysqlError = mysqli_error(self::getCon());
                            return false;
                        }
                    }
                } else {
                    //not in POST
                    if (!is_null($prop["property_id"]) && !PropertyRepository::delete(self::getCon(), $prop["property_id"])) {
                        $this->mysqlError = mysqli_error(self::getCon());
                        return false;
                    }
                }
            }
            return true;
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/table.css">
        <?php }

        public function render(): void { ?>
            <h1>
                Edit Product-Properties
                <a href="/admin/product/<?= $this->product["id"] ?>/edit">
                    <?= $this->product["name"] ?>
                    (<?= $this->product["id"] ?>)
                </a>
            </h1>
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
                <table class="no-alternate-coloring">
                    <caption>Properties</caption>
                    <thead>
                        <tr>
                            <th scope="col" rowspan="2">Subcategory</th>
                            <th scope="colgroup" colspan="2">Specifcation</th>
                            <th scope="col" rowspan="2">Property</th>
                            <th scope="col" rowspan="2">Notation</th>
                        </tr>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $cache = [];
                            $subcategoryId = -1;
                            foreach (PropertyRepository::findAllByProduct(self::getCon(), $this->product["id"]) as $spec):
                                if ($spec["subcategory_id"] != $subcategoryId):
                                    $this->printRows($cache);
                                    $cache = [$spec];
                                    $subcategoryId = $spec["subcategory_id"];
                                else:
                                    $cache[] = $spec;
                                endif;
                            endforeach;
                            $this->printRows($cache);
                        ?>
                    </tbody>
                </table>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }

        private function printRows(array $sp) {
            foreach ($sp as $i => $s): ?>
                <tr>
                    <?php if ($i == 0): ?>
                        <td rowspan="<?= count($sp) ?>">
                            <a href="/admin/subcategory/<?= $s["subcategory_id"] ?>/edit">
                                <?= $s["subcategory_name"] ?>
                            </a>
                        </td>
                    <?php endif; ?>
                    <td><?= $s["specification_name"] ?></td>
                    <td><?= $s["specification_type"] ?></td>
                    <td>
                        <?php SpecificationRepository::specificationTypeToHtmlInput(
                                $s["specification_type"],
                                $s["property_value"],
                                "properties[{$s["specification_id"]}]",
                                $s["specification_notation"]
                        ) ?>
                    </td>
                    <td><?= $s["specification_notation"] ?></td>
                </tr>
            <?php endforeach;
        }
    }
