<?php
/**
 * ログインを行う
*/
class menu_common_model extends ACWModel
{
	public function _getMenuHeader()
	{
		$sql = "
			SELECT
				*
			FROM
				m_menu
			WHERE
				menu_type = 'HEADER'	
			ORDER BY 
				sort_no
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}
	
	public function _insertMenu($param)
	{
		$sql = "
			INSERT INTO m_menu (menu_name, menu_link, menu_type, sort_no, add_datetime, upd_datetime, del_flg)
			VALUES (:menu_name, :menu_link, :menu_type, :sort_no, NOW(), NOW(), 0);
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'menu_name'
													,'menu_link'
													,'menu_type'
													,'sort_no'
		)));
	}

}
/* ファイルの終わり */