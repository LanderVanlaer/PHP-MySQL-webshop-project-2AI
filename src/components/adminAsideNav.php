<div id="admin-tables-nav">
    <nav>
        <ul>
            <?php
                $tables = [
                        "Brand",
                        "Product",
                        "ProductImage",
                        "Property",
                        "Category",
                        "CategorySubcategory",
                        "Subcategory",
                        "Specification",
                        "Customer",
                        "Like",
                        "Order",
                        "OrderProduct",
                        "ShoppingcartProducts",
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
