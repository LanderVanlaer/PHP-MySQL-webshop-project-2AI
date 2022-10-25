<?php

    namespace routes;

    use database\entities\SubcategoryRepository;
    use Exception;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\redirect;

    class AdminSubcategoryCreate extends Route
    {
        private array $errors = [];
        private string $customError = "";

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/subcategory/create";
        }

        public function getDocumentTitle(): string {
            return "Subcategory Create";
        }

        public function preRender(): bool {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") return parent::preRender();
            try {
                $data = $this->parseData($GLOBALS["POST"]);
            } catch (Exception $e) {
                $this->customError = $e->getMessage();
                return parent::preRender();
            }

            foreach ($data["specifications"] as $specification) {
                if (!in_array($specification["type"], ['list', 'boolean', 'number', 'string'])) {
                    $this->customError = "\"type\" has to be of type ['list', 'boolean', 'number', 'string']";
                    return parent::preRender();
                }
            }

            $id = SubcategoryRepository::createWithSpecifications(self::getCon(), $data);

            redirect("/admin/subcategory/$id/edit");
        }

        /**
         * @throws Exception
         */
        private function parseData(array $d): array {
            $data = [];

            if (empty($d["name"]))
                throw new Exception("\"name\" does not exist");

            if (
                    isOneEmpty($d["specification-name"], $d["specification-type"])
                    || gettype($d["specification-name"]) != "array"
                    || gettype($d["specification-type"]) != "array"
                    || (!empty($d["specification-notation"]) && gettype($d["specification-notation"]) != "array")
            )
                throw new Exception("Invalid data");

            $data["name"] = $d["name"];
            $data["specifications"] = [];

            foreach ($d["specification-name"] as $id => $name) {
                $data["specifications"][] = [
                        "name" => $name,
                        "type" => empty($d["specification-type"][$id]) ?
                                throw new Exception("Invalid data") :
                                $d["specification-type"][$id],
                        "notation" => empty($d["specification-notation"][$id]) ? "" : $d["specification-notation"][$id],
                ];
            }

            return $data;
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/admin/subcategory/create.css">
            <script src="/static/js/admin/subcategory/create.js" defer></script>
        <?php }

        public function render(): void { ?>
            <h1>Create new Subcategory</h1>
            <form action="#" method="POST" enctype="multipart/form-data">
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
                    <input type="text" name="name" id="name"
                           value="<?= empty($GLOBALS["POST"]["name"]) ? "" : $GLOBALS["POST"]["name"] ?>"
                           required>
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
                            <?php foreach (
                                    empty($GLOBALS["POST"]["specification-name"]) ?
                                            [0 => ""] :
                                            $GLOBALS["POST"]["specification-name"]
                                    as $key => $specName): ?>
                                <tr>
                                    <td class="edit">
                                        <button type="button" class="delete" data-id="<?= $key ?>">X</button>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <input type="text" maxlength="32" name="specification-name[<?= $key ?>]"
                                               id="specification-name[<?= $key ?>]" value="<?= $specName ?>" required>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <select name="specification-type[<?= $key ?>]"
                                                id="specification-type[<?= $key ?>]" required>
                                            <option value="" <?= !in_array($GLOBALS["POST"]["specification-type"][$key], ["string", "boolean", "number", "list"]) ? "selected" : "" ?>
                                                    disabled>Choose an option
                                            </option>
                                            <option value="string" <?= $GLOBALS["POST"]["specification-type"][$key] == "string" && "selected" ?>>
                                                String
                                            </option>
                                            <option value="boolean" <?= $GLOBALS["POST"]["specification-type"][$key] == "boolean" && "selected" ?>>
                                                Boolean
                                            </option>
                                            <option value="number" <?= $GLOBALS["POST"]["specification-type"][$key] == "number" && "selected" ?>>
                                                Number
                                            </option>
                                            <option value="list" <?= $GLOBALS["POST"]["specification-type"][$key] == "list" && "selected" ?>>
                                                List
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <input type="text" max="255" name="specification-notation[<?= $key ?>]"
                                               id="specification-notation[<?= $key ?>]"
                                               value="<?= $GLOBALS["POST"]["specification-notation"][$key] ?>">
                                    </td>
                                    <td>
                                        <p id="specification-notation-example[<?= $key ?>]"></p>
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
