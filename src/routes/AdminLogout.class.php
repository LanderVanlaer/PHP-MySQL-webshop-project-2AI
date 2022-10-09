<?php

    namespace routes;

    use Route;
    use function utils\redirect;

    class AdminLogout extends Route
    {

        public function __construct() {
            parent::__construct(false, false, true);
        }

        public function matchesPath(string $path): bool {
            return $path === "/admin/logout";
        }

        public function getDocumentTitle(): string {
            return "Admin Logout";
        }

        public function preRender(): never {
            unset($_SESSION["admin"]);
            redirect("/admin/login");
        }

        public function render(): void {
            //Will never be executed, check preRender() --> redirect
        }
    }
