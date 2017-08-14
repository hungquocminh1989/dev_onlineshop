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

}
/* ファイルの終わり */