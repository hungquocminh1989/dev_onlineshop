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
		$accesstoken = "EAAAAAYsX7TsBALE8863zMCui5FRqz8oZCVOvi9ZAsxXo50KVQ5jUHlRBt8aqk08bb2kpv8cSm38LfnZCNt9EO3VINVpLJBinRuCx6KC34UH2WsuVZA9ehFtERwqTw0dT1QAZAnk7ZCZAhdZBKfL1fNElCkF4HAlDiR7VjLIEeG6MNd7Mv2epO9zfFVU1WeynE3nsZARZBv9AgYvryx5tKtq6zZAEt3wYxspmIAZD";
		
		$curl = new curlpost_lib_model();
		$res = $curl->getUidInfo($accesstoken,'100019187670508');
		//$res = $curl->getMe($accesstoken);
		print_r($res);
		die();
		//return ACWView::redirect(ACW_BASE_URL . 'trang-chu');
	}
}
/* ファイルの終わり */