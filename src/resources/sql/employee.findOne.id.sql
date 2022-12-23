SELECT id,
       password,
       username,
       firstname,
       lastname
FROM `employee`
WHERE id = ?
LIMIT 1;
