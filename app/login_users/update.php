<?php
/*
 * login_users/update.php
 * ユーザー情報DB更新処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ポスト値取得
$id            = $_POST['id'] ?? null;
$username     = trim($_POST['username'] ?? '');
$password     = $_POST['password'] ?? '';
$display_name = emptyToNull($_POST['display_name'] ?? '');
$role         = trim($_POST['role'] ?? 'member');

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}
// usernameのみ必須(passwordは空欄=変更なしを許容するため、ここではチェックしない)
if ($username === '') {
    die('ユーザー名は必須です。');
}

// username の重複チェック
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->execute([$username, $id]);
if ($stmt->fetch()) {
    die('そのユーザー名は既に使用されています。');
}

// 権限指定が定義されているものか判定
if (!array_key_exists($role, USER_ROLES)) {
    die('不正な権限指定です。');
}

/* ===============
 *  DB登録
 * =============== */
// ユーザー情報を更新
if ($password !== '') {
    // パスワードが入力されていれば、ハッシュ化してSET句に含める
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET username = ?, password_hash = ?, display_name = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $passwordHash, $display_name, $role, $id]);
} else {
    // パスワードが空なら、password_hash列は更新しない(SET句から除外)
    $stmt = $pdo->prepare("UPDATE users SET username = ?, display_name = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $display_name, $role, $id]);
}

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

