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
            new AdminCategorySubcategory(),
            new AdminSubcategory(),
            new AdminSubcategoryCreate(),
            new AdminSubcategoryEdit(),
            new AdminProductCreate(),
            new AdminCategorySubcategoryEdit(),
            new AdminProduct(),
            new AdminPropertiesEdit(),
            new AdminProductImagesEdit(),
            new AdminProductEdit(),
            new AdminEmployee(),
            new AdminEmployeeCreate(),
            new AdminEmployeeEdit(),
            new ProductsCategory(),
            new Product(),
            new UserPage(),
            new UserLoginPage(),
            new UserLogoutPage(),
            new CartAdd(),
        );
    }
