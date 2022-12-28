SELECT id,
       password,
       email,
       firstname,
       lastname,
       active
FROM customer
WHERE id = ?
LIMIT 1;
