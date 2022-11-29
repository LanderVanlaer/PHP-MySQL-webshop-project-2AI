SELECT id, path, `order`, product_id
FROM productimage
WHERE product_id = ?
ORDER BY `order`;
