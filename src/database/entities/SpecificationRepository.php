<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class SpecificationRepository
    {
        public static function findAllBySubcategoryId(mysqli $con, int $id): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.findAll.subcategory_id.sql'));
            $query->bind_param("i", $id);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function update(mysqli $con, int $id, int $subcategoryId, string $name, string $notation): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.update.sql'));
            $query->bind_param("ssii", $name, $notation, $id, $subcategoryId);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function create(mysqli $con, string $name, string $type, $notation, int $subcategoryId): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.create.sql'));
            $query->bind_param("isss", $subcategoryId, $name, $type, $notation);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function delete(mysqli $con, int $id): bool {
            $query1 = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification.delete.sql'));
            $query2 = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/specification-property.delete.sql'));

            $query1->bind_param("i", $id);
            $query2->bind_param("i", $id);

            $val1 = $query1->execute();
            $val2 = $query2->execute();
            $query1->close();
            $query2->close();

            return $val1 && $val2;
        }

        public static function specificationTypeToHtmlInput(string $type, string|null $value, string|null $name, string $list = ''): void {
            switch ($type):
                case "number": ?>
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input type="number" value="<?= $value ?>" name="<?= $name ?>" id="<?= $name ?>" step="any">
                    <?php break;
                case "string": ?>
                    <input type="text" value="<?= $value ?>" name="<?= $name ?>" id="<?= $name ?>">
                    <?php break;
                case "boolean": ?>
                    <label>
                        <input type="checkbox" <?= empty($value) ? null : "checked" ?>
                               name="<?= $name ?>"
                               id="<?= $name ?>">
                        <span class="checkbox-custom"></span>
                    </label>
                    <?php break;
                case "list":
                    $listValues = explode(",", $list); ?>
                    <select name="<?= $name ?>" id="<?= $name ?>">
                        <option selected label=" "></option>
                        <?php foreach ($listValues as $listValue): ?>
                            <option value="<?= $listValue ?>" <?= $value == $listValue ? "selected" : null ?>><?= $listValue ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php break;
                default:
            endswitch;
        }
    }
