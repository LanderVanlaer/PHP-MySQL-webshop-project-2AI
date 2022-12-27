INSERT INTO orderproduct
    (amount, product_id, order_id)
SELECT amount, product_id, ?
FROM shoppingcartproducts
WHERE customer_id = ?;
