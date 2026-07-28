<?php
/*
 * quotes/show.php
 * 見積書管理 詳細画面
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

// 該当の見積書情報を取得
$stmt = $pdo->prepare(
    "SELECT quotes.*, users.display_name AS created_by_name
     FROM quotes
     LEFT JOIN users ON quotes.created_by = users.id
     WHERE quotes.id = ?"
);
$stmt->execute([$id]);
$quote = $stmt->fetch();

if (!$quote) {
    die('見積書情報が見つかりません。');
}
// 見積書項目データ取得
$stmt = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ? ORDER BY display_order");
$stmt->execute([$id]);
$quoteItems = $stmt->fetchAll();

// ページタイトル設定
$title = '見積書管理 詳細';
// header読み込み
require __DIR__ . '/../includes/header.php';
?>


<div class="container">
    <table class="table quotes-detail-table">
        <tr>
            <th>ID</th>
            <td><?= h($quote['id']) ?></td>
        </tr>
        <tr>
            <th>顧客名</th>
            <td><?= h($quote['customer_name']) ?></td>
        </tr>
        <tr>
            <th>顧客住所</th>
            <td><?= nl2br(h($quote['customer_address'])) ?></td>
        </tr>
        <tr>
            <th>件名</th>
            <td><?= h($quote['subject']) ?></td>
        </tr>
        <tr>
            <th>発行日</th>
            <td><?= h(formatDateTime($quote['issue_date'], 'Y-m-d')) ?></td>
        </tr>
        <tr>
            <th>有効期限</th>
            <td><?= h(formatDateTime($quote['expire_date'], 'Y-m-d')) ?></td>
        </tr>
        <tr>
            <th>受渡期日</th>
            <td><?= h($quote['delivery_date']) ?></td>
        </tr>
        <tr>
            <th>受渡場所</th>
            <td><?= h($quote['delivery_place']) ?></td>
        </tr>
        <tr>
            <th>支払条件</th>
            <td><?= h($quote['payment_terms']) ?></td>
        </tr>
        <tr>
            <th>ステータス</th>
            <td><?= h(QUOTE_STATUSES[$quote['status']] ?? $quote['status']) ?></td>
        </tr>
        <tr>
            <th>課税区分</th>
            <td><?= h(QUOTE_TAX_TYPES[$quote['tax_type']] ?? $quote['tax_type']) ?></td>
        </tr>
        <tr>
            <th>課税率</th>
            <td><?= h($quote['tax_rate']) ?>%</td>
        </tr>
    </table>

    <h2>明細</h2>
    <table class="table quote-items-table is-readonly">
        <thead>
            <tr>
                <th>品目</th>
                <th>数量</th>
                <th>単位</th>
                <th>単価</th>
                <th>金額</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quoteItems)): ?>
                <tr>
                    <td colspan="5">明細が登録されていません。</td>
                </tr>
            <?php else: ?>
                <?php $currentGroup = null; ?>
                <?php foreach ($quoteItems as $item): ?>
                    <?php if (!empty($item['group_label']) && $item['group_label'] !== $currentGroup): ?>
                        <?php $currentGroup = $item['group_label']; ?>
                        <tr class="group-row">
                            <td colspan="5">[<?= h($currentGroup) ?>]</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td><?= h($item['item_name']) ?></td>
                        <td><?= h($item['quantity']) ?></td>
                        <td><?= h($item['unit']) ?></td>
                        <td><?= h(formatMoney($item['unit_price'])) ?></td>
                        <td><?= h(formatMoney($item['amount'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="table quotes-detail-table">
        <tr>
            <th>小計</th>
            <td><?= h(formatMoney($quote['subtotal'])) ?></td>
        </tr>
        <tr>
            <th>税額</th>
            <td><?= h(formatMoney($quote['tax_amount'])) ?></td>
        </tr>
        <tr>
            <th>合計金額</th>
            <td><?= h(formatMoney($quote['total_amount'])) ?></td>
        </tr>
        <tr>
            <th>備考</th>
            <td><?= nl2br(h($quote['note'])) ?></td>
        </tr>
        <tr>
            <th>作成者</th>
            <td><?= h($quote['created_by_name']) ?></td>
        </tr>
        <tr>
            <th>初期登録日</th>
            <td><?= h(formatDateTime($quote['created_at'])) ?></td>
        </tr>
        <tr>
            <th>最終更新日</th>
            <td><?= h(formatDateTime($quote['updated_at'])) ?></td>
        </tr>
    </table>

    <a href="pdf.php?id=<?= h($quote['id']) ?>" class="btn btn-pdf" target="_blank">PDF出力</a>
    <a href="edit.php?id=<?= h($quote['id']) ?>" class="btn btn-warning">編集</a>
    <a href="index.php" class="btn btn-secondary">一覧へ戻る</a>
</div>

<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>