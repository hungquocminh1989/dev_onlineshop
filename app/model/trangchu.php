<?php
/**
 * ログインを行う
*/
class trangchu_model extends ACWModel
{
	public static function action_index()
	{
		$oMenu = new menu_common_model();
		$oProduct = new product_common_model();
		$oCategory = new category_common_model();
		
		$result_menu = $oMenu->_getMenu();
		$result_product = $oProduct->_getProduct();
		$result_category = $oCategory->_getCategory();
		
		$template_data = array();
		$template_data["header_main"] = $result_menu;
		$template_data["slider_main"] = "";
		$template_data["category_main"] = "";
		$template_data["product_main"] = "";
		$template_data["other_main"] = "";
		$template_data["footer_main"] = "";
		return ACWView::template('trangchu.html',$template_data);
	}
	
}
/* ファイルの終わり */