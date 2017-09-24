<?php
/**
 * ログインを行う
*/
class tokenmanager_common_model extends ACWModel
{
	public function _getAcc($id = '')
	{
		$where = "";
		
		$sql_param = array();
		if($id != ''){
			$sql_param['id'] = $id;
			$where = "WHERE id = :id";
		}
		
		$sql = "
			SELECT
				*,'' countfriend
			FROM
				m_token_manager
			$where
			ORDER BY id	
		";
		
		
		return $this->query($sql,$sql_param);
	}
	
	public function _deactiveToken($id){
		$sql_update = "
			UPDATE m_token_manager
			SET use_flg = 0 
				,last_use_datetime = NOW()
			WHERE 
				id = :id;
		";
		$sql_param = array();
		$sql_param['id'] = $id;
		$this->execute($sql_update,$sql_param);
	}
	
	public function _getActiveToken()
	{
		$sql_update = "
			UPDATE m_token_manager
			SET use_flg = 1 
			WHERE 
				(TIME_TO_SEC(TIMEDIFF(NOW(),last_use_datetime)) >= 30 OR last_use_datetime IS NULL)
				AND info_status = 1 AND (use_flg = 0 OR use_flg IS NULL)
			ORDER BY id
			LIMIT ".LIMIT_TOKEN_REQUEST.";
		";
		$this->execute($sql_update);
		
		$sql = "
			SELECT
				*
			FROM
				m_token_manager
			WHERE 
				(TIME_TO_SEC(TIMEDIFF(NOW(),last_use_datetime)) >= 30 OR last_use_datetime IS NULL)
				AND info_status = 1 AND use_flg = 1
			ORDER BY id
			LIMIT ".LIMIT_TOKEN_REQUEST.";
		";
		$sql_param = array();
		return $this->query($sql,$sql_param);
	}
	
	public function _insertRecord($param)
	{
		$sql_del = " 
			DELETE FROM m_token_manager
			WHERE user_id = :user_id;
		";
		$this->execute($sql_del,ACWArray::filter($param,array(
													'user_id'
		)));
		
		$sql = "
			INSERT INTO m_token_manager (user, pass, user_id, cookie, token1, token2, full_name, info_status, del_flg, upd_datetime)
			VALUES (:user, :pass, :user_id, :cookie, :token1, :token2, :full_name, 0, 0, NOW());
		";
		
		return $this->execute($sql,$param);
	}
	

}
/* ファイルの終わり */