<?php
/*
 * quotes/create.php
 * 見積書管理機能の新規登録ページ
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// 顧客情報取得（プルダウンリスト用）
$stmt = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC");
$customers = $stmt->fetchAll();

// ページタイトル設定
$title = '見積書管理 新規登録';
// header読み込み
require __DIR__ . '/../includes/header.php';


/*
 * 見積書新規登録フォーム
 */
?>

<div class="container">
    <form class="form quotes-form" action="store.php" method="post">
        <div class="form-group">
            <label for="customer_id">顧客名</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">選択してください</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= h($customer['id']) ?>"><?= h($customer['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subject">件名</label>
            <input type="text" name="subject" id="subject" class="form-control">
        </div>
        <div class="form-group">
            <label for="issue_date">発行日</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control">
        </div>
        <div class="form-group">
            <label for="expire_date">有効期限</label>
            <input type="date" name="expire_date" id="expire_date" class="form-control">
        </div>
        <div class="form-group">
            <label for="delivery_date">受渡期日</label>
            <input type="text" name="delivery_date" id="delivery_date" class="form-control" placeholder="例: 依頼時、発注後2週間">
        </div>
        <div class="form-group">
            <label for="delivery_place">受渡場所</label>
            <input type="text" name="delivery_place" id="delivery_place" class="form-control" value="<?= h(APP_SETTINGS['quote_default_delivery_place']) ?>">
        </div>
        <div class="form-group">
            <label for="payment_terms">支払条件</label>
            <input type="text" name="payment_terms" id="payment_terms" class="form-control" placeholder="例: 検収月請求、翌月支払い" value="<?= h(APP_SETTINGS['quote_default_payment_terms']) ?>">
        </div>
        <div class="form-group">
            <label for="tax_type">課税区分</label>
            <div class="radio-inline">
                <?php foreach (QUOTE_TAX_TYPES as $value => $label): ?>
                    <input type="radio" name="tax_type" id="tax_type_<?= h($value) ?>" value="<?= h($value) ?>" <?= $value === 'excluded' ? 'checked' : '' ?>>
                    <label for="tax_type_<?= h($value) ?>"><?= h($label) ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="tax_rate">税率(%)</label>
            <input type="number" name="tax_rate" id="tax_rate" class="form-control" value="<?= h(APP_SETTINGS['quote_default_tax_rate']) ?>">
        </div>
        <div class="form-group">
            <label for="note">備考</label>
            <textarea name="note" id="note" class="form-control"></textarea>
        </div>
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
                <tr>
                    <td><input type="text" name="items[0][group_label]" placeholder="任意"></td>
                    <td><input type="text" name="items[0][item_name]"></td>
                    <td><input type="number" name="items[0][unit_price]" value="0" min="0"></td>
                    <td><input type="number" name="items[0][quantity]" value="1" min="0" step="1"></td>
                    <td><input type="text" name="items[0][unit]" placeholder="例: 式、個、回"></td>
                    <td><button type="button" class="btn btn-danger btn-remove-row">削除</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="btn-add-row" class="btn btn-secondary">行を追加</button>

        <button type="submit" class="btn btn-success">登録</button>
    </form>


    <a href="index.php" class="btn btn-secondary">戻る</a>
</div>

<!-- js呼出し -->
<script src="/assets/js/quote-form.js"></script>

<?php
// footer読み込み
require __DIR__ . '/../includes/footer.php';
?>