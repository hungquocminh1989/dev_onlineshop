<?php
/**
 * トップ
*/
class Top_model extends ACWModel
{
	/**
	* 初期処理
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}

	/**
	* インデックス
	*/
	public static function action_index()
	{
		$param = array();
		return ACWView::template('top.html', $param);
	}
}
/* ファイルの終わり */