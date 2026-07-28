<?php
/*
 * customers/store.php
 * 顧客情報DB登録処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ポスト値取得
$name          = trim($_POST['name'] ?? '');
$address       = emptyToNull($_POST['address'] ?? '');
$contact_name  = emptyToNull($_POST['contact_name'] ?? '');
$email         = emptyToNull($_POST['email'] ?? '');
$phone         = emptyToNull($_POST['phone'] ?? '');
$internal_memo = emptyToNull($_POST['internal_memo'] ?? '');

// nameは必須値のため、空の場合はエラー終了
if($name === '') {
    die('名前は必須です。');
}

// 顧客情報をDBに登録
$stmt = $pdo->prepare(
    "INSERT INTO customers (name, address, contact_name, email, phone, internal_memo) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $address, $contact_name, $email, $phone, $internal_memo]);

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

