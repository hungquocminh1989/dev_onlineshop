<?php
/**
 * Indexのサンプル
*/
class index_model extends ACWModel
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
		return ACWView::redirect(ACW_BASE_URL . 'trang-chu');
	}
}
/* ファイルの終わり */