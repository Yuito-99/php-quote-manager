<?php
/*
 * functions.php
 * 各画面共通メソッド
 */

// ログイン中ユーザーの役割が、指定した役割以上の強さを持つか判定する
// 例: roleAtLeast($current_user, 'admin') → 管理者かどうか
function roleAtLeast(array $user, string $minRole): bool
{
    $userLevel = ROLE_LEVELS[$user['role']] ?? 0;
    $minLevel = ROLE_LEVELS[$minRole] ?? 0;
    return $userLevel >= $minLevel;
}

// HTMLエスケープ
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// 空文字をnullに変換する
function emptyToNull($value)
{
    $value = trim($value ?? '');
    return $value === '' ? null : $value;
}

// 日時を「年-月-日 時:分」形式に整形して表示用の文字列にする
function formatDateTime($datetime, $format = 'Y-m-d H:i')
{
    if (empty($datetime)) {
        return '';
    }
    return (new DateTime($datetime))->format($format);
}

// 残り日数計算用
function daysUntil($dateStr)
{
    $today = new DateTime('today');
    $target = new DateTime($dateStr);
    $diff = $today->diff($target);
    return $diff->invert ? -$diff->days : $diff->days; // 過去日ならマイナス
}

function formatMoney($amount)
{
    if ($amount === null) {
        return '-';
    }
    return '¥' . number_format($amount);
}