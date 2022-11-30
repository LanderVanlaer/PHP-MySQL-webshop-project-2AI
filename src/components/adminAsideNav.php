<div id="admin-tables-nav">
    <nav>
        <ul>
            <?php
                $tables = [
                        "Brand",
                        "Product",
                        "Category",
                        "CategorySubcategory",
                        "Subcategory",
                        "Customer",
                        "Employee",
                ];

                foreach ($tables as $table) { ?>
                    <li class="<?= (rtrim($_SERVER["REDIRECT_URL"], '/') === strtolower("/admin/$table")) && "active" ?>">
                        <a href="/admin/<?= strtolower($table) ?>">
                            <?= $table ?>
                        </a>
                    </li>
                <?php } ?>
        </ul>
    </nav>
</div>
