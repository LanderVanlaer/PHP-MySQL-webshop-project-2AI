SELECT o.id,
       o.creationDate,
       o.customer_id,
       c.lastname,
       c.firstname,
       c.email
FROM `order` o
         INNER JOIN customer c on o.customer_id = c.id
WHERE o.id = ?;
