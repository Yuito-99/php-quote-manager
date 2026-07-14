<?php
/**
 * DB接続設定
 * 環境に合わせて値を書き換えてください。
 * 本番運用する場合は、この値を環境変数から読む形に変更することを推奨します。
 */
$DB_HOST = 'localhost';
$DB_PORT = '5432';
$DB_NAME = 'db_name';
$DB_USER = 'db_user';
$DB_PASS = 'db_password';

try {
    $dsn = "pgsql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME}";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // 本番ではエラー詳細をそのまま出さないようにする
    die('DB接続に失敗しました: ' . $e->getMessage());
}
