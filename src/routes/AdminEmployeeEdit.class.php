<?php

    namespace routes;

    use database\entities\EmployeeRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\passwordPossible;
    use function utils\redirect;

    class AdminEmployeeEdit extends Route
    {
        private array $errors = [];
        private array $employee = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/employee\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Employee {$this->employee["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/employee\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/employee");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (isOneEmpty(
                    $GLOBALS["POST"]["firstname"],
                    $GLOBALS["POST"]["lastname"],
                    $GLOBALS["POST"]["username"])) {
                $this->errors[] = 0;
                return false;
            }

            $firstname = $GLOBALS["POST"]["firstname"];
            $lastname = $GLOBALS["POST"]["lastname"];
            $username = $GLOBALS["POST"]["username"];
            $password = empty($GLOBALS["POST"]["password"]) ? null : $GLOBALS["POST"]["password"];

            if ($password != null && ($passwordErrors = passwordPossible($password)) && count($passwordErrors) > 0) {
                $this->errors = array_merge($this->errors, $passwordErrors);
                return false;
            }

            //Check if not duplicate
            $duplicatedEmployee = EmployeeRepository::findOneByUsername(self::getCon(), $username);
            if (!empty($duplicatedEmployee) && $duplicatedEmployee["id"] != $id) {
                $this->errors[] = 1000;
                return false;
            }

            if (!EmployeeRepository::update(self::getCon(), $id, $firstname, $lastname, empty($password) ? null : password_hash($password, PASSWORD_DEFAULT), $username)) {
                $this->mysqlError = mysqli_error(self::getCon());
                return false;
            }

            return true;
        }

        private function getPreRender(int $id): bool {
            $employee = EmployeeRepository::findOne(self::getCon(), $id);

            if (empty($employee)) return false;
            $this->employee = $employee;

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Edit Employee (<?= $this->employee["id"] ?>)</h1>
            <form action="#" method="POST" enctype="multipart/form-data">
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
                <fieldset>
                    <legend>Name</legend>
                    <label>
                        <span class="required">firstname:</span>
                        <input type="text" name="firstname" required value="<?= $this->employee["firstname"] ?>">
                    </label>
                    <label>
                        <span class="required">lastname:</span>
                        <input type="text" name="lastname" required value="<?= $this->employee["lastname"] ?>">
                    </label>
                </fieldset>
                <fieldset>
                    <legend>credentials</legend>
                    <label>
                        <span class="required">username:</span>
                        <input type="text" name="username" required value="<?= $this->employee["username"] ?>">
                    </label>
                    <label>
                        <span>password:</span>
                        <input type="password" name="password">
                    </label>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
