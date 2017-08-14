<?php
/**
 * ログインを行う
*/
class trangchu_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('trangchu.html');
	}
	
}
/* ファイルの終わり */