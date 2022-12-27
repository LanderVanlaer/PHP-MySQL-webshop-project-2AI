SELECT sp.id               AS id,
       sp.amount           AS amount,
       sp.product_id       AS product_id,
       p.name              AS name,
       p.price             AS price,
       image.path          AS path,
       sp.amount * p.price AS total_price
FROM shoppingcartproducts sp
         INNER JOIN product p on sp.product_id = p.id
         LEFT JOIN productimage image on p.id = image.product_id AND image.`order` = 0
WHERE customer_id = ?;
