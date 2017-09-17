<?php
/**
 * Indexのサンプル
*/
require_once ACW_APP_DIR . '/vendor/Facebook/autoload.php';
class testfb_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		//Login_model::check();	// ログインチェック
	}

	/**
	* インデックス トップページ
	*/
	public static function action_index()
	{
		$accesstoken = "EAAAAAYsX7TsBAErF5SZBo1Ch4AtMKwx9alZCDhuYqWpYwKZBUpqsIDzk9OXnL8fYIXZBmlUTG0ZBO9h7GFwnryflQwU51I2rW9ZCZClmvEgNKGWZADG9fuJlRRAX9PJpsYPeXMop5fdtRLqrVTAgXIIAu7z0Tpqt5ybb9GgwYVDwCBpSBbb8AX0oZAxzZAljZBC354NafXNSQdE7X08t1k3IAf7t2tsWUTIP9cZD";
		
		$curl = new curlpost_lib_model();
		$res = $curl->addFriend($accesstoken);
		print_r($res);
		die();
		//return ACWView::redirect(ACW_BASE_URL . 'trang-chu');
	}
}
/* ファイルの終わり */