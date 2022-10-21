SELECT c.id   categoryId,
       c.name categoryName,
       s.id   subcategoryId,
       s.name subcategoryName
FROM category c
         INNER JOIN categorysubcategory cs on c.id = cs.category_id
         INNER JOIN subcategory s on cs.subcategory_id = s.id
ORDER BY c.id, s.id;
