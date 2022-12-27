<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use Route;
    use function utils\redirect;

    class UserPage extends Route
    {
        private array $customer;

        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/user/style.css">
        <?php }

        public function getDocumentTitle(): string {
            return "User page";
        }

        public function matchesPath(string $path): bool {
            return $path === "/user";
        }

        public function preRender(): bool {
            $customer = CustomerRepository::findOne(self::getCon(), $_SESSION["user"]["id"]);

            if (empty($customer))
                redirect("/user/logout");

            $this->customer = $customer;
            return parent::preRender();
        }

        public function render(): void { ?>
            <h1>User page</h1>
            <section id="profile">
                <h2>Profile</h2>
                <div>
                    <label>
                        <span>User id</span>
                        <input type="number" name="id" value="<?= $this->customer["id"] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <span>First name:</span>
                        <input type="text" name="first-name" value="<?= $this->customer["firstname"] ?>" disabled />
                    </label>
                    <label>
                        <span>Last name:</span>
                        <input type="text" name="last-name" value="<?= $this->customer["lastname"] ?>" disabled />
                    </label>
                </div>
                <div>
                    <label>
                        <span>Email:</span>
                        <input type="text" name="email" value="<?= $this->customer["email"] ?>" disabled />
                    </label>
                </div>
            </section>
            <section id="orders">
                <!-- TODO add orders -->
            </section>
        <?php }
    }
