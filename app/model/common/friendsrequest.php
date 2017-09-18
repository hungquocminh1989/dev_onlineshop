<?php
/**
 * ログインを行う
*/
class friendsrequest_common_model extends ACWModel
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
	
	public function _getMenu()
	{
		$sql = "
			SELECT
				*
			FROM
				m_menu
			WHERE
				del_flg = 0	
			ORDER BY 
				sort_no
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}
	
	public function _insertMenu($param)
	{
		$sql = "
			INSERT INTO m_menu (menu_name, menu_link, menu_type, sort_no,mobile_display, add_datetime, upd_datetime, del_flg)
			VALUES (:menu_name, :menu_link, :menu_type, :sort_no,:menu_mobile, NOW(), NOW(), 0);
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'menu_name'
													,'menu_mobile'
													,'menu_link'
													,'menu_type'
													,'sort_no'
		)));
	}
	
	public function _updateMenu($param)
	{
		$sql = "
			UPDATE m_menu 
			SET
				menu_name = :menu_name, 
				menu_link = :menu_link, 
				menu_type = :menu_type, 
				sort_no = :sort_no,
				mobile_display = :menu_mobile, 
				upd_datetime = NOW()
			WHERE m_menu_id = :m_menu_id;
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'm_menu_id'
													,'menu_name'
													,'menu_mobile'
													,'menu_link'
													,'menu_type'
													,'sort_no'
		)));
	}
	
	public function _deleteMenu($param)
	{
		$sql = "
			DELETE FROM m_menu
			WHERE m_menu_id = :m_menu_id;
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'm_menu_id'
		)));
	}

}
/* ファイルの終わり */