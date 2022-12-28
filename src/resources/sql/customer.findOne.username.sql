SELECT id, password, email, active
FROM customer
WHERE email = ?
LIMIT 1;
