<?php
/*
 * login_users/delete.php
 * ユーザー情報削除処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ID取得
$id = $_POST['id'] ?? null;

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 削除対象ユーザー取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

// 削除対象ユーザーが取得できなかった場合エラー
if (!$user) {
    die('対象ユーザーが存在しません。');
}

// 自分自身は削除できないようにする
if ((int)$id === (int)$current_user['id']) {
    die('自分自身のアカウントは削除できません。');
}

// 管理者権限を持つユーザーは削除させない
if ($user["role"] === "admin") {
    die('管理者権限を持つユーザーは削除できません。削除する場合は権限を変更してから削除を行ってください。');
}

// 該当のユーザーを削除
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

