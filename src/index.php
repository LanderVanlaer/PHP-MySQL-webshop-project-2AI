<?php
    //    [REDIRECT_URL] => /abc/dev
    //    [REQUEST_METHOD] => GET
    //    [QUERY_STRING] => query=123&one=two
    //    [REQUEST_URI] => /abc/dev?query=123&one=two
    require_once "autoloader.php";
    require_once "routes/routes.php";
    require_once "defaultPage.php";

    use function routes\getRoutes;
    use function utils\page\generatePage;

    $route = null;
    foreach (getRoutes() as $rt) {
        if ($rt->matchesPath($_SERVER["REDIRECT_URL"])) {
            $route = $rt;
            break;
        }
    }


    if (is_null($route) || !$route->preRender()) {
        generatePage(
            "Error 404",
            function () {
                //HEAD data
            },
            function () {
                echo "error 404 page";
            },
        );
    } else {
        //https://www.php.net/manual/en/functions.first_class_callable_syntax.php
        generatePage($route->getDocumentTitle(), $route->renderHead(...), $route->render(...));
    }
