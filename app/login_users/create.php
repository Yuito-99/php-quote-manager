<?php
/*
 * login_users/create.php
 * ユーザー管理機能の新規登録ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

if (!roleAtLeast($current_user, 'admin')) {
    die('この操作を行う権限がありません。');
}

// ページタイトル設定
$title = 'ユーザー管理 新規登録';

// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * ユーザー新規登録フォーム
 */
?>

<div class="container">
    <form class="form login-users-form" action="store.php" method="post">
        <div class="form-group">
            <label for="username">ユーザー名</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="display_name">表示名</label>
            <input type="text" name="display_name" id="display_name" class="form-control">
        </div>
        <div class="form-group">
            <label for="role">権限</label>
            <select name="role" id="role" class="form-control">
                <?php foreach (USER_ROLES as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $value === 'member' ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">登録</button>
    </form>

    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>


<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>