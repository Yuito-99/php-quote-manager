<?php
/*
 * config/app_settings.php
 * アプリの運用設定(初期値・機能のON/OFFなど)
 *
 * ここに置くのは「会社や運用によって変えたくなる可能性がある値」。
 * - QUOTE_STATUSES のような「選べる値の一覧そのもの」は constants.php へ
 * - DB接続情報のような「環境によって絶対に変わる値」は db.php へ
 *
 * 使う側は連想配列としてそのままアクセスする。
 * 例: APP_SETTINGS['default_tax_rate']
 */

const APP_SETTINGS = [

    // ---- 見積書(quotes)関連 ----
    'quote_default_tax_rate'     => 10.00,   // 見積作成フォームの税率初期値
    'quote_default_payment_terms' => '',     // 支払条件の初期値(空文字なら未設定=何も表示しない)
    'quote_default_delivery_place' => '',    // 受渡場所の初期値
    'quote_alert_threshold_days'   => 30,   // 期限まで何日を切ったら警告表示するか
    'dashboard_upcoming_quotes_limit' => 5,   // ダッシュボードに表示する件数

    // 機能フラグの例(今はまだ使用箇所なし。将来ここに追加していく想定)
    // 'quote_enable_item_grouping' => true,  // 明細のグループ機能を使うかどうか

    // ---- 顧客(customers)関連 ----
    // 今はまだ設定項目なし。増えたら 'customer_xxx' の形で追加していく
];
