<?php

    namespace routes;

    use database\entities\EmployeeRepository;
    use Route;
    use function utils\getErrors;
    use function utils\isLoggedInAsAdmin;
    use function utils\isOneEmpty;
    use function utils\objectPick;
    use function utils\redirect;

    class AdminLogin extends Route
    {
        private array $errors = [];
        private bool $isLoggedIn = false;

        public function __construct() {
            parent::__construct(false, false, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("/^\/admin(\/login)?$/", $path);
        }

        public function getDocumentTitle(): string {
            return "Admin Login";
        }

        public function preRender(): bool {
            if (rtrim($_SERVER["REDIRECT_URL"], '/') === "/admin") {
                redirect("/admin/login");
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                //tries to log in
                if (isOneEmpty($GLOBALS["POST"]["username"], $GLOBALS["POST"]["password"])) {
                    $this->errors[] = 0;
                    return parent::preRender();
                }

                $admin = EmployeeRepository::findOne(self::getCon(), $GLOBALS["POST"]["username"]);

                if (empty($admin) || !password_verify($GLOBALS["POST"]["password"], $admin["password"])) {
                    $this->errors[] = 1;
                    return parent::preRender();
                }

                //password and username matches
                $_SESSION["admin"] = objectPick($admin, "id", "username");

                $this->isLoggedIn = true;
            }

            return parent::preRender();
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/form.css">
        <?php }

        public function render(): void {
            if ($this->isLoggedIn || isLoggedInAsAdmin()) { ?>
                <p>U bent ingelogd als <strong><?= $_SESSION["admin"]["username"] ?></strong></p>
            <?php } else { ?>
                <form action="/admin/login" method="post">
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
                        <span class="required">Gebruikersnaam:</span>
                        <input type="text" name="username" id="username" required>
                    </label>
                    <label class="vertical">
                        <span class="required">Wachtwoord:</span>
                        <input type="password" name="password" id="password" required>
                    </label>
                    <button type="submit" class="btn-blue">Login</button>
                </form>
                <?php
            }
        }
    }
