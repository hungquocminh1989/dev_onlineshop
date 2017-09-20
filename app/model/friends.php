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
		$curl = new curlpost_lib_model();
		$aa = dirname(php_ini_loaded_file()).DIRECTORY_SEPARATOR.'php.exe';
		//$res = shell_exec(ACW_BASE_URL.'batch_add_friends.php');
		//pclose(popen("start /B ". $aa . " ../batch_add_friends.php", "r"));
		//$ddd = $aa.' '.ACW_ROOT_DIR.'/batch/php/batch_add_friends.php';
		shell_exec('"'.$aa.'" "'.ACW_ROOT_DIR.'/batch_add_friends.php"') ;
		if (substr(php_uname(), 0, 7) == "Windows"){
		     //pclose(popen('start /B "'.$aa.'" "'.ACW_ROOT_DIR.'/batch/php/batch_add_friends.php"', "r")); 
		}
		else {
		     exec('yourphpscript.php 2>nul >nul');
		}
		$result['status'] = 'OK';
		return ACWView::json($result);
	}
	
}
/* ファイルの終わり */