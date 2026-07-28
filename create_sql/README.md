# create_sql

見積書作成・管理ツールのDB作成用SQL一式。

## 初回構築

```bash
psql -U <user> -d <database> -f run_all.sql
```

`create_sql` ディレクトリ内で実行してください（`\i` が相対パス参照のため）。

## テーブル構成

| ファイル | テーブル | 役割 |
|---|---|---|
| 001_create_users.sql | users | ログイン認証用 |
| 002_create_customers.sql | customers | 顧客マスタ（最低限の項目のみ） |
| 003_create_quotes.sql | quotes | 見積書本体 |
| 004_create_quote_items.sql | quote_items | 見積明細行 |
| 005_alter_customers_add_updated_at_and_memo.sql | customers | updated_at / internal_memo 追加 |
| 007_alter_quotes_add_status.sql | quotes | status（ステータス管理）追加 |
| 008_alter_quotes_add_delivery_and_payment_fields.sql | quotes | 受渡期日・受渡場所・支払条件・責任者 追加 |
| 009_alter_quote_items_add_group_label.sql | quote_items | 明細の大項目グループ(group_label) 追加 |
| 010_alter_users_add_role.sql | users | 権限(role) 追加 |

## 運用ルール（列追加・仕様変更時）

作成済みのファイル（001〜004）は基本的に直接編集しない。
列追加や仕様変更が発生した場合は、実行順がわかるように連番のALTER文ファイルを追加していく。

例：
```
005_alter_quotes_add_xxx.sql
006_alter_customers_add_xxx.sql
```

`run_all.sql` にも追加した分の `\i` 行を追記すること。

## 備考

- 自社情報（company_settings相当）はDBに持たず、アプリ側の `config.php` に直書きする方針のためテーブルなし。
- `quotes.customer_name` / `customer_address` は `customers` テーブルからのスナップショット。
  顧客マスタが後から編集・削除されても、発行済み見積書の表示内容が変わらないようにするため。
- `customers.internal_memo`（値引き経緯や顧客とのやり取りなど社内向けメモ）は現状 TEXT 1本で簡易運用。
  時系列で履歴管理したくなった場合は `006_create_customer_memos_FUTURE.sql` を参照（未適用・将来の拡張候補）。
  移行手順はファイル内コメントに記載。
- `quotes.subtotal` / `quotes.tax_amount` はNULL許容。税込(`tax_type='included'`)選択時は
  小計・税額の内訳を表示しない仕様のため、値そのものを持たせない設計とした。
  税別(`excluded`)の場合は必ず値が入る前提（アプリ側のロジックで担保する）。
- `quotes.status`（下書き/社内確認中/送付済み/成約/失注）を追加。詳細は
  `007_alter_quotes_add_status.sql` のコメント参照。
  期限アラートは `draft` / `pending_review` / `sent` のみを対象とし、
  `accepted` / `rejected` は期限超過していても警告表示しない（アプリ側ロジックで判定）。
- `quotes.expire_date` は具体的な日付として持たせる方針を維持（期限アラート機能のため）。
  実際の見積書運用では「見積有効期限：24か月」のような期間の文言で表記されることもあるが、
  それは PDF出力時に `expire_date` から変換して表示する形で対応する（DB設計自体は変更しない）。
- `quotes.delivery_date`（受渡期日）/ `delivery_place`（受渡場所）/ `payment_terms`（支払条件）を追加。
  実際の見積書フォーマットを参考に、他社でも使えるよう汎用項目として用意
  （全てNULL許容、未使用の会社は空のままでよい）。
  「責任者」「担当者」欄は、印刷後に押印/サインするための空欄と判断したためDB化せず、
  PDF出力側のテンプレートで空枠を描画するだけで対応する。
- `quote_items.group_label` を追加。明細を大項目でグルーピングする実際の運用（見積書サンプルの `[A]` 表記）
  に対応するための列。同じ値が連続していれば同一グループとして扱う想定（PDF/画面側の表示ロジックで対応）。
- `users.role` を追加。値は文字列（例: `member`, `admin`）で、数値そのものはDBに持たせない。
  階層的な強さの比較（管理者以上かどうか、など）が必要な場合は、
  `config/constants.php` の `ROLE_LEVELS` 対応表とヘルパー関数 `roleAtLeast()`（functions.php）を介して行う。
  文字列を正とすることで、将来「序列に収まらない役割」が増えてもDBのマイグレーションを発生させずに済む。

## 開発中の運用ルールについて（本番投入前）

現在はまだ開発中で、本番DBへの初回投入前のため、001〜004のDDLファイルも
必要に応じて直接編集している（例：subtotal/tax_amountのNULL許容化）。
「作成済みファイルを直接編集しない」運用ルールは、本番へ初回投入した後から適用する。
