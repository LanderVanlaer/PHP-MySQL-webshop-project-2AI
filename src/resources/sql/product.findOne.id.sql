SELECT id,
       name,
       description,
       public,
       category_id,
       brand_id
FROM product p
WHERE p.id = ?;
