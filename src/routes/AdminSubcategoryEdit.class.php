<?php

    namespace routes;

    use database\entities\SpecificationRepository;
    use database\entities\SubcategoryRepository;
    use Exception;
    use Route;
    use function utils\getErrors;
    use function utils\redirect;

    class AdminSubcategoryEdit extends Route
    {
        private array $errors = [];
        private string $customError = "";
        private array $subcategory = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/subcategory\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Subcategory {$this->subcategory["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/subcategory\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/subcategory");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            try {
                $data = $this->parseData($GLOBALS["POST"]);
            } catch (Exception $e) {
                $this->customError = $e->getMessage();
                return false;
            }

            if (!empty($GLOBALS["POST"]["specification-type"])) {
                foreach ($data["specifications"] as $specification) {
                    if ($specification["id"] < 0 && !in_array($specification["type"], ['list', 'boolean', 'number', 'string'])) {
                        $this->customError = "\"type\" has to be of type ['list', 'boolean', 'number', 'string']";
                        return false;
                    }
                }
            }

            if (!SubcategoryRepository::update(self::getCon(), $id, $data["name"])) {
                $this->customError = mysqli_error(self::getCon());
                return false;
            }

            foreach ($data["specifications"] as $specification) {
                if ($specification["id"] >= 0 ?
                        !SpecificationRepository::update(self::getCon(), $specification["id"], $id, $specification["name"], $specification["notation"])
                        : !SpecificationRepository::create(self::getCon(), $specification["name"], $specification["type"], $specification["notation"], $id)) {
                    $this->customError = mysqli_error(self::getCon());
                    return false;
                }
            }

            foreach ($data["delete"] as $delId)
                if (!SpecificationRepository::delete(self::getCon(), $delId)) {
                    $this->customError = self::getCon()->error;
                    return false;
                }

            redirect("/admin/subcategory");
        }

        /**
         * @throws Exception
         */
        private function parseData(array $d): array {
            $data = [];

            if (empty($d["name"]))
                throw new Exception("\"name\" does not exist");

            if (
                    empty($d["specification-name"])
                    || gettype($d["specification-name"]) != "array"
                    || (!empty($d["specification-notation"]) && gettype($d["specification-notation"]) != "array")
            )
                throw new Exception("Invalid data");

            $data["name"] = $d["name"];
            $data["specifications"] = [];

            foreach ($d["specification-name"] as $id => $name) {
                if (empty($d["specification-type"][$id])) {
                    if ($id < 0) throw new Exception("Invalid data");

                    $type = "";
                } else {
                    $type = $d["specification-type"][$id];
                }


                $data["specifications"][] = [
                        "id" => $id,
                        "name" => $name,
                        "type" => $type,
                        "notation" => empty($d["specification-notation"][$id]) ? "" : $d["specification-notation"][$id],
                ];
            }

            $data["delete"] = [];
            if (!empty($d["specification-delete"]) && gettype($d["specification-delete"] == "array"))
                foreach ($d["specification-delete"] as $delId)
                    if (is_numeric($delId))
                        $data["delete"][] = intval($delId);

            return $data;
        }

        private function getPreRender(int $id): bool {
            $subcategory = SubcategoryRepository::findOne(self::getCon(), $id);
            if (empty($subcategory)) return false;
            $this->subcategory = $subcategory;
            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/subcategory/create.css">
            <script src="/static/js/admin/subcategory/create.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>Edit Subcategory (<?= $this->subcategory["id"] ?>)</h1>
            <form action="#" method="POST" name="edit-form" enctype="multipart/form-data">
                <?php if (count($this->errors) || !empty($this->customError)) { ?>
                    <div class="form-error">
                        <ul>
                            <?php foreach (getErrors($this->errors) as $error) { ?>
                                <li><?= $error ?></li>
                            <?php } ?>

                            <?php if (!empty($this->customError)) { ?>
                                <li><?= $this->customError ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <label class="vertical">
                    <span class="required">Name:</span>
                    <input type="text" name="name" id="name" value="<?= $this->subcategory["name"] ?>" required>
                </label>
                <fieldset>
                    <legend>Specifications</legend>
                    <div class="description">
                        If type is of <em>List</em>,
                        use <em>List values</em> in following the format:
                        <em>"1080p,720p,360p"</em>
                        <hr>
                        Notation: Use <em>{}</em> for value replacement
                    </div>
                    <table id="specifications"> <!-- //NOSONAR -->
                        <thead>
                            <tr>
                                <th>Del</th>
                                <th class="required">Name</th>
                                <th class="required">Type</th>
                                <th colspan="2"><span>Notation</span> / <span class="required">List values</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (SpecificationRepository::findAllBySubcategoryId(self::getCon(), $this->subcategory["id"])
                                           as $specification): ?>
                                <tr>
                                    <td class="edit">
                                        <button type="button" class="delete" data-id="<?= $specification["id"] ?>">X
                                        </button>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <input type="text" maxlength="32"
                                               name="specification-name[<?= $specification["id"] ?>]"
                                               id="specification-name[<?= $specification["id"] ?>]"
                                               value="<?= $specification["name"] ?>" required>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <select id="specification-type[<?= $specification["id"] ?>]" disabled>
                                            <option value="" <?= !in_array($specification["type"], ["string", "boolean", "number", "list"]) ? "selected" : "" ?>
                                                    disabled>Choose an option
                                            </option>
                                            <option value="string" <?= $specification["type"] == "string" && "selected" ?>>
                                                String
                                            </option>
                                            <option value="boolean" <?= $specification["type"] == "boolean" && "selected" ?>>
                                                Boolean
                                            </option>
                                            <option value="number" <?= $specification["type"] == "number" && "selected" ?>>
                                                Number
                                            </option>
                                            <option value="list" <?= $specification["type"] == "list" && "selected" ?>>
                                                List
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <input type="text" max="255"
                                               name="specification-notation[<?= $specification["id"] ?>]"
                                               id="specification-notation[<?= $specification["id"] ?>]"
                                               value="<?= $specification["notation"] ?>">
                                    </td>
                                    <td>
                                        <p id="specification-notation-example[<?= $specification["id"] ?>]"></p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" id="add-row" class="btn-blue">Voeg rij toe</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <template id="specification-template">
                        <tr>
                            <td class="edit">
                                <button type="button" class="delete">X</button>
                            </td>
                            <td>
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <input type="text" name="specification-name" id="specification-name" required>
                            </td>
                            <td>
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <select name="specification-type" id="specification-type" required>
                                    <option value="" selected disabled>Choose an option</option>
                                    <option value="string">String</option>
                                    <option value="boolean">Boolean</option>
                                    <option value="number">Number</option>
                                    <option value="list">List</option>
                                </select>
                            </td>
                            <td>
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <input type="text" name="specification-notation" id="specification-notation">
                            </td>
                            <td>
                                <p id="specification-notation-example"></p>
                            </td>
                        </tr>
                    </template>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
