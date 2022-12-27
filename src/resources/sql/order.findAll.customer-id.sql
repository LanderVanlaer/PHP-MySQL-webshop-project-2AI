SELECT o.id           AS order_id,
       o.creationDate AS order_creationDate,
       op.product_id  AS product_id,
       op.amount      AS amount
FROM `order` o
         INNER JOIN orderproduct op on o.id = op.order_id
WHERE o.customer_id = ?
ORDER BY o.creationDate;
