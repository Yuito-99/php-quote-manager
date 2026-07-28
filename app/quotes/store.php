<?php
/*
 * quotes/store.php
 * 見積書管理DB登録処理
 * 
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// ポスト値取得
$cid            = trim($_POST['customer_id'] ?? '');
$subject        = emptyToNull($_POST['subject'] ?? '');
$issue_date     = trim($_POST['issue_date'] ?? '');
$expire_date    = trim($_POST['expire_date'] ?? '');
$delivery_date  = emptyToNull($_POST['delivery_date'] ?? '');
$delivery_place = emptyToNull($_POST['delivery_place'] ?? '');
$payment_terms  = emptyToNull($_POST['payment_terms'] ?? '');
$tax_type       = trim($_POST['tax_type'] ?? '');
$tax_rate       = trim($_POST['tax_rate'] ?? '');
$note           = emptyToNull($_POST['note'] ?? '');

// 不正データ・不足値チェック
if (!ctype_digit((string)$cid)) {
    die('不正な顧客IDです。');
}
if ($issue_date === '' || $expire_date === '') {
    die('発行日と有効期限は必須です。');
}

// IDから顧客情報を取得
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$cid]);
$customer = $stmt->fetch();

if (!$customer) {
    die('該当の顧客が見つかりません。');
}

// 取得した顧客情報から、必要情報を抽出
$c_name = $customer['name'];
$c_address = $customer['address'];

// ステータスを下書きに設定
$status = 'draft';

$items = $_POST['items'] ?? [];
$validItems = [];
$subtotal = 0;

foreach ($items as $item) {
    $itemName = trim($item['item_name'] ?? '');

    // 品目名が空の行はスキップ(未入力の余った行として扱う)
    if ($itemName === '') {
        continue;
    }

    $quantity = (float)($item['quantity'] ?? 0);
    $unitPrice = (float)($item['unit_price'] ?? 0);
    $amount = $quantity * $unitPrice;

    $validItems[] = [
        'group_label' => emptyToNull($item['group_label'] ?? ''),
        'item_name'  => $itemName,
        'quantity'   => $quantity,
        'unit'       => trim($item['unit'] ?? ''),
        'unit_price' => $unitPrice,
        'amount'     => $amount,
    ];

    $subtotal += $amount;
}

$taxRate = (float)$tax_rate;

if ($tax_type === 'excluded') {
    // 税別：小計・税額をそれぞれ保存
    $taxAmount = round($subtotal * $taxRate / 100);
    $totalAmount = $subtotal + $taxAmount;
} else {
    // 税込：小計・税額はNULL、合計金額だけ算出(税を上乗せした金額)
    $taxAmount = null;
    $totalAmount = round($subtotal * (1 + $taxRate / 100));
    $subtotal = null; // DBにはNULLで保存する(以前の設計通り)
}


/* ===============
 *  DB登録
 * =============== */

// 見積書をDBに登録
$stmt = $pdo->prepare(
    "INSERT INTO quotes (customer_id, customer_name, customer_address, subject, issue_date, expire_date, delivery_date, delivery_place, payment_terms, tax_type, tax_rate, subtotal, tax_amount, total_amount, note, created_by, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->execute([
    $cid,
    $c_name,
    $c_address,
    $subject,
    $issue_date,
    $expire_date,
    $delivery_date,
    $delivery_place,
    $payment_terms,
    $tax_type,
    $tax_rate,
    $subtotal,
    $taxAmount,
    $totalAmount,
    $note,
    $current_user['id'],
    $status,
]);

// 直前にINSERTした見積書自身のIDを取得
$quoteId = $pdo->lastInsertId();

// 見積書項目をDBに登録
$stmt = $pdo->prepare(
    "INSERT INTO quote_items (quote_id, display_order, group_label, item_name, quantity, unit, unit_price, amount)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

foreach ($validItems as $index => $item) {
    $stmt->execute([
        $quoteId,
        $index,
        $item['group_label'],
        $item['item_name'],
        $item['quantity'],
        $item['unit'],
        $item['unit_price'],
        $item['amount'],
    ]);
}

// 一覧画面へリダイレクト
header('Location: index.php');
exit;

