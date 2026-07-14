-- ============================================
-- quotes: 見積書本体
-- customer_name / customer_address は customers からのスナップショット
-- （顧客マスタが後から変更/削除されても発行済み見積の内容が変わらないようにするため）
-- ============================================
CREATE TABLE quotes (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER REFERENCES customers(id),
    customer_name VARCHAR(150) NOT NULL,
    customer_address TEXT,
    subject VARCHAR(150),
    issue_date DATE NOT NULL,
    expire_date DATE NOT NULL,
    tax_type VARCHAR(10) NOT NULL DEFAULT 'excluded', -- 'excluded'=税別 / 'included'=税込
    tax_rate NUMERIC(5,2) NOT NULL DEFAULT 10.00,
    subtotal NUMERIC(12,2) NOT NULL DEFAULT 0,
    tax_amount NUMERIC(12,2) NOT NULL DEFAULT 0,
    total_amount NUMERIC(12,2) NOT NULL DEFAULT 0,
    note TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP NOT NULL DEFAULT now(),
    updated_at TIMESTAMP NOT NULL DEFAULT now()
);

-- 一覧画面で期限切れ間近を判定する際によく使うので、expire_date にインデックスを張っておく
CREATE INDEX idx_quotes_expire_date ON quotes(expire_date);
