SELECT id,
       password,
       email,
       firstname,
       lastname
FROM customer
WHERE id = ?
LIMIT 1;
