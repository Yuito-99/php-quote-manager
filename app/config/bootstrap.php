<?php
/*
 * config/bootstrap.php
 * 各画面の先頭で読み込む共通初期化処理
 *
 * ここに書くのは「ほぼ全ページで必ず必要になる処理」のみ。
 * ページ固有の処理（個別のクエリなど）はここに書かず、各ファイル側に書くこと。
 * 未ログイン状態でも使用できる処理は、bootstrap_guest.phpに書くこと。
 */

// 未ログイン時用の共通初期化処理読み込み
require __DIR__ . '/bootstrap_guest.php';

// ログイン済みかどうかをチェック
if(empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
} else {
    // ログイン済みの場合、ユーザー情報を取得しておく
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch();
}