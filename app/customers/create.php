<?php
/*
 * customers/create.php
 * 顧客管理機能の新規登録ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ページタイトル設定
$title = '顧客管理 新規登録';

// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * 顧客新規登録フォーム
 */
?>

<div class="container">
    <form class="form customers-form" action="store.php" method="post">
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control">
        </div>
        <div class="form-group">
            <label for="contact_name">担当者名</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control">
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="form-group">
            <label for="internal_memo">内部メモ</label>
            <textarea name="internal_memo" id="internal_memo" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">登録</button>
    </form>

    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>


<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>