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
	
	public function _insertProduct($param)
	{
		$sql = "
			INSERT INTO m_product (m_ctg_id, product_name, product_price, product_info, discount_price, group_type, add_datetime, upd_datetime, del_flg)
			VALUES (:m_ctg_id, :product_name, :product_price, :product_info, :discount_price, :group_type, NOW(), NOW(), 0);
		";
		
		return $this->execute($sql,ACWArray::filter($param,array(
													'm_ctg_id'
													,'product_name'
													,'product_price'
													,'product_info'
													,'discount_price'
													,'group_type'
		)));
	}
	
	public function _updateProduct($ctg_name)
	{
		$sql = "
			UPDATE m_product
			SET m_ctg_id = :m_ctg_id, 
				product_name = :product_name, 
				product_price = :product_price, 
				product_info = :product_info, 
				discount_price = :discount_price, 
				group_type = :group_type, 
				upd_datetime = NOW(), 
				del_flg = 0
			WHERE m_product_id = :m_product_id
		";
		$sql_param = array();
		$sql_param['m_ctg_id'] = $m_ctg_id;
		$sql_param['product_name'] = $product_name;
		$sql_param['product_price'] = $product_price;
		$sql_param['product_info'] = $product_info;
		$sql_param['discount_price'] = $discount_price;
		$sql_param['group_type'] = $group_type;
		
		return $this->execute($sql,$sql_param);
	}

}
/* ファイルの終わり */