<?php

    namespace routes;

    use Route;
    use function utils\redirect;

    class UserLogoutPage extends Route
    {
        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function getDocumentTitle(): string {
            return "User Logut";
        }

        public function matchesPath(string $path): bool {
            return $path === "/user/logout";
        }

        public function preRender(): bool {
            session_start();
            unset($_SESSION["user"]);
            redirect("/user/login");
        }

        public function render(): void {
            //Will never be executed, check preRender() --> redirect
        }
    }
