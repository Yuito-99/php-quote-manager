<?php
/*
 * config/constants.php
 * アプリ全体で使う定数定義
 *
 * ここに定義するのは「複数の画面から参照される、決まった値の集合」。
 * 特定の1画面でしか使わない値は、ここに置かずその画面に直接書くこと。
 */

// 見積書ステータス（値 => 日本語表示名）
const QUOTE_STATUSES = [
    'draft'          => '下書き',
    'pending_review' => '社内確認中',
    'sent'           => '送付済み',
    'accepted'       => '成約',
    'rejected'       => '失注',
];

// 期限アラートの対象となるステータス(まだ動きがあり、期限を意識すべきもの)
// accepted(成約) / rejected(失注) はここに含めない
// → 期限が過ぎていても警告表示の対象外とするため
const QUOTE_STATUSES_OPEN = ['draft', 'pending_review', 'sent'];

// 税区分(値 => 日本語表示名)
const QUOTE_TAX_TYPES = [
    'excluded' => '税別',
    'included' => '税込',
];

// ユーザーの役割(値 => 日本語表示名)
const USER_ROLES = [
    'member' => '一般ユーザー',
    'admin'  => '管理者',
];

// 役割の「強さ」を数値で表した対応表(階層的な権限比較に使う)
// 数値そのものに意味はなく、大小関係(序列)だけを表す。DBには保存しない。
const ROLE_LEVELS = [
    'member' => 1,
    'admin'  => 10,
];
