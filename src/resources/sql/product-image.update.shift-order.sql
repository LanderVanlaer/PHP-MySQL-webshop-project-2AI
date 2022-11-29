UPDATE productimage
SET `order` = `order` - 1
WHERE product_id = ?
  AND `order` > ?;
