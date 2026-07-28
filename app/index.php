<?php
/*
 * index.php
 * アプリケーションのトップページ
 */

require __DIR__ . '/config/bootstrap.php';

$title = 'トップページ';
require __DIR__ . '/includes/header.php';
?>

<div class="container hero">
    <h1 class="hero-title">見積書管理システム</h1>
    <p class="hero-subtitle">見積書の作成・管理</p>

    <div class="hero-links">
        <a href="dashboard.php" class="btn btn-primary btn-large">ダッシュボードを見る</a>
        <a href="quotes/index.php" class="btn btn-secondary btn-large">見積書一覧</a>
        <a href="customers/index.php" class="btn btn-secondary btn-large">顧客マスタ</a>
        <a href="login_users/index.php" class="btn btn-secondary btn-large">ユーザー管理</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>