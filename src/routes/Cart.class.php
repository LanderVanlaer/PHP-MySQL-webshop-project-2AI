<?php

    namespace routes;

    use database\entities\CartRepository;
    use Route;

    class Cart extends Route
    {
        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function matchesPath(string $path): bool {
            return $path === "/cart";
        }

        public function getDocumentTitle(): string {
            return "Cart";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
            <link rel="stylesheet" href="/static/css/cart/style.css">
        <?php }

        public function render(): void { ?>
            <div class="split">
                <div>
                    <table id="cart">
                        <caption>Cart</caption>
                        <thead>
                            <tr>
                                <th colspan="2">product</th>
                                <th>amount</th>
                                <th>change</th>
                                <th>delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (CartRepository::findAll(self::getCon(), $_SESSION["user"]["id"]) as $row) { ?>
                                <tr>
                                    <td class="center">
                                        <img src="/images/product/<?= $row["path"] ?>" alt="<?= $row["name"] ?>">
                                    </td>
                                    <td>
                                        <a href="/product/<?= $row["product_id"] ?>">
                                            <?= $row["name"] ?>
                                        </a>
                                    </td>
                                    <td class="center">
                                        <?= $row["amount"] ?>
                                    </td>
                                    <td>
                                        <form action="/cart/set-amount" method="POST" autocomplete="off">
                                            <input type="hidden" name="product-id" value="<?= $row["product_id"] ?>">
                                            <label>
                                                Amount:
                                                <input type="number" name="amount" min="1"
                                                       value="<?= $row["amount"] ?>">
                                            </label>
                                            <button type="submit" class="btn-blue">Update</button>
                                        </form>
                                    </td>
                                    <td class="center">
                                        <form action="/cart/set-amount" method="POST" autocomplete="off">
                                            <input type="hidden" name="product-id" value="<?= $row["product_id"] ?>">
                                            <input type="hidden" name="amount" value="0">
                                            <button type="submit" class="btn-blue">
                                                <img src="/static/images/Icon_trash.svg" alt="">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    more
                </div>
            </div>
        <?php }
    }
