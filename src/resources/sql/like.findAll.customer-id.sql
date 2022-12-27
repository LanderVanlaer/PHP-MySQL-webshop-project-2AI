SELECT added, product_id
FROM `like`
         INNER JOIN product p on `like`.product_id = p.id
WHERE customer_id = ?
  AND p.public IS TRUE;
