SELECT id,
       name,
       description,
       public,
       category_id,
       brand_id,
       price
FROM product p
WHERE p.id = ?;
