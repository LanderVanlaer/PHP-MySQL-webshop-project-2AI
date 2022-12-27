SELECT product.id,
       product.name,
       product.description,
       product.public,
       product.category_id,
       product.brand_id,
       product.price,
       productimage.path AS image_path,
       b.logo            AS brand_path,
       b.name            AS brand_name
FROM product
         LEFT JOIN productimage on product.id = productimage.product_id
         LEFT JOIN brand b on product.brand_id = b.id
WHERE category_id = ?
  AND (productimage.`order` = 0 OR productimage.`order` IS NULL)
  AND product.public IS TRUE;
