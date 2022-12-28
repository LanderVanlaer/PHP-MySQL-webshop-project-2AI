UPDATE customer
SET firstname = ?,
    lastname  = ?,
    email     = ?,
    active    = ?
WHERE id = ?;
