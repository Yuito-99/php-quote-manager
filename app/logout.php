<?php
/*
 * logout.php
 * ログアウトページ
 */

// 初期処理
require __DIR__ . '/config/bootstrap_guest.php';

// セッションの中身を空にする
$_SESSION = [];

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header('Location: /login.php');
exit;

?>