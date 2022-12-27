SELECT id,
       product_id,
       customer_id,
       amount
FROM shoppingcartproducts
WHERE customer_id = ?
  AND product_id = ?
LIMIT 1;
