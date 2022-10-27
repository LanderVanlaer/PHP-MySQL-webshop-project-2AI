SELECT id,
       name,
       type,
       notation
FROM specification
WHERE subcategory_id = ?
ORDER BY name;
