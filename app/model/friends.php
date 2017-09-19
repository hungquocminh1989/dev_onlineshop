<?php
/**
 * ログインを行う
*/
class friends_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('friends.html');
	}
	
	public static function action_importuid()
	{
		$result = array('error_msg' => '');
		try{
			$model_friend = new friendsrequest_common_model();
			$curl = new curlpost_lib_model();
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'import_uid'
	        ));
	        if($param['import_uid'] != ''){
				$multi_line = preg_replace("/\r\n|\r|\n/", '  ', $param['import_uid']);
			    $arr_line = explode('  ',trim($multi_line));
		        
		        $model = new token_model();
		        foreach($arr_line as $key => $uid){
		        	$info = $curl->getUidInfo(DEFAULT_TOKEN,$uid);
		        	$param['uid'] = $uid;
		        	$param['name'] = $info['name'];
					$model_friend->_insertFriend($param);
				}
				return ACWView::json($result);
			}
			else{
				$result['error_msg'] = 'No data.';
			}
		} catch (Exception $e) {
			$result['error_msg'] = EXCEPTION_CATCH_ERROR_MSG;
			return ACWView::json($result);
		}
	}
	public static function action_execfriendsrequest()
	{
		
	}
	
}
/* ファイルの終わり */