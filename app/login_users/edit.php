<?php
/*
 * login_users/edit.php
 * ユーザー管理機能 編集ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ID取得
$id = $_GET['id'] ?? null;
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 該当のユーザー情報を取得（パスワードは不要なため除外）
$stmt = $pdo->prepare("SELECT id, username, display_name, role FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

// ユーザーが取得できなかった場合はエラー終了
if (!$user) {
    die('該当のユーザーが見つかりません。');
}

// ページタイトル設定
$title = 'ユーザー管理 編集';
// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * ユーザー情報編集フォーム
 */
?>

<div class="container">
    <form class="form login-users-form" action="update.php" method="post">
        <div class="form-group">
            <label for="username">ユーザー名</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= h($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">パスワード(変更する場合のみ入力)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="変更しない場合は空欄のまま">
        </div>
        <div class="form-group">
            <label for="display_name">表示名</label>
            <input type="text" name="display_name" id="display_name" class="form-control" value="<?= h($user['display_name']) ?>">
        </div>
        <div class="form-group">
            <label for="role">権限</label>
            <select name="role" id="role" class="form-control">
                <?php foreach (USER_ROLES as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $user['role'] === $value ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="hidden" name="id" value="<?= h($user['id']) ?>">

        <button type="submit" class="btn btn-success">更新</button>
    </form>

    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>


<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>