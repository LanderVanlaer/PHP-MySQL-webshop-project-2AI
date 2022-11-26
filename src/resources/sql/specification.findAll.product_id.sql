SELECT sub.id      AS subcategory_id,
       sub.name    AS subcategory_name,
       sp.id       AS specification_id,
       sp.name     AS specification_name,
       sp.type     AS specification_type,
       sp.notation AS specification_notation,
       prop.id     AS property_id,
       prop.value  AS property_value
FROM product pro
         INNER JOIN category cat on pro.category_id = cat.id
         INNER JOIN categorysubcategory csc on cat.id = csc.category_id
         INNER JOIN subcategory sub on csc.subcategory_id = sub.id
         INNER JOIN specification sp on sub.id = sp.subcategory_id
         LEFT JOIN property prop on sp.id = prop.specification_id
WHERE pro.id = ?
  AND (prop.product_id = ? OR prop.product_id IS NULL)
ORDER BY sub.id, sp.id;
