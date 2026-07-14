-- ============================================
-- quote_items: 見積明細行
-- ============================================
CREATE TABLE quote_items (
    id SERIAL PRIMARY KEY,
    quote_id INTEGER NOT NULL REFERENCES quotes(id) ON DELETE CASCADE,
    display_order INTEGER NOT NULL DEFAULT 0,
    item_name VARCHAR(150) NOT NULL,
    quantity NUMERIC(10,2) NOT NULL DEFAULT 1,
    unit VARCHAR(20),
    unit_price NUMERIC(12,2) NOT NULL DEFAULT 0,
    amount NUMERIC(12,2) NOT NULL DEFAULT 0
);

CREATE INDEX idx_quote_items_quote_id ON quote_items(quote_id);
