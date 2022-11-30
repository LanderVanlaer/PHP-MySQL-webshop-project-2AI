UPDATE product
SET name        = ?,
    description = ?,
    public      = ?,
    brand_id    = ?,
    category_id = ?
WHERE id = ?;
