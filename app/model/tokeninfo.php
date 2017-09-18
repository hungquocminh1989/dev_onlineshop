<?php
/**
 * ログインを行う
*/
class tokeninfo_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('tokeninfo.html');
	}
	
	public static function action_reload()
	{
		
	}
	
	
}
/* ファイルの終わり */