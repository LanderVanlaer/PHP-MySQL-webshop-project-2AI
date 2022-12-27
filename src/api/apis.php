<?php

    namespace api;

    use ApiRoute;
    use Search;

    /**
     * @return ApiRoute[]
     */
    function getApis(): array {
        return array(
            new Search(),
        );
    }
