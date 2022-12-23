SELECT DISTINCT prop.value            AS value,
                prop.specification_id AS specification_id
FROM property prop
         INNER JOIN product prod on prop.product_id = prod.id
WHERE prop.specification_id = ?
  AND prod.category_id = ?;
