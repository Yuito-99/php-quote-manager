<?php
/*
 * quotes/delete.php
 * 見積書削除処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ID取得
$id = $_POST['id'] ?? null;

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 該当の見積書を削除
$stmt = $pdo->prepare("DELETE FROM quotes WHERE id = ?");
$stmt->execute([$id]);

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

