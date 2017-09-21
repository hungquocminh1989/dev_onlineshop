<?php
/**
 * ログインを行う
*/
class friends_model extends ACWModel
{
	public static function action_index()
	{
		$friend_model = new friendsrequest_common_model();
		$result['list'] = $friend_model->getFriends();
		if(file_exists(BATH_LOCK_TXT) === TRUE) {
			$result['batch_status'] = 'locked';
		}
		else{
			$result['batch_status'] = 'unlock';
		}
		$result['status'] = 'OK';
		return ACWView::template('friends.html',$result);
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
		        	//$info = $curl->getUidInfo(DEFAULT_TOKEN,$uid);
		        	$param['uid'] = $uid;
		        	$param['name'] = '';//$info['name'];
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
		if(file_exists(BATH_LOCK_TXT) === FALSE) {
			$curl = new curlpost_lib_model();
			$curl->execute_batch(ACW_BASE_URL.'batch/php/batch_add_friends.php');
			$result['batch_status'] = 'locked';
		}
		else{
			unlink(BATH_LOCK_TXT);
			$result['batch_status'] = 'unlock';
		}
		
		$result['status'] = 'OK';
		
		return ACWView::json($result);
	}
	
}
/* ファイルの終わり */