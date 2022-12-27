DELETE
FROM shoppingcartproducts
WHERE customer_id = ?
  AND product_id = ?;
