SELECT op.id               AS id,
       op.product_id       AS product_id,
       p.name              AS product_name,
       op.amount           AS amount,
       p.price             AS product_price_piece,
       p.price * op.amount AS product_price_total,
       pi.path             AS product_thumbnail
FROM orderproduct op
         INNER JOIN product p on op.product_id = p.id
         LEFT JOIN productimage pi on p.id = pi.product_id AND pi.`order` = 0
WHERE op.order_id = ?;
