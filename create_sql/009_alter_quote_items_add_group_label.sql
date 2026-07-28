-- ============================================
-- quote_items に group_label（明細の大項目グループ）を追加
-- 実行例: psql -U <user> -d <database> -f 009_alter_quote_items_add_group_label.sql
--
-- 実際の見積書サンプルにあった [A] のような大項目グルーピングに対応するための列。
-- 同じ group_label を持つ行が(display_order順に)連続していれば、
-- PDF/画面表示側で同一グループとしてまとめて表示する想定。
-- NULL・空文字の場合はグループ無し(通常の1行)として扱う。
-- ============================================
ALTER TABLE quote_items
    ADD COLUMN group_label VARCHAR(100);
