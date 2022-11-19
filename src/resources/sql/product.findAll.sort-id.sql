SELECT p.id     as product_id,
       p.name   as product_name,
       p.public as product_public,
       c.id     as category_id,
       c.name   as category_name,
       b.id     as brand_id,
       b.name   as brand_name
FROM product p
         INNER JOIN category c on p.category_id = c.id
         INNER JOIN brand b on p.brand_id = b.id
ORDER BY p.id;
