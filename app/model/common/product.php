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
	
	public function _getProductTrend()
	{
		$sql = "
			SELECT
				c.ctg_name,p.*,(p.product_price+p.discount_price) AS org_price
			FROM
				m_product p
			INNER JOIN m_category c ON c.m_ctg_id = p.m_ctg_id
		";
		$sql_param = array();
		
		return $this->query($sql,$sql_param);
	}

}
/* ファイルの終わり */