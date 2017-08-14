<?php
/**
 * Indexのサンプル
*/
class Index_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}

	/**
	* インデックス トップページ
	*/
	public static function action_index()
	{
		return ACWView::redirect(ACW_BASE_URL . 'top');
	}
}
/* ファイルの終わり */