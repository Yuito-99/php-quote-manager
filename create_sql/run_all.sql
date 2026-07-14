-- ============================================
-- 初期構築時、この1本を psql -f run_all.sql で流せば全テーブルが作成される
-- 列追加など仕様変更が入った場合は、このファイルではなく
-- create_sql/999_alter_xxx.sql のような形で追記していく運用を推奨
-- ============================================
\i 001_create_users.sql
\i 002_create_customers.sql
\i 003_create_quotes.sql
\i 004_create_quote_items.sql
