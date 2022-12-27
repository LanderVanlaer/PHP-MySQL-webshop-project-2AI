<?php

    namespace api;

    use ApiRoute;
    use Like;
    use Search;

    /**
     * @return ApiRoute[]
     */
    function getApis(): array {
        return array(
            new Search(),
            new Like(),
        );
    }
