<?php
/*
 * quotes/pdf.php
 * 見積書PDF生成処理
 *
 * テンプレート(見た目)は templates/pdf_quote.php に分離してある。
 * 他社で使う場合、レイアウトを変えたい時は基本的にそちらを編集すればよい。
 */

// 初期処理
require __DIR__ . '/../config/bootstrap.php';

// Composerのオートローダー(mPDFなど、composer requireで入れたライブラリを使うために必要)
require __DIR__ . '/../vendor/autoload.php';

// ID取得
$id = $_GET['id'] ?? null;
if (!ctype_digit((string)$id)) {
    die('不正なIDです。');
}

// 該当の見積書情報を取得
$stmt = $pdo->prepare("SELECT * FROM quotes WHERE id = ?");
$stmt->execute([$id]);
$quote = $stmt->fetch();

if (!$quote) {
    die('見積書情報が見つかりません。');
}

// 明細行を取得
$stmt = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ? ORDER BY display_order");
$stmt->execute([$id]);
$quoteItems = $stmt->fetchAll();

// テンプレートをHTML文字列として組み立てる
// (画面に直接出力する代わりに、出力バッファに貯めて文字列として取り出す)
ob_start();
require __DIR__ . '/../templates/pdf_quote.php';
$html = ob_get_clean();

// mPDFでPDFを生成する
// mPDF本体には日本語フォントが同梱されていないため、
// app/assets/fonts/ に配置した Noto Sans JP を独自フォントとして登録する
$defaultConfigFonts = (new \Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfigFonts['fontDir'];

$defaultConfigFontData = (new \Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultConfigFontData['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 15,
    'margin_bottom' => 15,
    'fontDir' => array_merge($fontDirs, [__DIR__ . '/../assets/fonts']),
    'fontdata' => $fontData + [
        'notosansjp' => [
            'R' => 'NotoSansJP-Regular.ttf',
            'B' => 'NotoSansJP-Bold.ttf', // Boldファイルを用意していない場合はこの行を削除
        ],
    ],
    'default_font' => 'notosansjp',
]);
$mpdf->SetTitle('見積書_' . $quote['id']);
$mpdf->WriteHTML($html);

// ファイル名を組み立てる(顧客名_見積書.pdf のような形にする)
$fileName = '見積書_' . $quote['id'] . '.pdf';

// ブラウザにPDFとして出力する('I' = ブラウザ内で表示。ダウンロードさせたい場合は 'D' に変更する)
$mpdf->Output($fileName, 'I');
