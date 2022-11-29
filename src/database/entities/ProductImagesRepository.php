<?php

    namespace database\entities;

    use Generator;
    use mysqli;

    class ProductImagesRepository
    {
        public static function findByProduct(mysqli $con, int $productId): Generator {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.find.product-id.sql'));
            $query->bind_param("i", $productId);
            $query->execute();
            $res = $query->get_result();

            while ($row = $res->fetch_assoc()) yield $row;

            $query->close();
            $res->close();
        }

        public static function create(mysqli $con, int $productId, string $path): int {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.create.sql'));
            $query->bind_param("isi", $productId, $path, $productId);
            $query->execute();
            $query->close();

            return $con->insert_id;
        }

        public static function update(mysqli $con, int $id, int $order): bool {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.update.order.sql'));
            $query->bind_param("ii", $order, $id);
            $val = $query->execute();
            $query->close();

            return $val;
        }

        public static function delete(mysqli $con, int $imageId, int $productId): array|null {
            //check if image exists
            $image = self::findOne($con, $imageId);
            if (!$image) return null;

            //delete image
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.delete.sql'));
            $query->bind_param("ii", $imageId, $productId);
            $val = $query->execute();
            $query->close();

            if (!$val) return null;

            //shift `order` by one
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.update.shift-order.sql'));
            $query->bind_param("ii", $productId, $image["order"]);
            $query->execute();
            $query->close();

            return $image;
        }

        public static function findOne(mysqli $con, int $id): array|null {
            $query = $con->prepare(file_get_contents(__DIR__ . '/../../resources/sql/product-image.findOne.id.sql'));
            $query->bind_param("i", $id);
            $query->execute();

            $res = $query->get_result();
            $image = $res->fetch_assoc();

            $query->close();
            $res->close();

            return $image;
        }
    }
