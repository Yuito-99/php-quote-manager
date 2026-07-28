-- ============================================
-- quotes に status を追加
-- 実行例: psql -U <user> -d <database> -f 007_alter_quotes_add_status.sql
--
-- ステータスの値と意味:
--   draft           下書き(作成中、まだ誰にも見せていない)
--   pending_review  社内確認中(内容を社内でチェック中、まだ顧客には未送付)
--   sent            送付済み(顧客に送付済み、返事待ち)
--   accepted        成約
--   rejected        失注
--
-- 期限アラートの対象は draft / pending_review / sent のみ。
-- accepted / rejected は既に動きが無い見積のため、期限が過ぎても警告表示はしない。
-- (この判定はアプリ側のロジックで行う。DB制約としては単純な文字列のみ)
-- ============================================
ALTER TABLE quotes
    ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'draft';

CREATE INDEX idx_quotes_status ON quotes(status);
