<?php
/**
 * ログインを行う
*/
class admin_model extends ACWModel
{
	public static function action_index()
	{
		$oMenu = new menu_common_model();
		$oProduct = new product_common_model();
		$oCategory = new category_common_model();
		
		$result_menu_header = $oMenu->_getMenu();
		$product_main = $oProduct->_getProduct();
		$result_category = $oCategory->_getCategory();
		
		$template_data = array();
		$template_data["header_main"] = $result_menu_header;
		$template_data["slider_main"] = "";
		$template_data["category_main"] = $result_category;
		$template_data["product_main"] = $product_main;
		$template_data["other_main"] = "";
		$template_data["footer_main"] = "";
		return ACWView::template('admin.html',$template_data);
	}
	
	public static function action_addmenu()
	{
		$result = array();
		$param = self::get_param(array(
                    'acw_url'
                    , 'm_menu_id'
                    , 'menu_name'
                    , 'menu_mobile'
                    , 'menu_link'
                    , 'menu_type'
                    , 'sort_no'
        ));
		
		$oMenu = new menu_common_model();
		
		if($param['m_menu_id']!=""){
			$oMenu->_updateMenu($param);
		}
		else{
			$oMenu->_insertMenu($param);
		}
		
		return ACWView::json($result);
	}
	
	public static function action_delmenu()
	{
		$result = array();
		$param = self::get_param(array(
                    'acw_url'
                    , 'm_menu_id'
        ));
		
		$oMenu = new menu_common_model();
		
		$oMenu->_deleteMenu($param);
		return ACWView::json($result);
	}
	
}
/* ファイルの終わり */