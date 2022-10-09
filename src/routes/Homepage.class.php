<?php

    namespace routes;

    use Route;

    class Homepage extends Route
    {
        public function __construct() {
            parent::__construct(false, false, false);
        }

        public function matchesPath(string $path): bool {
            return $path === "";
        }

        public function render(): void {
            // TODO: Implement render() method.
        }

        public function getDocumentTitle(): string {
            return "Homepage";
        }
    }
