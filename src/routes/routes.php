<?php

    namespace routes;

    use Route;

    /**
     * @return Route[]
     */
    function getRoutes(): array {
        return array(
            new Homepage(),
            new AdminLogin(),
            new AdminLogout(),
            new AdminBrand(),
            new AdminBrandCreate(),
            new AdminBrandEdit(),
            new ErrorPage(),
            new AdminCategory(),
            new AdminCategoryEdit(),
            new AdminCategoryCreate(),
        );
    }
