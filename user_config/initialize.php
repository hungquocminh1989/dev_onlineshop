<?php
/**
 * 初期化処理ファイル
 *
 * フレームワーク初期化処理が書いてあります あまり変更する必要はありません
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
require ACW_USER_CONFIG_DIR . '/config.php';
require ACW_USER_CONFIG_DIR . '/db.php';
require ACW_USER_CONFIG_DIR . '/define.php';

// Core
require ACW_SYSTEM_DIR . '/class/ACWCore.php';
// Controller
require ACW_SYSTEM_DIR . '/class/ACWController.php';
// Log
require ACW_SYSTEM_DIR . '/class/ACWLog.php';
// DB
require ACW_SYSTEM_DIR . '/class/ACWDB.php';
require ACW_SYSTEM_DIR . '/class/ACWModel.php';
// View
require ACW_SYSTEM_DIR . '/class/ACWView.php';
// 配列用
require ACW_SYSTEM_DIR . '/class/ACWArray.php';
// Session
require ACW_SYSTEM_DIR . '/class/ACWSession.php';
// Error
require ACW_SYSTEM_DIR . '/class/ACWError.php';
// Mailer
//require ACW_SYSTEM_DIR . '/class/Mailer.php';

// Smarty
require ACW_SYSTEM_DIR . '/smarty/Smarty.class.php';

if (DIRECTORY_SEPARATOR == '\\') {
	// Windowsの処理切り分け パス、メール等に使います
	define('ACW_WINDOWS', true);
} else {
	define('ACW_WINDOWS', false);
}

// フレームワーク初期化
ACWCore::init();
// 「ページの有効期限切れ」を出さない(下記コードは正しい)
ACWSession::init();	//session_start()とtoken作成

header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

date_default_timezone_set('Asia/Tokyo');	// Smartyのエラー回避

/**
* コントローラー名 継承したコントローラーを使う場合変更してください
*/
ACWCore::set_config('controller_class_name', 'ACWController');
/**
* .htaccessで設定される名前
*/
ACWCore::set_config('url_cmd', 'acw_url_cmd');
/**
* Smartyタグ定義 {{}}だとコンパイル済みJavaScriptが動作しないため
*/
ACWCore::set_config('smarty_left_delimiter', '<%');
ACWCore::set_config('smarty_right_delimiter', '%>');
/**
* SmartyがHTMLエンコードする
* したくないときの書き方 <%$var nofilter%>
* textareaの書き方 <%$var|escape|nl2br nofilter%>
*/
// ACWView::templateの引数で指定
/**
* Smartyデバッグ ONにするときはconfig_xxxx.phpで上書きした方が安全
* 0:OFF
* 1:強制ON
* 2:パラメタにSMARTY_DEBUGを含むとONになる設定 例:http://www.test.com/aaa/bbb?SMARTY_DEBUG
*/
ACWCore::set_config('smarty_debug', 0);

//error_reporting(E_ALL & ~E_NOTICE);

/**
* プロジェクトごとのルート定義
*/
require ACW_USER_CONFIG_DIR . '/routes.php';
/* 終わり */