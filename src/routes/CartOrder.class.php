<?php

    namespace routes;

    use database\entities\CartRepository;
    use Route;
    use function utils\redirect;

    class CartOrder extends Route
    {
        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function matchesPath(string $path): bool {
            return $path === "/cart/order";
        }

        public function getDocumentTitle(): string {
            return "Confirm Order";
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
            <link rel="stylesheet" href="/static/css/cart/style.css">
            <style>
                .large button {
                    margin-top: 2rem;
                }
            </style>
        <?php }

        public function preRender(): bool {
            if (!CartRepository::findAll(self::getCon(), $_SESSION["user"]["id"])->valid())
                redirect("/cart");

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                CartRepository::toOrder(self::getCon(), $_SESSION["user"]["id"]);
                redirect("/user");
            }

            return parent::preRender();
        }

        public
        function render(): void { ?>
            <div class="split">
                <div>
                    <table id="cart">
                        <caption>Order</caption>
                        <thead>
                            <tr>
                                <th colspan="2">product</th>
                                <th>price</th>
                                <th>amount</th>
                                <th>total price</th>
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
                                    </tr>
                                <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="right">Total:</td>
                                <td class="center"><?= $totalAmount ?></td>
                                <td class="center">&euro;&nbsp;<?= $totalPrice ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="center large">
                    <h2>Confirm Order</h2>
                    <form method="POST">
                        <button class="btn-blue">Confirm</button>
                    </form>
                </div>
            </div>
        <?php }
    }
