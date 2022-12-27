<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isLoggedInAsUser;
    use function utils\isOneEmpty;
    use function utils\passwordPossible;
    use function utils\redirect;

    class UserRegisterPage extends Route
    {
        private array $errors = [];
        private array $customerData = [
                "email" => "",
                "lastname" => "",
                "firstname" => "",
        ];

        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <link rel="stylesheet" href="/static/css/user/register.css">
            <script defer src="/static/js/user/register/validation.js"></script>
        <?php }


        public function getDocumentTitle(): string {
            return "User Register";
        }

        public function matchesPath(string $path): bool {
            return $path === "/user/register";
        }

        public function preRender(): bool {
            session_start();

            if (isLoggedInAsUser())
                redirect("/user");

            if ($_SERVER["REQUEST_METHOD"] !== "POST")
                return parent::preRender();

            $this->customerData = [
                    "email" => empty($GLOBALS["POST"]["email"]) ? "" : $GLOBALS["POST"]["email"],
                    "lastname" => empty($GLOBALS["POST"]["lastname"]) ? "" : $GLOBALS["POST"]["lastname"],
                    "firstname" => empty($GLOBALS["POST"]["firstname"]) ? "" : $GLOBALS["POST"]["firstname"],
            ];

            //tries to log in
            if (isOneEmpty($GLOBALS["POST"]["email"], $GLOBALS["POST"]["password"], $GLOBALS["POST"]["lastname"], $GLOBALS["POST"]["firstname"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            if (($passwordErrors = passwordPossible($GLOBALS["POST"]["password"])) && count($passwordErrors) > 0) {
                $this->errors = array_merge($this->errors, $passwordErrors);
                return parent::preRender();
            }


            $customer = CustomerRepository::findOneByEmail(self::getCon(), $GLOBALS["POST"]["email"]);

            if (!empty($customer)) {
                $this->errors[] = 4;
                return parent::preRender();
            }

            $passwordHash = password_hash($GLOBALS["POST"]["password"], PASSWORD_DEFAULT);

            $id = CustomerRepository::create(self::getCon(), $GLOBALS["POST"]["email"], $passwordHash, $GLOBALS["POST"]["lastname"], $GLOBALS["POST"]["firstname"]);

            //password and username matches
            $_SESSION["user"] = ["id" => $id, "email" => $GLOBALS["POST"]["email"]];

            redirect("/user");
        }

        public function render(): void { ?>
            <h1>User Registration</h1>
            <form method="POST" id="register-form">
                <div class="form-error">
                    <ul>
                        <?php foreach (getErrors($this->errors) as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="wrapper">
                    <div>
                        <label class="vertical">
                            <span class="required">Firstname:</span>
                            <input type="text" name="firstname" id="firstname"
                                   value="<?= $this->customerData["firstname"] ?>">
                            <span id="firstname-error" class="error"></span>
                        </label>
                    </div>
                    <div>
                        <label class="vertical">
                            <span class="required">Lastname:</span>
                            <input type="text" name="lastname" id="lastname"
                                   value="<?= $this->customerData["lastname"] ?>">
                            <span id="lastname-error" class="error"></span>
                        </label>
                    </div>
                </div>
                <label class="vertical name">
                    <span class="required">Email:</span>
                    <input type="text" name="email" id="email" value="<?= $this->customerData["email"] ?>">
                    <span id="email-error" class="error"></span>
                </label>
                <div class="wrapper">
                    <label class="vertical">
                        <span class="required">Password:</span>
                        <input type="password" name="password" id="password">
                        <span id="password-error" class="error"></span>
                    </label>
                    <label class="vertical">
                        <span class="required">Confirm password:</span>
                        <input type="password" name="password-confirm" id="password-confirm">
                        <span id="password-confirm-error" class="error"></span>
                    </label>
                </div>
                <button class="btn-blue" type="submit">Register</button>
            </form>
            <div class="center">
                <a href="/user/login">I already have an account</a>
            </div>
        <?php }
    }
