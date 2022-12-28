<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isOneEmpty;
    use function utils\passwordPossible;
    use function utils\redirect;

    class AdminCustomerEdit extends Route
    {
        private array $errors = [];
        private array $customer = [];
        private string $mysqlError;

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin\/customer\/\d+\/edit$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Customer {$this->customer["name"]} edit";
        }

        public function preRender(): bool {
            preg_match("/^\/admin\/customer\/(\d+)\/edit/", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            if ($_SERVER["REQUEST_METHOD"] !== "GET" && $this->postPreRender($id)) {
                redirect("/admin/customer");
            }

            return $this->getPreRender($id);
        }

        private function postPreRender(int $id): bool {
            if (isOneEmpty(
                    $GLOBALS["POST"]["firstname"],
                    $GLOBALS["POST"]["lastname"],
                    $GLOBALS["POST"]["email"])) {
                $this->errors[] = 0;
                return false;
            }

            $firstname = $GLOBALS["POST"]["firstname"];
            $lastname = $GLOBALS["POST"]["lastname"];
            $email = $GLOBALS["POST"]["email"];
            $active = !empty($GLOBALS["POST"]["active"]);
            $password = empty($GLOBALS["POST"]["password"]) ? null : $GLOBALS["POST"]["password"];

            if ($password != null && ($passwordErrors = passwordPossible($password)) && count($passwordErrors) > 0) {
                $this->errors = array_merge($this->errors, $passwordErrors);
                return false;
            }

            //Check if not duplicate
            $duplicatedCustomer = CustomerRepository::findOneByEmail(self::getCon(), $email);
            if (!empty($duplicatedCustomer) && $duplicatedCustomer["id"] != $id) {
                $this->errors[] = 1000;
                return false;
            }

            if (!CustomerRepository::update(self::getCon(), $id, $firstname, $lastname, $active, empty($password) ? null : password_hash($password, PASSWORD_DEFAULT), $email)) {
                $this->mysqlError = mysqli_error(self::getCon());
                return false;
            }

            return true;
        }

        private function getPreRender(int $id): bool {
            $customer = CustomerRepository::findOne(self::getCon(), $id);

            if (empty($customer)) return false;
            $this->customer = $customer;

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void { ?>
            <h1>Edit Customer (<?= $this->customer["id"] ?>)</h1>
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
                    <label class="vertical">
                        <span class="required">firstname:</span>
                        <input type="text" name="firstname" required value="<?= $this->customer["firstname"] ?>">
                    </label>
                    <label class="vertical">
                        <span class="required">lastname:</span>
                        <input type="text" name="lastname" required value="<?= $this->customer["lastname"] ?>">
                    </label>
                </fieldset>
                <fieldset>
                    <legend>credentials</legend>
                    <label class="vertical">
                        <span class="required">email:</span>
                        <input type="text" name="email" required value="<?= $this->customer["email"] ?>">
                    </label>
                    <label class="vertical">
                        <span>password:</span>
                        <input type="password" name="password">
                    </label>
                    <label class="vertical">
                        Active: <input type="checkbox" <?= $this->customer["active"] ? "checked" : "" ?>
                                       name="active">
                        <span class="checkbox-custom"></span>
                    </label>
                </fieldset>
                <button type="submit" class="btn-blue">Submit</button>
            </form>
        <?php }
    }
