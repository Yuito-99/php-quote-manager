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
