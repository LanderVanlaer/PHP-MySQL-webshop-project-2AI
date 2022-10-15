<?php

    namespace routes;

    use Route;

    class ErrorPage extends Route
    {
        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function matchesPath(string $path): bool {
            return $path === "/error";
        }

        public function preRender(): bool {
            http_response_code(500);
            return parent::preRender();
        }

        public function render(): void { ?>
            <style>
                p {
                    color: red;
                    font-size: 25px;
                }
            </style>
            <p>Er is iets fout gelopen,<br>gelieve de beheerder te contacteren voor meer informatie.</p>
        <?php }

        public function getDocumentTitle(): string {
            return "Error";
        }
    }
