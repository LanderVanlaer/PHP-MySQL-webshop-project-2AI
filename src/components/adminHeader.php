<header>
    <nav>
        <ul>
            <li id="logo">
                <a href="/">
                    <img src="/static/images/logo_basic.svg" alt="logo">
                </a>
            </li>
            <?php use function utils\isLoggedInAsAdmin;

                if (isLoggedInAsAdmin()) { ?>
                    <li id="login-logout">
                        <a class="btn-blue" href="/admin/logout">Logout</a>
                    </li>
                <?php } ?>
        </ul>
    </nav>
</header>
