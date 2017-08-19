<?php
define('ACW_FOOTER_COPY_RIGHT', '2017 - Admin');
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

//Phone
define('HTML_PHONE', '0902676026');

//Curency
define('HTML_CURRENCY', 'đ');

//Deliver Message
define('HTML_DELIVER_TITLE', 'abc');
define('HTML_DELIVER_INFO_TITLE_1', 'Miễn Phí Giao Hàng');
define('HTML_DELIVER_INFO_CONTENT_1', 'Trong vòng 3 ngày.');
define('HTML_DELIVER_INFO_TITLE_2', 'Quà Tặng Hấp Dẫn');
define('HTML_DELIVER_INFO_CONTENT_2', 'Nhiều quà tặng ưu đãi.');
define('HTML_DELIVER_INFO_TITLE_3', 'ABC');
define('HTML_DELIVER_INFO_CONTENT_3', 'ABC');
define('HTML_DELIVER_INFO_TITLE_4', 'XYZ');
define('HTML_DELIVER_INFO_CONTENT_4', 'XYZ');

//Product Trending
define('HTML_PRODUCT_TRENDING_TITLE', 'Xu Hướng');


// ------------------------------------
// エラー出力設定
// ------------------------------------
error_reporting(E_ALL);