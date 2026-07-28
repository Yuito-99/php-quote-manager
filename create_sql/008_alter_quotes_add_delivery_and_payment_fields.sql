-- ============================================
-- quotes に 受渡期日・受渡場所・支払条件 を追加
-- 実行例: psql -U <user> -d <database> -f 008_alter_quotes_add_delivery_and_payment_fields.sql
--
-- いずれも会社によって使う/使わないが分かれる汎用項目のため、全てNULL許容。
--
-- delivery_date（受渡期日）は具体的な日付ではなく、
-- 「依頼時」「発注後2週間」のような自由記述の文言を想定するため VARCHAR。
-- (quotes.expire_date とは別物。expire_date は見積の有効期限そのもの)
--
-- 「責任者」「担当者」欄は、見積書サンプルを確認した結果、
-- システムへの入力項目ではなく印刷後に押印/サインするための空欄と判断したため、
-- DBには持たせない。PDF出力側のテンプレートで空枠を描画するだけで対応する。
-- ============================================
ALTER TABLE quotes
    ADD COLUMN delivery_date VARCHAR(50),
    ADD COLUMN delivery_place VARCHAR(100),
    ADD COLUMN payment_terms VARCHAR(100);
