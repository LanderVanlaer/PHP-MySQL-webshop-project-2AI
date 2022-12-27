<?php
    //    [REDIRECT_URL] => /abc/dev
    //    [REQUEST_METHOD] => GET
    //    [QUERY_STRING] => query=123&one=two
    //    [REQUEST_URI] => /abc/dev?query=123&one=two
    require_once "autoloader.php"; //NOSONAR
    require_once "routes/routes.php"; //NOSONAR
    require_once "api/apis.php"; //NOSONAR
    require_once "defaultPage.php"; //NOSONAR

    use function api\getApis;
    use function routes\getRoutes;
    use function utils\{isLoggedInAsAdmin, isLoggedInAsUser, page\generatePage, redirect, validateStringArray};

    $GLOBALS["POST"] = validateStringArray($_POST);
    $GLOBALS["GET"] = validateStringArray($_GET);

    if (str_starts_with($_SERVER["REDIRECT_URL"], "/api")) {
        $path = substr(rtrim($_SERVER["REDIRECT_URL"], '/'), 4);

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $found = false;
        foreach (getApis() as $api) {
            if ($api->matchesPath($path)) {
                echo json_encode($api->render());
                $found = true;
                break;
            }
        }

        if (!$found) {
            http_response_code(404);
            echo json_encode(['message' => "Route not found"]);
        }

    } else {
        $route = null;
        foreach (getRoutes() as $rt) {
            if ($rt->matchesPath(rtrim($_SERVER["REDIRECT_URL"], '/'))) {
                $route = $rt;
                break;
            }
        }

        if (!is_null($route) && ($route->hasToBeLoggedInAsUser || $route->hasToBeLoggedInAsAdmin || $route->isAdminPage)) {
            session_start();

            if ($route->hasToBeLoggedInAsUser && !isLoggedInAsUser())
                redirect("/user/login");
            if ($route->hasToBeLoggedInAsAdmin && !isLoggedInAsAdmin())
                redirect("/admin/login");
        }

        if (is_null($route) || !$route->preRender()) {
            http_response_code(404);
            generatePage(false,
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
            generatePage($route->isAdminPage, $route->getDocumentTitle(), $route->renderHead(...), $route->render(...));
        }
    }
