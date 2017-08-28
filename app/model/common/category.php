<?php
/**
 * ログインを行う
*/
class category_common_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public function _getCategory()
	{
		$sql = "
			SELECT
				*
			FROM
				m_category
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}
	
	public function _insertCategory($ctg_name)
	{
		$sql = "
			INSERT INTO m_category (ctg_name, add_datetime, upd_datetime, del_flg)
			VALUES (:ctg_name, NOW(), NOW(), 0);
		";
		$sql_param = array();
		$sql_param['ctg_name'] = $ctg_name;
		
		return $this->execute($sql,$sql_param);
	}
	
	public function _updateCategory($m_ctg_id, $ctg_name)
	{
		$sql = "
			UPDATE m_category
			SET ctg_name = :ctg_name,
				add_datetime = NOW()
			WHERE m_ctg_id = :m_ctg_id;
		";
		$sql_param = array();
		$sql_param['ctg_name'] = $ctg_name;
		$sql_param['m_ctg_id'] = $m_ctg_id;
		
		return $this->execute($sql,$sql_param);
	}
	
	public function _deleteCategory($m_ctg_id, $ctg_name)
	{
		$sql_ctg = "
			DELETE FROM m_category
			WHERE m_ctg_id = :m_ctg_id;
		";
		$sql_product = "
			DELETE FROM m_product
			WHERE m_ctg_id = :m_ctg_id;
		";
		$sql_param = array();
		$sql_param['m_ctg_id'] = $m_ctg_id;
		
		$this->execute($sql_ctg,$sql_param);
		$this->execute($sql_product,$sql_param);
	}

}
/* ファイルの終わり */