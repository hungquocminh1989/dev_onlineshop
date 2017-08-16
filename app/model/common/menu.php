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

}
/* ファイルの終わり */