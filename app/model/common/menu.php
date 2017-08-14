<?php
/**
 * ログインを行う
*/
class menu_common_model extends ACWModel
{
	public function _getMenu()
	{
		$sql = "
			SELECT
				*
			FROM
				m_menu
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}

}
/* ファイルの終わり */