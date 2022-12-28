<?php

    namespace routes;

    use database\entities\OrderProductRepository;
    use database\entities\OrderRepository;
    use Route;

    class AdminOrderDetails extends Route
    {
        private array $order = [];

        public function __construct() {
            parent::__construct(false, true, true);
        }

        public function matchesPath(string $path): bool {
            return preg_match("#^/admin/order/\d+/details#", $path);
        }

        public function getDocumentTitle(): string {
            return "Order {$this->order["name"]} Details";
        }

        public function preRender(): bool {
            preg_match("#^/admin/order/(\d+)/details#", $_SERVER["REDIRECT_URL"], $matches);

            if (!is_numeric($matches[1]))
                return false;

            $id = intval($matches[1]);

            $order = OrderRepository::findOne(self::getCon(), $id);

            if (empty($order)) return false;
            $this->order = $order;

            return parent::preRender();
        }

        public
        function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/admin/table.css">
            <link rel="stylesheet" href="/static/css/admin/order/details/style.css">
        <?php }

        public
        function render(): void { ?>
            <h1>Edit Order (<?= $this->order["id"] ?>)</h1>
            <div class="top-wrapper">
                <div>
                    <label>
                        Date: <input type="datetime-local" disabled
                                     value="<?php echo date('Y-m-d\TH:i', strtotime($this->order["creationDate"])); ?>">
                    </label>
                </div>
                <div>
                    <fieldset>
                        <legend><a href="/admin/customer/<?= $this->order["customer_id"] ?>/edit">Customer:</a></legend>
                        <label>
                            Firstname:
                            <input type="text" disabled value="<?= $this->order["firstname"] ?>">
                        </label>
                        <label>
                            Lastname:
                            <input type="text" disabled value="<?= $this->order["lastname"] ?>">
                        </label>
                        <label>
                            Email:
                            <input type="text" disabled value="<?= $this->order["email"] ?>">
                        </label>
                    </fieldset>
                </div>
            </div>
            <table>
                <caption>Products</caption>
                <thead>
                    <tr>
                        <th scope="col" rowspan="2">id</th>
                        <th scope="col" rowspan="2">Name</th>
                        <th scope="col" rowspan="2">Amount</th>
                        <th scope="colgroup" colspan="2">Price</th>
                    </tr>
                    <tr>
                        <th scope="col">Piece</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $amountTotal = 0;
                        $pricePieceTotal = 0;
                        $priceTotal = 0;
                        foreach (OrderProductRepository::findAll(self::getCon(), $this->order["id"]) as $product):
                            $amountTotal += $product["amount"];
                            $pricePieceTotal += $product["product_price_piece"];
                            $priceTotal += $product["product_price_total"];
                            ?>
                            <tr>
                                <td class="center"><?= $product["product_id"] ?></td>
                                <td>
                                    <a href="/admin/product/<?= $product["product_id"] ?>/edit"><?= $product["product_name"] ?></a>
                                </td>
                                <td class="center"><?= $product["amount"] ?></td>
                                <td class="right">&euro;&nbsp;<?= $product["product_price_piece"] ?></td>
                                <td class="right">&euro;&nbsp;<?= $product["product_price_total"] ?></td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="center" colspan="2" scope="row">Total</th>
                        <td class="center"><?= $amountTotal ?></td>
                        <td class="right">&euro;&nbsp;<?= $pricePieceTotal ?></td>
                        <td class="right">&euro;&nbsp;<?= $priceTotal ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php }
    }
