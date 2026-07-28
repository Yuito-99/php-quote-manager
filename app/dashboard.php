<?php
/*
 * dashboard.php
 * ダッシュボード画面
 * 期限が近い見積書(進行中ステータスのみ)を一覧表示する
 */

require __DIR__ . '/config/bootstrap.php';

// QUOTE_STATUSES_OPEN の要素数分のプレースホルダを生成
$placeholders = implode(',', array_fill(0, count(QUOTE_STATUSES_OPEN), '?'));
$limit = APP_SETTINGS['dashboard_upcoming_quotes_limit'];

$stmt = $pdo->prepare(
    "SELECT * FROM quotes
     WHERE status IN ({$placeholders})
     ORDER BY expire_date ASC
     LIMIT ?"
);
// QUOTE_STATUSES_OPEN の各要素 + limit を、この順番でバインド
$stmt->execute([...QUOTE_STATUSES_OPEN, $limit]);
$upcomingQuotes = $stmt->fetchAll();

// ページタイトル設定
$title = 'ダッシュボード';
// header読み込み
require __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2>期限が近い見積書</h2>
    <?php if (empty($upcomingQuotes)): ?>
        <p>進行中の見積書はありません。</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>顧客名</th>
                    <th>件名</th>
                    <th>有効期限</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingQuotes as $quote): ?>
                    <?php $daysLeft = daysUntil($quote['expire_date']); ?>
                    <tr class="<?= $daysLeft < 0 ? 'row-expired' : ($daysLeft <= APP_SETTINGS['quote_alert_threshold_days'] ? 'row-warning' : '') ?>">
                        <td><?= h($quote['customer_name']) ?></td>
                        <td><?= h($quote['subject']) ?></td>
                        <td>
                            <?= h(formatDateTime($quote['expire_date'], 'Y-m-d')) ?>
                            <?php if ($daysLeft < 0): ?>
                                <span class="badge badge-danger">期限切れ</span>
                            <?php elseif ($daysLeft <= APP_SETTINGS['quote_alert_threshold_days']): ?>
                                <span class="badge badge-warning">残り<?= h($daysLeft) ?>日</span>
                            <?php endif; ?>
                        </td>
                        <td><a href="quotes/show.php?id=<?= h($quote['id']) ?>" class="btn btn-info">詳細</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="quotes/index.php" class="btn btn-secondary">見積書一覧を見る</a>
    <a href="customers/index.php" class="btn btn-secondary">顧客マスタを見る</a>
    <a href="index.php" class="btn btn-secondary">トップへ戻る</a>
</div>


<?php
// footer読み込み
require __DIR__ . '/includes/footer.php';
?>