<?php
/*
 * login.php
 * ログインページ
 */

// 未ログイン時用の共通初期化処理を読み込む
require __DIR__ . '/config/bootstrap_guest.php';

$error = '';

// POST(ログイン試行)の場合のみ認証処理を行う
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ユーザー名からDBのレコードを取得
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // ユーザーが見つかり、かつパスワードが一致するか確認
    if ($user && password_verify($password, $user['password_hash'])) {
        // 認証成功：セッションにログイン情報を保存
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['display_name'] = $user['display_name'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'ユーザー名またはパスワードが正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | 見積書管理システム</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h1 class="login-title">見積書管理システム</h1>
        <p class="login-subtitle">ログインしてください</p>

    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

        <form method="post" action="login.php" class="login-form">
        <div class="form-group">
            <label for="username">ユーザー名</label>
                <input type="text" name="username" id="username" class="form-control" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-control" required>
        </div>
            <button type="submit" class="btn btn-primary btn-block">ログイン</button>
    </form>
</div>
</body>
</html>
