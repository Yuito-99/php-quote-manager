<?php
/*
 * config/bootstrap_guest.php
 * 未ログイン状態でも読み込める共通初期化処理
 *
 * ログイン認証後に使用可能とする処理はここに書かず、bootstrap.php側に書くこと。
 */

// DB接続
require __DIR__ . '/db.php';
// 定数
require __DIR__ . '/constants.php';
// オプション機能設定・初期値設定ファイル
require __DIR__ . '/app_settings.php';
// 共通メソッド
require_once __DIR__ . '/../includes/functions.php';

// セッション開始
session_start();

// 会社設定取得
$company = require __DIR__ . '/company.php';