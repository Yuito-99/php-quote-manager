<?php
/*
 * customers/index.php
 * 顧客管理機能 一覧ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// 顧客情報一覧データ取得
$stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC");
$customers = $stmt->fetchAll();

// ページタイトル設定
$title = '顧客管理 一覧';

// header読み込み
require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <a href="create.php" class="btn btn-success">新規登録</a>

    <table class="table customers-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>電話番号</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="5">登録されている顧客がありません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= h($customer['id']) ?></td>
                        <td><?= h($customer['name']) ?></td>
                        <td><?= h($customer['email']) ?></td>
                        <td><?= h($customer['phone']) ?></td>
                        <td>
                            <a href="show.php?id=<?= h($customer['id']) ?>" class="btn btn-info">詳細</a>
                            <a href="edit.php?id=<?= h($customer['id']) ?>" class="btn btn-warning">編集</a>
                            <form class="inline-form" method="post" action="delete.php" onsubmit="return confirm('この顧客を削除しますか？');">
                                <input type="hidden" name="id" value="<?= h($customer['id']) ?>">
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