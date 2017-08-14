<?php
/**
 * ログインを行う
*/
class trangchu_model extends ACWModel
{
	public static function action_index()
	{
		$oMenu = new menu_common_model();
		$result_menu = $oMenu->_getMenu();
		return ACWView::template('trangchu.html');
	}
	
}
/* ファイルの終わり */