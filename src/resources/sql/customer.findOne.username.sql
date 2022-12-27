SELECT id, password, email
FROM customer
WHERE email = ?
LIMIT 1;
