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
		$template_data = array();
		$template_data["header_main"] = "";
		$template_data["slider_main"] = "";
		$template_data["category_main"] = "";
		$template_data["product_main"] = "";
		$template_data["other_main"] = "";
		$template_data["footer_main"] = "";
		return ACWView::template('trangchu.html',$template_data);
	}
	
}
/* ファイルの終わり */