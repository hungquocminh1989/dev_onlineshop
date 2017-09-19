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
	
	public function _insertFriend($param)
	{
		$sql = "
			INSERT INTO m_friends_request (uid, name, status, upd_datetime)
			VALUES (:uid, :name, 0, NOW());
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'uid'
													,'name'
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