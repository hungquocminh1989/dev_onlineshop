<?php
/**
 * ログインを行う
*/
class product_common_model extends ACWModel
{
	public function _getProduct()
	{
		$sql = "
			SELECT
				*
			FROM
				t_section_trans
			WHERE
				 COALESCE(m_section_id,t_ctg_section_id) = :select_id
			ORDER BY m_lang_id
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}

}
/* ファイルの終わり */