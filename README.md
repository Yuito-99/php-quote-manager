# php-quote-manager

見積書作成・管理ツール。素のPHP + PostgreSQLで構築。

## まず読むもの

1. `app/README.md` — 環境構築手順（Composer、DB接続設定、管理者アカウント作成など）はこちら
2. `create_sql/README.md` — DBのテーブル構成、設計判断の経緯はこちら

## ディレクトリ構成

```
quote-manager/
├── app/          アプリケーション本体（PHPコード一式）。Apacheのドキュメントルートに設定する
├── create_sql/   DBのテーブル作成用SQL一式
└── tools/        開発用の使い捨てスクリプト（管理者アカウント作成用など）
```

## 概要

- 顧客マスタ管理
- 見積書の作成・編集・PDF出力（明細行・グループ分け対応）
- 見積有効期限が近い案件のアラート表示（一覧・ダッシュボード）
- ユーザー管理・権限制御（一般ユーザー / 管理者）

詳細な設計判断・既知の課題は `app/README.md` と `create_sql/README.md` に記載。
