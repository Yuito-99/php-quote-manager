<?php
/*
 * templates/pdf_quote.php
 * 見積書PDFのレイアウト(見た目)を定義するテンプレート
 *
 * 呼び出し側(quotes/pdf.php)で、以下の変数を用意してからこのファイルをrequireすること。
 *   $quote      : quotesテーブルの1件分の連想配列
 *   $quoteItems : quote_itemsテーブルの複数件分の配列
 *   $company    : 自社情報の連想配列(config.phpから)
 *
 * 他社で使う場合、基本的にはこのファイルのHTML/インラインCSSを書き換えるだけで
 * 見た目をカスタマイズできる(ロジック側のquotes/pdf.phpには手を入れなくてよい)。
 *
 * 注意: mPDFはbrowserのレンダリングエンジンとは別物(古いブラウザ相当)のため、
 * flexboxやgridは使わず、tableベースのレイアウトにしている。
 */

// 見積有効期限を「発行日からNヶ月」の文言に変換する
$issueDateObj = new DateTime($quote['issue_date']);
$expireDateObj = new DateTime($quote['expire_date']);
$monthDiff = ($expireDateObj->format('Y') - $issueDateObj->format('Y')) * 12
    + ($expireDateObj->format('m') - $issueDateObj->format('m'));
$expirePeriodText = $monthDiff > 0 ? $monthDiff . 'か月' : formatDateTime($quote['expire_date'], 'Y-m-d');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: notosansjp;
            font-size: 10.5pt;
            color: #222;
        }

        thead {
            display: table-header-group;
        }

        .doc-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            background: #2e7d4f;
            color: #fff;
            padding: 6px 0;
            margin-bottom: 16px;
            letter-spacing: 8px;
        }

        .issue-date {
            text-align: right;
            margin-bottom: 8px;
        }

        .top-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .top-table td {
            vertical-align: top;
        }

        .customer-name {
            font-size: 14pt;
            border-bottom: 2px solid #333;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .company-box {
            text-align: right;
            font-size: 9pt;
            line-height: 1.5;
        }

        .company-box .company-name {
            font-size: 12pt;
            font-weight: bold;
        }

        .lead-text {
            font-size: 9.5pt;
            margin: 8px 0 14px;
        }

        .total-box {
            border: 1px solid #333;
            margin-bottom: 12px;
        }

        .total-box .subject-row {
            border-bottom: 1px solid #333;
            padding: 6px 10px;
            font-size: 10.5pt;
        }

        .total-box .amount-row {
            padding: 8px 10px;
            font-size: 20pt;
            font-weight: bold;
        }

        .total-box .amount-row .tax-note {
            font-size: 10pt;
            font-weight: normal;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #999;
            padding: 5px 8px;
            font-size: 9.5pt;
        }

        .info-table th {
            background: #f0f0f0;
            width: 22%;
            text-align: left;
            font-weight: normal;
            color: #555;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            page-break-inside: auto;
        }

        .items-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .items-table th {
            background: #2e7d4f;
            color: #fff;
            padding: 6px 8px;
            font-size: 9.5pt;
            text-align: center;
        }

        .items-table td {
            border-bottom: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 9.5pt;
        }

        .items-table .col-item {
            width: 40%;
            text-align: left;
        }

        .items-table .col-qty {
            width: 12%;
            text-align: right;
        }

        .items-table .col-unit {
            width: 10%;
            text-align: center;
        }

        .items-table .col-price {
            width: 19%;
            text-align: right;
        }

        .items-table .col-amount {
            width: 19%;
            text-align: right;
        }

        .group-row {
            page-break-after: avoid;
        }

        .group-row td {
            background: #eef5ef;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding: 5px 8px;
        }

        .sum-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: avoid;
        }

        .sum-table td {
            padding: 4px 8px;
            font-size: 10pt;
        }

        .sum-table .label {
            width: 82%;
            text-align: right;
            border-bottom: 1px solid #ccc;
        }

        .sum-table .value {
            width: 18%;
            text-align: right;
            border-bottom: 1px solid #ccc;
        }

        .sum-table .grand-total .label,
        .sum-table .grand-total .value {
            font-weight: bold;
            font-size: 11.5pt;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .note-box {
            margin-top: 18px;
            font-size: 9.5pt;
            line-height: 1.6;
        }

        .note-box .note-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stamp-table {
            width: 200px;
            border-collapse: collapse;
            margin-left: auto;
            margin-top: 4px;
            page-break-inside: avoid;
        }

        .stamp-table th,
        .stamp-table td {
            border: 1px solid #999;
            text-align: center;
            font-size: 9pt;
        }

        .stamp-table th {
            background: #f0f0f0;
            padding: 4px;
            font-weight: normal;
        }

        .stamp-table td {
            height: 50px;
        }
    </style>
</head>

<body>

    <div class="doc-title">御見積書</div>

    <div class="issue-date"><?= h(formatDateTime($quote['issue_date'], 'Y年m月d日')) ?></div>

    <table class="top-table">
        <tr>
            <td style="width: 60%;">
                <div class="customer-name"><?= h($quote['customer_name']) ?> 御中</div>
            </td>
            <td style="width: 40%;">
                <div class="company-box">
                    <div class="company-name"><?= h($company['company_name'] ?? '') ?></div>
                    <div><?= nl2br(h($company['address'] ?? '')) ?></div>
                    <?php if (!empty($company['tel'])): ?>
                        <div>TEL / <?= h($company['tel']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($company['invoice_reg_number'])): ?>
                        <div>登録番号 / <?= h($company['invoice_reg_number']) ?></div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="lead-text">
        下記のとおり御見積申し上げます。<br>
        ご検討の上、是非ご用命くださいますよう、よろしくお願い申し上げます。
    </div>

    <div class="total-box">
        <div class="subject-row">件名 : <?= h($quote['subject']) ?></div>
        <div class="amount-row">
            合計金額　<?= h(formatMoney($quote['total_amount'])) ?>
            <span class="tax-note">（<?= h(QUOTE_TAX_TYPES[$quote['tax_type']] ?? '') ?>）</span>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <th>受渡期日</th>
            <td><?= h($quote['delivery_date'] ?: '-') ?></td>
            <th>受渡場所</th>
            <td><?= h($quote['delivery_place'] ?: '-') ?></td>
        </tr>
        <tr>
            <th>お支払条件</th>
            <td><?= h($quote['payment_terms'] ?: '-') ?></td>
            <th>見積有効期限</th>
            <td><?= h($expirePeriodText) ?></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-item">項目</th>
                <th class="col-price">単価</th>
                <th class="col-qty">数量</th>
                <th class="col-unit">単位</th>
                <th class="col-amount">金額</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $currentGroup = null;
            foreach ($quoteItems as $item):
                if (!empty($item['group_label']) && $item['group_label'] !== $currentGroup):
                    $currentGroup = $item['group_label'];
            ?>
                    <tr class="group-row">
                        <td colspan="5">[<?= h($currentGroup) ?>]</td>
                    </tr>
                <?php
                endif;
                ?>
                <tr>
                    <td class="col-item"><?= h($item['item_name']) ?></td>
                    <td class="col-price"><?= h(formatMoney($item['unit_price'])) ?></td>
                    <td class="col-qty"><?= h($item['quantity']) ?></td>
                    <td class="col-unit"><?= h($item['unit']) ?></td>
                    <td class="col-amount"><?= h(formatMoney($item['amount'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="sum-table">
        <?php if ($quote['tax_type'] === 'excluded'): ?>
            <tr>
                <td class="label">小計</td>
                <td class="value"><?= h(formatMoney($quote['subtotal'])) ?></td>
            </tr>
            <tr>
                <td class="label">消費税（<?= h($quote['tax_rate']) ?>%）</td>
                <td class="value"><?= h(formatMoney($quote['tax_amount'])) ?></td>
            </tr>
        <?php endif; ?>
        <tr class="grand-total">
            <td class="label">合計</td>
            <td class="value"><?= h(formatMoney($quote['total_amount'])) ?></td>
        </tr>
    </table>

    <?php if (!empty($quote['note'])): ?>
        <div class="note-box">
            <div class="note-title">備考</div>
            <div><?= nl2br(h($quote['note'])) ?></div>
        </div>
    <?php endif; ?>

    <table class="stamp-table">
        <tr>
            <th>責任者</th>
            <th>担当者</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>

</body>

</html>