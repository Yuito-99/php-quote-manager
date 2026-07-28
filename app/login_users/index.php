<?php
/*
 * login_users/index.php
 * ユーザー管理機能 一覧ページ
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ユーザー情報一覧データ取得（デバッグ時にうっかりミスでリスクにならないよう、パスワードは取得しない）
$stmt = $pdo->query("SELECT id, username, display_name, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// ページタイトル設定
$title = 'ユーザー管理 一覧';

// header読み込み
require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <a href="create.php" class="btn btn-success">新規登録</a>

    <table class="table login-users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>表示名</th>
                <th>権限</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5">登録されているユーザーがいません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= h($user['id']) ?></td>
                        <td><?= h($user['username']) ?></td>
                        <td><?= h($user['display_name']) ?></td>
                        <td><?= h(USER_ROLES[$user['role']] ?? $user['role']) ?></td>
                        <td>
                            <a href="show.php?id=<?= h($user['id']) ?>" class="btn btn-info">詳細</a>
                            <a href="edit.php?id=<?= h($user['id']) ?>" class="btn btn-warning">編集</a>
                            <form class="inline-form" method="post" action="delete.php" onsubmit="return confirm('このユーザーを削除しますか？');">
                                <input type="hidden" name="id" value="<?= h($user['id']) ?>">
                                <button type="submit" class="btn btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="../index.php" class="btn btn-secondary">トップへ戻る</a>
</div>

<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>