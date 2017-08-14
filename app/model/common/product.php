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
				m_product
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}

}
/* ファイルの終わり */