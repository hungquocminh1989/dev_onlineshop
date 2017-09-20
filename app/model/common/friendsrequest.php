<?php
/**
 * ログインを行う
*/
class friendsrequest_common_model extends ACWModel
{
	public function getFriends()
	{
		$sql = "
			SELECT
				*
				,CASE 	WHEN status = 0 THEN 'Chưa Xử Lý'
						WHEN status = 1 THEN 'Chờ Xử Lý'
						WHEN status = 9 THEN 'Thành Công'
						WHEN status = 6 THEN 'Thất Bại'
						ELSE 'Không Xác Định'
				END as status_text
			FROM
				m_friends_request	
			ORDER BY 
				id
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

}
/* ファイルの終わり */