SELECT DISTINCT brand.id,
                brand.name,
                brand.logo
FROM brand
         INNER JOIN product p on brand.id = p.brand_id
         INNER JOIN category c on p.category_id = c.id
WHERE category_id = ?;
