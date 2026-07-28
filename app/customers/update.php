<?php
/*
 * customers/update.php
 * 顧客情報DB更新処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ポスト値取得
$id            = $_POST['id'] ?? null;
$name          = trim($_POST['name'] ?? '');
$address       = emptyToNull($_POST['address'] ?? '');
$contact_name  = emptyToNull($_POST['contact_name'] ?? '');
$email         = emptyToNull($_POST['email'] ?? '');
$phone         = emptyToNull($_POST['phone'] ?? '');
$internal_memo = emptyToNull($_POST['internal_memo'] ?? '');

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}
// nameは必須値のため、空の場合はエラー終了
if($name === '') {
    die('名前は必須です。');
}

// 該当の顧客情報を更新
$stmt = $pdo->prepare(
    "UPDATE customers SET name = ?, address = ?, contact_name = ?, email = ?, phone = ?, updated_at = now(), internal_memo = ? WHERE id = ?");
$stmt->execute([$name, $address, $contact_name, $email, $phone, $internal_memo, $id]);

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

