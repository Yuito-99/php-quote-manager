<?php
/*
 * header.php
 * 各画面共通のヘッダー
 */

$currentPath = $_SERVER['SCRIPT_NAME'];
$navActive = function ($keyword) use ($currentPath) {
    return str_contains($currentPath, $keyword) ? 'active' : '';
};

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? '' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header>
        <nav class="main-nav">
            <a href="/index.php" class="<?= $navActive('/index.php') ?>">トップ</a>
            <a href="/dashboard.php" class="<?= $navActive('/dashboard.php') ?>">ダッシュボード</a>
            <a href="/customers/" class="<?= $navActive('/customers/') ?>">顧客管理</a>
            <a href="/quotes/" class="<?= $navActive('/quotes/') ?>">見積書管理</a>
            <?php if (!empty($current_user) && roleAtLeast($current_user, 'admin')): ?>
                <a href="/login_users/" class="<?= $navActive('/login_users/') ?>">ユーザー管理</a>
            <?php endif; ?>
        </nav>
        <?php if (!empty($current_user)): ?>
            <div class="user-menu">
                <span class="user-avatar"><?= h(mb_substr($current_user['display_name'], 0, 1)) ?></span>
                <span class="user-name"><?= h($current_user['display_name']) ?> さん</span>
                <a href="/logout.php" class="btn-logout">ログアウト</a>
            </div>
        <?php endif; ?>
    </header>
    <main>
        <div>
            <h1><?php echo $title ?? '' ?></h1>
        </div>