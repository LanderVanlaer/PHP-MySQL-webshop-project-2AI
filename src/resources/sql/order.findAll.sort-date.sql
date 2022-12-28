SELECT o.id           AS id,
       o.creationDate AS creationDate,
       o.customer_id  AS customer_id,
       SUM(op.amount) AS amount
FROM `order` o
         LEFT JOIN orderproduct op on o.id = op.order_id
GROUP BY o.id, o.creationDate
ORDER BY o.creationDate DESC;
