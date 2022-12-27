<?php

    namespace routes;

    use database\entities\CustomerRepository;
    use database\entities\OrderRepository;
    use database\entities\ProductImagesRepository;
    use database\entities\ProductRepository;
    use Route;
    use function utils\redirect;

    class UserPage extends Route
    {
        private array $customer;

        public function __construct() {
            parent::__construct(true, false, false);
        }

        public function renderHead(): void { ?>
            <link rel="stylesheet" href="/static/css/user/style.css">
        <?php }

        public function getDocumentTitle(): string {
            return "User page";
        }

        public function matchesPath(string $path): bool {
            return $path === "/user";
        }

        public function preRender(): bool {
            $customer = CustomerRepository::findOne(self::getCon(), $_SESSION["user"]["id"]);

            if (empty($customer))
                redirect("/user/logout");

            $this->customer = $customer;
            return parent::preRender();
        }

        public function render(): void { ?>
            <h1>User page</h1>
            <section id="profile">
                <h2>Profile</h2>
                <div>
                    <div class="left">
                        <div>
                            <label>
                                <span>User id</span>
                                <input type="number" name="id" value="<?= $this->customer["id"] ?>" disabled>
                            </label>
                        </div>
                        <div>
                            <label>
                                <span>First name:</span>
                                <input type="text" name="first-name" value="<?= $this->customer["firstname"] ?>"
                                       disabled>
                            </label>
                            <label>
                                <span>Last name:</span>
                                <input type="text" name="last-name" value="<?= $this->customer["lastname"] ?>" disabled>
                            </label>
                        </div>
                        <div>
                            <label>
                                <span>Email:</span>
                                <input type="text" name="email" value="<?= $this->customer["email"] ?>" disabled>
                            </label>
                        </div>
                    </div>
                    <div class="right">
                        <a href="/user/logout" class="btn-blue">Logout</a>
                    </div>
                </div>
            </section>
            <section id="orders">
                <h2>Orders</h2>
                <ul>
                    <?php
                        $prevOrderId = -1;
                        $totalAmount = 0;
                        $totalPrice = 0;
                        $hasOrders = false;
                    ?>
                    <?php foreach (OrderRepository::findAll(self::getCon(), $_SESSION["user"]["id"]) as $i => $order): ?>
                <?php
                    $hasOrders = true;
                    if ($prevOrderId != $order["order_id"]): ?>
                <?php if ($prevOrderId != -1): ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="right">Total:</td>
                            <td><?= $totalAmount ?></td>
                            <td>&euro;&nbsp;<?= $totalPrice ?></td>
                        </tr>
                    </tfoot>
                    </table>
                    </li>
                <?php endif; ?>
                <?php
                    $totalAmount = 0;
                    $totalPrice = 0;
                ?>
                    <li>
                        <table>
                            <caption class="none">
                                Order <?= $i ?>
                            </caption>
                            <thead>
                                <tr>
                                    <th colspan="2" scope="row" class="right">Date:</th>
                                    <td colspan="3"><?= $order["order_creationDate"] ?></td>
                                </tr>
                                <tr>
                                    <th scope="colgroup" colspan="5" class="center title">Products</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php endif; ?>

                                <!--INSIDE TBODY-->
                                <?php
                                    $prevOrderId = $order["order_id"];

                                    $totalAmount += $order["amount"];
                                    $totalPrice += $this->printProductRow($order);
                                ?>
                                <!--INSIDE TBODY-->

                                <?php endforeach; ?>
                                <!--END-->
                                <?php if ($hasOrders): ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="right">Total:</td>
                                    <td><?= $totalAmount ?></td>
                                    <td>&euro;&nbsp;<?= $totalPrice ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </li>
                <?php endif; ?>
                </ul>
            </section>
        <?php }

        private function printProductRow(array $order): int {
            $product = ProductRepository::findOne(self::getCon(), $order["product_id"]);
            ?>
            <tr>
                <td class="center">
                    <?php $thumbnail = ProductImagesRepository::findThumbnail(self::getCon(), $order["product_id"]); ?>
                    <?php if (!empty($thumbnail)): ?>
                        <img src="/images/product/<?= $thumbnail["path"] ?>"
                             alt="<?= $product["name"] ?>">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/product/<?= $product["id"] ?>"><?= $product["name"] ?></a>
                </td>
                <td>
                    &euro;&nbsp;<?= $product["price"] ?>
                </td>
                <td>
                    <?= $order["amount"] ?>
                </td>
                <td>
                    &euro;&nbsp;<?= $order["amount"] * $product["price"] ?>
                </td>
            </tr>
            <?php
            return $product["price"];
        }
    }
