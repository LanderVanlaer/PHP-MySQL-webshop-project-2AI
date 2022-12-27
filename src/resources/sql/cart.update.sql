UPDATE shoppingcartproducts
SET amount = ?
WHERE customer_id = ?
  AND product_id = ?;
