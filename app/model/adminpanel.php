<?php
/**
 * ログインを行う
*/
class adminpanel_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('adminpanel.html');
	}
	
}
/* ファイルの終わり */