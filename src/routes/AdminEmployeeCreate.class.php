<?php

    namespace routes;

    use database\entities\EmployeeRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\passwordPossible;
    use function utils\redirect;

    class AdminEmployeeCreate extends Route
    {
        private array $errors = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/employee/create";
        }

        public function getDocumentTitle(): string {
            return "Employee Create";
        }

        public function preRender(): bool {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") return parent::preRender();

            if (isOneEmpty(
                    $GLOBALS["POST"]["firstname"],
                    $GLOBALS["POST"]["lastname"],
                    $GLOBALS["POST"]["username"],
                    $GLOBALS["POST"]["password"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            $firstname = $GLOBALS["POST"]["firstname"];
            $lastname = $GLOBALS["POST"]["lastname"];
            $username = $GLOBALS["POST"]["username"];
            $password = $GLOBALS["POST"]["password"];

            if (($passwordErrors = passwordPossible($password)) && count($passwordErrors) > 0) {
                $this->errors = array_merge($this->errors, $passwordErrors);
                return parent::preRender();
            }

            //Check if not duplicate
            $duplicatedEmployee = EmployeeRepository::findOne(self::getCon(), $username);
            if (!empty($duplicatedEmployee)) {
                $this->errors[] = 1000;
                return parent::preRender();
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $id = EmployeeRepository::create(self::getCon(), $firstname, $lastname, $passwordHash, $username);

            redirect("/admin/employee/$id/edit");
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Create new Employee</h1>
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
                <fieldset>
                    <legend>Name</legend>
                    <label>
                        <span class="required">firstname:</span>
                        <input type="text" name="firstname" required>
                    </label>
                    <label>
                        <span class="required">lastname:</span>
                        <input type="text" name="lastname" required>
                    </label>
                </fieldset>
                <fieldset>
                    <legend>credentials</legend>
                    <label>
                        <span class="required">username:</span>
                        <input type="text" name="username" required>
                    </label>
                    <label>
                        <span class="required">password:</span>
                        <input type="password" name="password" required>
                    </label>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
