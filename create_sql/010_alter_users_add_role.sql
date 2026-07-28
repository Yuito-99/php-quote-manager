-- ============================================
-- users に role を追加
-- 実行例: psql -U <user> -d <database> -f 010_alter_users_add_role.sql
--
-- 役割そのものは文字列で持たせ、階層的な強さの比較(以上/以下)が必要な場合は
-- アプリ側の ROLE_LEVELS 対応表(config/constants.php)を介して行う。
-- 数値そのものをDBに持たせない設計にすることで、
-- 将来「序列に収まらない役割」が増えてもDBのマイグレーションを発生させずに済む。
-- ============================================
ALTER TABLE users
    ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'member';
