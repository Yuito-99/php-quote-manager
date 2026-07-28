<?php
/*
 * login_users/show.php
 * ユーザー情報 詳細画面
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ID取得
$id = $_GET['id'] ?? null;

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 該当のユーザー情報を取得（デバッグ時にうっかりミスでリスクにならないよう、パスワードは取得しない）
$stmt = $pdo->prepare("SELECT id, username, display_name, role, created_at FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die('ユーザー情報が見つかりません。');
}

// ページタイトル設定
$title = 'ユーザー情報 詳細';

// header読み込み
require __DIR__ . '/../includes/header.php';
?>


<div class="container">
    <table class="table login-users-detail-table">
        <tr>
            <th>ID</th>
            <td><?= h($user['id']) ?></td>
        </tr>
        <tr>
            <th>ユーザー名</th>
            <td><?= h($user['username']) ?></td>
        </tr>
        <tr>
            <th>表示名</th>
            <td><?= h($user['display_name']) ?></td>
        </tr>
        <tr>
            <th>登録日</th>
            <td><?= h(formatDateTime($user['created_at'])) ?></td>
        </tr>
        <tr>
            <th>権限</th>
            <td><?= h(USER_ROLES[$user['role']] ?? $user['role']) ?></td>
        </tr>
    </table>

    <a href="edit.php?id=<?= h($user['id']) ?>" class="btn btn-warning">編集</a>
    <a href="index.php" class="btn btn-secondary">一覧へ戻る</a>
</div>

<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>