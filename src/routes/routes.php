<?php

    namespace routes;

    use Route;

    /**
     * @return Route[]
     */
    function getRoutes(): array {
        return array(
            new Homepage(),
        );
    }
