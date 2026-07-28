-- ============================================
-- customers に updated_at / internal_memo を追加
-- 実行例: psql -U <user> -d <database> -f 005_alter_customers_add_updated_at_and_memo.sql
-- ============================================

-- 最終更新日時
ALTER TABLE customers
    ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT now();

-- 既存行にも created_at と同じ値を入れておく(追加直後の初期値として)
UPDATE customers SET updated_at = created_at;

-- 社内向けメモ(値引き経緯、これまでのやり取りなど。顧客には見せない前提の情報)
-- quotes.note (見積書に印字する備考=顧客向け) とは役割が異なるため、名前で区別する
ALTER TABLE customers
    ADD COLUMN internal_memo TEXT;
