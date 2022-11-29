INSERT INTO productimage(product_id, path, `order`)
SELECT ?, ?, MAX(`order`) + 1
FROM productimage
WHERE product_id = ?;
