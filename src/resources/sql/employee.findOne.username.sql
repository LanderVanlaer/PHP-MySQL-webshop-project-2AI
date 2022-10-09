SELECT id, password, username
FROM `employee`
WHERE username = ?
LIMIT 1;
