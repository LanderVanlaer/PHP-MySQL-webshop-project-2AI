<?php

    namespace utils\page;

    function generatePage(bool $isAdminPage, string $title, callable $renderHead, callable $render): void {
        ?>

        <!doctype html>
        <html lang="nl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">

            <link rel="stylesheet" href="/static/css/style.css">
            <script defer src="/static/js/header.js"></script>
            <meta name="theme-color" content="#ffffff">
            <link rel="icon" href="/static/images/logo_basic.svg">
            <link rel="mask-icon" href="/static/images/logo_basic.svg" color="#000000">
            <link rel="apple-touch-icon" href="/static/images/logo_basic.svg">

            <?php if ($isAdminPage) { ?>
                <link rel="stylesheet" href="/static/css/admin/style.css">
            <?php } ?>

            <?php $renderHead() ?>
            <title><?= $title ?></title>
        </head>
        <body>
            <?php if ($isAdminPage) {
                include "components/adminHeader.php";
            ?>
            <div id="admin-content-wrapper">
                <?php
                    include "components/adminAsideNav.php";
                    } else {
                    include "components/header.php";
                } ?>
                <main>
                    <?php $render() ?>
                </main>
                <?php if ($isAdminPage) { ?>
            </div>
        <?php } ?>
            <?php include "components/footer.php" ?>
        </body>
        </html>

        <?php
    }
