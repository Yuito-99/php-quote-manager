<?php
/*
 * quotes/index.php
 * 見積書管理機能 一覧ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// 見積書一覧データ取得
$stmt = $pdo->query(
    "SELECT quotes.*, users.display_name AS created_by_name
     FROM quotes
     LEFT JOIN users ON quotes.created_by = users.id
     ORDER BY id DESC"
);
$quotes = $stmt->fetchAll();

// ページタイトル設定
$title = '見積書管理 一覧';

// header読み込み
require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <a href="create.php" class="btn btn-success">新規登録</a>

    <table class="table customers-table">
        <thead>
            <tr>
                <th class="col-5">ID</th>
                <th>顧客名</th>
                <th>件名</th>
                <th>発行日</th>
                <th>有効期限</th>
                <th>ステータス</th>
                <th class="col-5">作成者</th>
                <th>作成日</th>
                <th>更新日</th>
                <th class="col-15">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quotes)): ?>
                <tr>
                    <td colspan="10">登録されている見積書がありません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($quotes as $quote): ?>
                    <?php // 有効期限から警告ステータスとして表示するか判定
                    $daysLeft = daysUntil($quote['expire_date']);
                    $isOpenStatus = in_array($quote['status'], QUOTE_STATUSES_OPEN, true);
                    $shouldAlert = $isOpenStatus && $daysLeft <= APP_SETTINGS['quote_alert_threshold_days'];
                    ?>
                    <tr class="<?= $shouldAlert ? ($daysLeft < 0 ? 'row-expired' : 'row-warning') : '' ?>">
                        <td class="text-center"><?= h($quote['id']) ?></td>
                        <td><?= h($quote['customer_name']) ?></td>
                        <td><?= h($quote['subject']) ?></td>
                        <td class="text-center"><?= h(formatDateTime($quote['issue_date'], 'Y-m-d')) ?></td>
                        <td class="text-center">
                            <?= h(formatDateTime($quote['expire_date'], 'Y-m-d')) ?>
                            <?php if ($shouldAlert): ?>
                                <?php if ($daysLeft < 0): ?>
                                    <span class="badge badge-danger">期限切れ</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">残り<?= h($daysLeft) ?>日</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= h(QUOTE_STATUSES[$quote['status']] ?? $quote['status']) ?></td>
                        <td class="text-center"><?= h($quote['created_by_name']) ?></td>
                        <td class="text-center"><?= h(formatDateTime($quote['created_at'])) ?></td>
                        <td class="text-center"><?= h(formatDateTime($quote['updated_at'])) ?></td>
                        <td>
                            <a href="show.php?id=<?= h($quote['id']) ?>" class="btn btn-info">詳細</a>
                            <a href="edit.php?id=<?= h($quote['id']) ?>" class="btn btn-warning">編集</a>
                            <a href="pdf.php?id=<?= h($quote['id']) ?>" class="btn btn-pdf" target="_blank">PDF出力</a>
                            <form class="inline-form" method="post" action="delete.php" onsubmit="return confirm('この見積書を削除しますか？');">
                                <input type="hidden" name="id" value="<?= h($quote['id']) ?>">
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