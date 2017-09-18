<?php
/**
 * ログインを行う
*/
class facebookmanager_common_model extends ACWModel
{
	public function _getAcc()
	{
		$sql = "
			SELECT
				*
			FROM
				facebook_manager
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}
	
	public function _insertRecord($param)
	{
		$sql_del = " 
			DELETE FROM facebook_manager
			WHERE user_id = :user_id;
		";
		$this->execute($sql_del,ACWArray::filter($param,array(
													'user_id'
		)));
		
		$sql = "
			INSERT INTO facebook_manager (user, pass, user_id, cookie, token1, token2, full_name, info_status, del_flg, upd_datetime)
			VALUES (:user, :pass, :user_id, :cookie, :token1, :token2, :full_name, 0, 0, NOW());
		";
		
		return $this->execute($sql,$param);
	}
	

}
/* ファイルの終わり */