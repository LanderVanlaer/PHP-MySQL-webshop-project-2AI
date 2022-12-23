UPDATE product
SET name        = ?,
    description = ?,
    price       = ?,
    public      = ?,
    brand_id    = ?,
    category_id = ?
WHERE id = ?;
