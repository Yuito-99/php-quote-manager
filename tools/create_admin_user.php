<?php
/*
 * tools/create_admin_user.php
 *
 * 管理者アカウントを1件だけ登録するための使い捨てスクリプト。
 * ブラウザから直接アクセスして実行する想定(実行後は忘れずに削除、
 * または本番サーバーには配置しないこと)。
 *
 * 使い方:
 *   1. 下の $username, $password を書き換える
 *   2. ブラウザで tools/create_admin_user.php を開く
 *   3. 「登録できました」と表示されたら成功
 *   4. このファイルは削除する(パスワードが書かれたまま残すと危険なため)
 */

require __DIR__ . '/../app/config/bootstrap.php';

$username     = 'admin';
$password     = 'change-me-please';
$display_name = '管理者';

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (username, password_hash, display_name, role) VALUES (?, ?, ?, 'admin')"
);
$stmt->execute([$username, $passwordHash, $display_name]);

echo '登録できました。username: ' . h($username) . '<br>';
echo 'このファイルは、確認できたら必ず削除してください。';
