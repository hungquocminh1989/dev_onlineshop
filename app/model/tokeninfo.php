<?php
/**
 * ログインを行う
*/
class tokeninfo_model extends ACWModel
{
	public static function action_index()
	{
		$model = new tokenmanager_common_model();
		$curl = new curlpost_lib_model();
		$res = $model->_getAcc();
		
		foreach($res as $k => $value){
			$res[$k]['countfriend'] = $curl->getCountFriend($value['token2'],$value['user_id']);
		}
		$return['list'] = $res;
		
		return ACWView::template('tokeninfo.html',$return);
	}
	
	public static function action_reload()
	{
		
	}
	
	
}
/* ファイルの終わり */