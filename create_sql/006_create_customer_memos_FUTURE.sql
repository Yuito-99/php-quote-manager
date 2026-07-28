-- ============================================
-- customer_memos: 顧客とのやり取り・社内メモの履歴テーブル
--
-- 【ステータス】現時点では未適用(将来必要になったタイミングで流し込む想定)
-- 現在は customers.internal_memo (TEXT1本) で簡易運用している。
--
-- 移行する場合の手順:
--   1) このファイルを実行してテーブルを作成
--   2) 既存の customers.internal_memo の内容を移行(下記コメント参照)
--   3) customers.internal_memo 列を DROP
--
-- 移行時のINSERT例:
--   INSERT INTO customer_memos (customer_id, body, created_at)
--   SELECT id, internal_memo, updated_at
--   FROM customers
--   WHERE internal_memo IS NOT NULL AND internal_memo <> '';
--
--   ALTER TABLE customers DROP COLUMN internal_memo;
-- ============================================
CREATE TABLE customer_memos (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    quote_id INTEGER REFERENCES quotes(id),  -- 任意。特定の見積に紐づくメモ(値引き経緯など)の場合に使う
    body TEXT NOT NULL,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP NOT NULL DEFAULT now()
    -- updated_at は持たせない。メモは追記型の履歴として扱い、後から書き換えない運用とするため。
    -- 内容を訂正したい場合は、修正内容を新規メモとして追加すること。
);

CREATE INDEX idx_customer_memos_customer_id ON customer_memos(customer_id);
CREATE INDEX idx_customer_memos_quote_id ON customer_memos(quote_id);
