SELECT product.id,
       product.name,
       product.description,
       product.price,
       p.path
FROM product
         LEFT JOIN productimage p on product.id = p.product_id AND p.`order` = 0
WHERE name LIKE ?
  AND public IS true;
