SELECT subcategory.id   as id,
       subcategory.name as name
FROM subcategory
         INNER JOIN categorysubcategory c on subcategory.id = c.subcategory_id
WHERE c.category_id = ?;
