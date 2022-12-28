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
                                <th>price</th>
                                <th>amount</th>
                                <th>total price</th>
                                <th>change</th>
                                <th>delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalPrice = 0;
                                $totalAmount = 0;
                                foreach (CartRepository::findAll(self::getCon(), $_SESSION["user"]["id"]) as $row):
                                    $totalPrice += $row["total_price"];
                                    $totalAmount += $row["amount"];
                                    ?>
                                    <tr>
                                        <td class="center">
                                            <?php if (!empty($row["path"])): ?>
                                                <img src="/images/product/<?= $row["path"] ?>"
                                                     alt="<?= $row["name"] ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/product/<?= $row["product_id"] ?>">
                                                <?= $row["name"] ?>
                                            </a>
                                        </td>
                                        <td class="center">
                                            &euro;&nbsp;<?= $row["price"] ?>
                                        </td>
                                        <td class="center">
                                            <?= $row["amount"] ?>
                                        </td>
                                        <td class="center">
                                            &euro;&nbsp;<?= $row["total_price"] ?>
                                        </td>
                                        <td class="change">
                                            <form action="/cart/set-amount" method="POST" autocomplete="off">
                                                <input type="hidden" name="product-id"
                                                       value="<?= $row["product_id"] ?>">
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
                                                <input type="hidden" name="product-id"
                                                       value="<?= $row["product_id"] ?>">
                                                <input type="hidden" name="amount" value="0">
                                                <button type="submit" class="btn-blue">
                                                    <img src="/static/images/Icon_trash.svg" alt="">
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php if ($totalAmount === 0): ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="right">Total:</td>
                                <td class="center"><?= $totalAmount ?></td>
                                <td class="center">&euro;&nbsp;<?= $totalPrice ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="center large">
                    <?php if ($totalAmount): ?>
                        <a href="/cart/order" class="btn-blue">Bestellen</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php }
    }
