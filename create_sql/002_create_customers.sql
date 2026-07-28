-- ============================================
-- customers: 顧客マスタ（最低限の項目のみ）
-- ============================================
CREATE TABLE customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address TEXT,
    contact_name VARCHAR(100),   -- 担当者名
    email VARCHAR(150),
    phone VARCHAR(30),
    created_at TIMESTAMP NOT NULL DEFAULT now()
);
