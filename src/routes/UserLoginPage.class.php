<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isLoggedInAsUser;
    use function utils\isOneEmpty;
    use function utils\objectPick;
    use function utils\redirect;

    class UserLoginPage extends Route
    {
        private array $errors = [];

        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
            <style>
                form {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    gap: 3.5rem;
                }

                form label {
                    font-size: 2rem;
                }

                form label input {
                    font-size: 2rem;
                }
            </style>
        <?php }


        public function getDocumentTitle(): string {
            return "User Login";
        }

        public function matchesPath(string $path): bool {
            return $path === "/user/login";
        }

        public function preRender(): bool {
            session_start();

            if (isLoggedInAsUser())
                redirect("/user");

            if ($_SERVER["REQUEST_METHOD"] !== "POST")
                return parent::preRender();

            //tries to log in
            if (isOneEmpty($GLOBALS["POST"]["email"], $GLOBALS["POST"]["password"])) {
                $this->errors[] = 0;
                return parent::preRender();
            }

            $customer = CustomerRepository::findOneByEmail(self::getCon(), $GLOBALS["POST"]["email"]);

            if (empty($customer) || !password_verify($GLOBALS["POST"]["password"], $customer["password"])) {
                $this->errors[] = 1;
                return parent::preRender();
            }

            //password and username matches
            $_SESSION["user"] = objectPick($customer, "id", "email");

            redirect("/user");
        }

        public function render(): void { ?>
            <h1>User Login</h1>
            <form method="POST">
                <?php if (count($this->errors)) { ?>
                    <div class="form-error">
                        <ul>
                            <?php foreach (getErrors($this->errors) as $error) { ?>
                                <li><?= $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <label class="vertical">
                    <span class="required">Email:</span>
                    <input type="text" name="email" required>
                </label>
                <label class="vertical">
                    <span class="required">Password:</span>
                    <input type="password" name="password" required>
                </label>
                <label class="vertical">
                    <button class="btn-blue" type="submit">Login</button>
                </label>
            </form>
        <?php }
    }
