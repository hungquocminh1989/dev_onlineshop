<?php
define('AKAGANE_COPYRIGHT', 'Copyright (C) 2014 Akagane Co., Ltd. All Rights Reserved.');//Add - Minh VNIT - 2014/08/04

/*
 * CMSタイトル
 */
define('AKAGANE_TITLE', 'Pimlus'); //Edit LIXD-13 hungtn VNIT 20150803

/*
 * シリーズ情報
 */
define('AKAGANE_STRAGE_PATH', ACW_ROOT_DIR . '/data/');

// エクスポート(インポート)処理の添付ファイル名のエンコード
define('AKAGANE_EXPORT_FILE_ENCODING_XML',    'UTF-8');    // XML 内に記載している文字コード
define('AKAGANE_EXPORT_FILE_ENCODING_SYSTEM', 'SJIS-win'); // ファイルシステム(OS)の文字コード(実ファイル名で保存の際に使用)

// ------------------------------------
// エラー出力設定
// ------------------------------------
error_reporting(E_ALL);