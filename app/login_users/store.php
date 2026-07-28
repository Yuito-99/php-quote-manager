<?php
/*
 * login_users/store.php
 * ユーザー情報DB登録処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ポスト値取得
$username     = trim($_POST['username'] ?? '');
$password     = $_POST['password'] ?? '';
$display_name = emptyToNull($_POST['display_name'] ?? '');
$role         = trim($_POST['role'] ?? 'member');

// 必須値の空チェック
if ($username === '' || $password === '') {
    die('ユーザー名とパスワードは必須です。');
}

// username の重複チェック
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    die('そのユーザー名は既に使用されています。');
}

// 権限指定が定義されているものか判定
if (!array_key_exists($role, USER_ROLES)) {
    die('不正な権限指定です。');
}

// パスワードをハッシュ変換
$passwordHash = password_hash($password, PASSWORD_DEFAULT);


/* ===============
 *  DB登録
 * =============== */
// ユーザー情報をDBに登録
$stmt = $pdo->prepare(
    "INSERT INTO users (username, password_hash, display_name, role) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$username, $passwordHash, $display_name, $role]);


// 一覧画面へリダイレクト
header('Location: index.php');
exit;

