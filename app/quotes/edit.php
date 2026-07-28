<?php
/*
 * quotes/edit.php
 * 見積書管理機能の編集ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ID取得
$id = $_GET['id'] ?? null;
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 顧客情報取得（プルダウンリスト用）
$stmt = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC");
$customers = $stmt->fetchAll();

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

$stmt = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ? ORDER BY display_order");
$stmt->execute([$id]);
$quoteItems = $stmt->fetchAll();

// ページタイトル設定
$title = '見積書管理 編集';
// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * 見積書編集フォーム
 */
?>

<div class="container">
    <form class="form quotes-form" action="update.php" method="post">
        <div class="form-group">
            <label for="customer_id">顧客名</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">選択してください</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= h($customer['id']) ?>" <?= (int)$quote['customer_id'] === (int)$customer['id'] ? 'selected' : '' ?>>
                        <?= h($customer['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subject">件名</label>
            <input type="text" name="subject" id="subject" class="form-control" value="<?= h($quote['subject']) ?>" required>
        </div>
        <div class="form-group">
            <label for="issue_date">発行日</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control" value="<?= h($quote['issue_date']) ?>">
        </div>
        <div class="form-group">
            <label for="expire_date">有効期限</label>
            <input type="date" name="expire_date" id="expire_date" class="form-control" value="<?= h($quote['expire_date']) ?>">
        </div>
        <div class="form-group">
            <label for="delivery_date">受渡期日</label>
            <input type="text" name="delivery_date" id="delivery_date" class="form-control" value="<?= h($quote['delivery_date']) ?>">
        </div>
        <div class="form-group">
            <label for="delivery_place">受渡場所</label>
            <input type="text" name="delivery_place" id="delivery_place" class="form-control" value="<?= h($quote['delivery_place']) ?>">
        </div>
        <div class="form-group">
            <label for="payment_terms">支払条件</label>
            <input type="text" name="payment_terms" id="payment_terms" class="form-control" value="<?= h($quote['payment_terms']) ?>">
        </div>
        <div class="form-group">
            <label for="tax_type">課税区分</label>
            <div class="radio-inline">
                <?php foreach (QUOTE_TAX_TYPES as $value => $label): ?>
                    <input type="radio" name="tax_type" id="tax_type_<?= h($value) ?>" value="<?= h($value) ?>" <?= $quote['tax_type'] === $value ? 'checked' : '' ?>>
                    <label for="tax_type_<?= h($value) ?>"><?= h($label) ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_rate">税率(%)</label>
            <input type="number" name="tax_rate" id="tax_rate" class="form-control" value="<?= h($quote['tax_rate']) ?>">
        </div>
        <div class="form-group">
            <label for="note">備考</label>
            <textarea name="note" id="note" class="form-control"><?= h($quote['note']) ?></textarea>
        </div>
        <div class="form-group">
            <table class="quote-items-table">
                <thead>
                    <tr>
                        <th>グループ</th>
                        <th>項目</th>
                        <th>単価</th>
                        <th>数量</th>
                        <th>単位</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="quote-items-body">
                    <?php if (empty($quoteItems)): ?>
                        <!-- 明細が1件も無い見積書の場合、空の1行だけ用意しておく -->
                        <tr>
                            <td><input type="text" name="items[0][group_label]"></td>
                            <td><input type="text" name="items[0][item_name]"></td>
                            <td><input type="number" name="items[0][unit_price]" value="0" min="0"></td>
                            <td><input type="number" name="items[0][quantity]" value="1" min="0" step="1"></td>
                            <td><input type="text" name="items[0][unit]"></td>
                            <td><button type="button" class="btn btn-danger btn-remove-row">削除</button></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($quoteItems as $i => $item): ?>
                            <tr>
                                <td><input type="text" name="items[<?= $i ?>][group_label]" value="<?= h($item['group_label']) ?>"></td>
                                <td><input type="text" name="items[<?= $i ?>][item_name]" value="<?= h($item['item_name']) ?>"></td>
                                <td><input type="number" name="items[<?= $i ?>][unit_price]" value="<?= h($item['unit_price']) ?>" min="0"></td>
                                <td><input type="number" name="items[<?= $i ?>][quantity]" value="<?= h($item['quantity']) ?>" min="0" step="1"></td>
                                <td><input type="text" name="items[<?= $i ?>][unit]" value="<?= h($item['unit']) ?>"></td>
                                <td><button type="button" class="btn btn-danger btn-remove-row">削除</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" id="btn-add-row" class="btn btn-success">行を追加</button>
        </div>


        <div class="form-group">
            <label for="status">ステータス</label>
            <select name="status" id="status" class="form-control">
                <?php foreach (QUOTE_STATUSES as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $quote['status'] === $value ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="id" value="<?= h($quote['id']) ?>">

        <button type="submit" class="btn btn-success">更新</button>
    </form>

    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>

<!-- js呼出し -->
<script>
    window.initialItemIndex = <?= count($quoteItems) > 0 ? count($quoteItems) : 1 ?>;
    window.confirmMessageOnSubmit = 'この内容で更新しますか？';
</script>
<script src="/assets/js/quote-form.js"></script>

<?php
// footer
require __DIR__ . '/../includes/footer.php';
?>