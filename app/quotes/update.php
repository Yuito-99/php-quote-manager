<?php
/*
 * quotes/update.php
 * 見積書DB更新処理
 */

require __DIR__ . '/../config/bootstrap.php';

// ポスト値取得
$id             = $_POST['id'] ?? null;
$cid            = $_POST['customer_id'] ?? null;
$subject        = emptyToNull($_POST['subject'] ?? '');
$issue_date     = trim($_POST['issue_date'] ?? '');
$expire_date    = trim($_POST['expire_date'] ?? '');
$delivery_date  = emptyToNull($_POST['delivery_date'] ?? '');
$delivery_place = emptyToNull($_POST['delivery_place'] ?? '');
$payment_terms  = emptyToNull($_POST['payment_terms'] ?? '');
$tax_type       = trim($_POST['tax_type'] ?? '');
$tax_rate       = trim($_POST['tax_rate'] ?? '');
$note           = emptyToNull($_POST['note'] ?? '');
$status         = $_POST['status'] ?? '';

if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}
if (!ctype_digit((string)$cid)) {
    die('不正な顧客IDです。');
}
if ($issue_date === '' || $expire_date === '') {
    die('発行日と有効期限は必須です。');
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$cid]);
$customer = $stmt->fetch();

if (!$customer) {
    die('該当の顧客が見つかりません。');
}

$c_name = $customer['name'];
$c_address = $customer['address'];

// 明細行の集計
$items = $_POST['items'] ?? [];
$validItems = [];
$subtotal = 0;

foreach ($items as $item) {
    $itemName = trim($item['item_name'] ?? '');
    if ($itemName === '') {
        continue;
    }

    $quantity = (float)($item['quantity'] ?? 0);
    $unitPrice = (float)($item['unit_price'] ?? 0);
    $amount = $quantity * $unitPrice;

    $validItems[] = [
        'group_label' => emptyToNull($item['group_label'] ?? ''),
        'item_name'   => $itemName,
        'quantity'    => $quantity,
        'unit'        => trim($item['unit'] ?? ''),
        'unit_price'  => $unitPrice,
        'amount'      => $amount,
    ];

    $subtotal += $amount;
}

$taxRate = (float)$tax_rate;

if ($tax_type === 'excluded') {
    $taxAmount = round($subtotal * $taxRate / 100);
    $totalAmount = $subtotal + $taxAmount;
} else {
    $taxAmount = null;
    $totalAmount = round($subtotal * (1 + $taxRate / 100));
    $subtotal = null;
}

/* ===============
 *  DB更新
 * =============== */

$pdo->beginTransaction();

try {
    // 1. quotesをUPDATE
    $stmt = $pdo->prepare(
        "UPDATE quotes SET customer_id = ?, customer_name = ?, customer_address = ?, subject = ?, issue_date = ?, expire_date = ?, delivery_date = ?, delivery_place = ?, payment_terms = ?, tax_type = ?, tax_rate = ?, subtotal = ?, tax_amount = ?, total_amount = ?, note = ?, status = ?, updated_at = now() WHERE id = ?"
    );
    $stmt->execute([$cid, $c_name, $c_address, $subject, $issue_date, $expire_date, $delivery_date, $delivery_place, $payment_terms, $tax_type, $tax_rate, $subtotal, $taxAmount, $totalAmount, $note, $status, $id]);

    // 2. 既存のquote_itemsを全削除
    $stmt = $pdo->prepare("DELETE FROM quote_items WHERE quote_id = ?");
    $stmt->execute([$id]);

    // 3. quote_itemsを作り直す
    $stmt = $pdo->prepare(
        "INSERT INTO quote_items (quote_id, display_order, group_label, item_name, quantity, unit, unit_price, amount)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    foreach ($validItems as $index => $item) {
        $stmt->execute([
            $id,
            $index,
            $item['group_label'],
            $item['item_name'],
            $item['quantity'],
            $item['unit'],
            $item['unit_price'],
            $item['amount'],
        ]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('更新処理に失敗しました。');
}

header('Location: index.php');
exit;
