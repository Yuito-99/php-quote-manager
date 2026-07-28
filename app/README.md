# php-quote-manager

見積書作成・管理ツール。素のPHP + PostgreSQLで構築。

## 環境構築手順

1. Apache + PHP + PostgreSQL の実行環境を用意し、`app/` をドキュメントルートに設定する
2. Composerで依存ライブラリ(mPDFなど)をインストール
   ```bash
   cd app
   composer install
   ```
3. `config/db.php` を、実際のDB接続情報に書き換える
   ```php
   $DB_HOST = 'localhost';
   $DB_PORT = '5432';
   $DB_NAME = 'your_database_name';
   $DB_USER = 'your_db_user';
   $DB_PASS = 'your_db_password';
   ```
4. `config/company.php` を、実際の自社情報に書き換える(見積書PDFに印字される)
5. DBを構築する
   ```bash
   cd ../create_sql
   psql -U <user> -d <database> -f run_all.sql
   ```
   テーブル構成・各SQLファイルの役割は `create_sql/README.md` を参照。
6. 管理者アカウントを作成する
   - `tools/create_admin_user.php` 内の `$username` / `$password` を書き換える
   - ブラウザで `tools/create_admin_user.php` にアクセスして実行
   - 「登録できました」と表示されたら成功
   - **確認できたら、パスワードが平文で書かれたこのファイルは必ず削除する**
     （再度アカウントを作りたくなった場合は、Gitの履歴からテンプレートを復元すればよい）
7. `login.php` にアクセスし、作成した管理者アカウントでログインできることを確認する

## ディレクトリ構成

```
app/
├── config/
│   ├── db.php               DB接続設定
│   ├── company.php          自社情報(見積書PDFに印字される情報)
│   ├── bootstrap.php        共通初期化(ログイン必須ページ用)
│   ├── bootstrap_guest.php  共通初期化(未ログインでもアクセス可能なページ用)
│   ├── constants.php        選べる値の一覧(ステータス、税区分、権限など。基本変更しない)
│   ├── app_settings.php     運用設定(初期値・機能フラグなど。会社ごとに変えたい値はここに追加していく)
│   └── functions.php        共通ヘルパー関数
├── includes/
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/style.css        アプリ全体の共通スタイル
│   ├── js/quote-form.js     見積フォーム(create/edit)共通のJavaScript
│   └── fonts/                PDF出力用の日本語フォント(Noto Sans JP)
├── templates/
│   └── pdf_quote.php        見積書PDFのレイアウト(他社で使う場合はここを編集する)
├── customers/                顧客マスタ機能一式
├── quotes/                   見積書機能一式(明細行・PDF出力含む)
├── login_users/               ユーザー管理機能一式(管理者権限が必要)
├── tools/                     開発用の使い捨てスクリプト置き場
├── login.php
├── logout.php
├── index.php                  トップページ
└── dashboard.php              ダッシュボード(期限が近い見積書一覧)
```

## 認証・権限について

- `login.php` / `logout.php` は `bootstrap_guest.php` を読み込む(未ログインでもアクセス可能にするため)
- それ以外の全画面は `bootstrap.php` を読み込む(未ログインなら `/login.php` へ強制リダイレクト)
- `users.role` によって権限を制御。`admin` のみユーザー管理機能(`login_users/`配下)にアクセス可能
  （判定は `roleAtLeast()` 関数、詳細は `create_sql/README.md` 参照）

## 既知の課題・意図的に見送った対応

### CSRF対策は未実装

`delete.php` 系（顧客削除など、状態を変更する操作）について、CSRFトークンによる検証は現時点で未実装。

- POST送信 + `confirm()` による誤操作防止は入っているが、これはCSRF対策としては不十分
- 本システムは社内の限られた人数のみが使用し、機密性がそれほど高くない前提のため、開発優先度を下げて意図的に見送った
- 本格対応する場合は、フォーム表示時にトークンを発行してセッションに保存 + hiddenで埋め込み、
  処理側で `hash_equals()` を使って照合する方式を想定（`login.php`/`logout.php`のような実害の軽い操作は対象外でよい）

### company設定はDB化せず設定ファイル直書き

単一企業でしか使わない前提のため、`config/company.php` に直書きする方針。詳細は `create_sql/README.md` 参照。

### customers.internal_memo は時系列履歴になっていない

現状 TEXT 1本で簡易運用。履歴管理が必要になった場合の移行先テーブル定義は
`create_sql/006_create_customer_memos_FUTURE.sql` に用意済み（未適用）。

### 明細行のリアルタイム金額計算は未実装

JavaScriptでの見た目上の即時計算は行っておらず、保存(登録/更新)して初めて小計・税額・合計が確定して見える。
実際の計算はサーバー側(store.php/update.php)で行っており、これは改ざん防止のため意図的な設計。
リアルタイム表示はUXの改善であり、優先度を下げて見送った。
