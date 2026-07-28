<?php
/*
 * customers/show.php
 * 顧客情報 詳細画面
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ID取得
$id = $_GET['id'] ?? null;

// IDが数字のみで構成されているかチェック(未指定・空文字・不正な値をまとめて弾く)
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 該当の顧客情報を取得
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    die('顧客情報が見つかりません。');
}

// ページタイトル設定
$title = '顧客情報 詳細';

// header読み込み
require __DIR__ . '/../includes/header.php';
?>


<div class="container">
    <table class="table customers-detail-table">
        <tr>
            <th>ID</th>
            <td><?= h($customer['id']) ?></td>
        </tr>
        <tr>
            <th>名前</th>
            <td><?= h($customer['name']) ?></td>
        </tr>
        <tr>
            <th>住所</th>
            <td><?= h($customer['address']) ?></td>
        </tr>
        <tr>
            <th>担当者名</th>
            <td><?= h($customer['contact_name']) ?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?= h($customer['email']) ?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?= h($customer['phone']) ?></td>
        </tr>
        <tr>
            <th>内部メモ</th>
            <td><?= h($customer['internal_memo']) ?></td>
        </tr>
        <tr>
            <th>初期登録日</th>
            <td><?= h(formatDateTime($customer['created_at'])) ?></td>
        </tr>
        <tr>
            <th>最終更新日</th>
            <td><?= h(formatDateTime($customer['updated_at'])) ?></td>
        </tr>
    </table>

    <a href="edit.php?id=<?= h($customer['id']) ?>" class="btn btn-warning">編集</a>
    <a href="index.php" class="btn btn-secondary">一覧へ戻る</a>
</div>

<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>