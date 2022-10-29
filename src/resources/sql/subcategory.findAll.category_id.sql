SELECT s.id                                                     as id,
       s.name                                                   as name,
       MAX(c.category_id IS NOT NULL AND c.category_id = ?) > 0 as selected
FROM subcategory s
         LEFT JOIN categorysubcategory c on s.id = c.subcategory_id
GROUP BY s.id;
