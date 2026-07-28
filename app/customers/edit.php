<?php
/*
 * customers/edit.php
 * 顧客管理機能の編集ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ID取得
$id = $_GET['id'] ?? null;
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 顧客情報取得
$customer = null;
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

// 顧客情報が取得できなかった場合はエラー終了
if (!$customer) {
    die('該当の顧客情報が見つかりません。');
}

// ページタイトル設定
$title = '顧客管理 編集';
// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * 顧客編集フォーム
 */
?>

<div class="container">
    <form class="form customers-form" action="update.php" method="post">
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= h($customer['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control" value="<?= h($customer['address']) ?>">
        </div>
        <div class="form-group">
            <label for="contact_name">担当者名</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" value="<?= h($customer['contact_name']) ?>">
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= h($customer['email']) ?>">
        </div>
        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?= h($customer['phone']) ?>">
        </div>
        <div class="form-group">
            <label for="internal_memo">内部メモ</label>
            <textarea name="internal_memo" id="internal_memo" class="form-control"><?= h($customer['internal_memo']) ?></textarea>
        </div>

        <input type="hidden" name="id" value="<?= h($customer['id']) ?>">

        <button type="submit" class="btn btn-success">更新</button>
    </form>

    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>


<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>