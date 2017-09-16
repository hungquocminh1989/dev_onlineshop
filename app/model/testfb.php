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
		$accesstoken = "EAAVjM3Dif5YBAGVyW1vsFSGAdWcVJ9AM2SbFMOXCtULbgZCbTX8qKV3pnJUXWI5z1eYEjMTI6Ala4xZBLz8UNyv3nVVZA7NgukMKEZB1fZAbmaN59b00HrUZCKzflteZCgBoSAZB2yS49au2DNHzwaWG0HNeg2GRviP4xd6ZClx8Dnxq6MVqAoAJyDkfOsy48LZBUZD";
		$fb = new Facebook\Facebook([
			 'app_id' => '1516447471927190',
        	'app_secret' => 'e1249d40b48864326f23bb05631fa15c',
			'default_graph_version' => 'v2.4',
			// . . .
		]);
		//$fb->setDefaultAccessToken('');
		$a= array();
		$a[] = "111";
		$a[] = "222";
		$response = $fb->get('/me?fields=cover', $accesstoken);
		var_dump($response->getBody());
		echo 123;die();
		//return ACWView::redirect(ACW_BASE_URL . 'trang-chu');
	}
}
/* ファイルの終わり */