<?php
/**
 * ログインを行う
*/
class token_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('token.html');
	}
	
	public static function action_gettokenmulti()
	{
		try{
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'multi_line'
	        ));
	        if($param['multi_line'] != ''){
				$multi_line = preg_replace("/\r\n|\r|\n/", '  ', $param['multi_line']);
			    $arr_line = explode('  ',$multi_line);
		        
		        $model = new token_model();
		        foreach($arr_line as $key => $value){
					$arr_user = explode('|',$value);
					$param['email'] = $arr_user[0];
					$param['pass'] = $arr_user[1];
					$result = $model->run_get_token($param['email'],$param['pass']);
					ACWLog::debug_var('get_multi_token',$value."=>".$result['error_msg']);
				}
				
				return ACWView::json($result);
			}
	        
			
		} catch (Exception $e) {
			$result = array('error_msg' => 'Occur error execute!');
			return ACWView::json($result);
		}
		  
	}
	
	public static function action_gettoken()
	{
		try{
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'email'
	                    , 'pass'
	        ));
	        $model = new token_model();
	        $result = $model->run_get_token($param['email'],$param['pass']);
			return ACWView::json($result);
			
		} catch (Exception $e) {
			$result = array('error_msg' => 'Occur error execute!');
			return ACWView::json($result);
		}
	}
	
	public function run_get_token($email, $pass){
		$pagetoken = new pagetoken_lib_model();
        $iphonetoken = new iphonetoken_lib_model();
        $curl = new curlpost_lib_model();
		$result = $pagetoken->get_token($email,$pass);
        if($result['error_msg'] == '' && $result['access_token'] != ''){
			$token_str = $iphonetoken->get_token($email,$pass);
			if($token_str != ''){
				$result['access_token'] .= ";" . $token_str;
				
				//Insert DB
				$sql_arr = array();
				$arr = explode(';',$result['access_token']);
				$info = $curl->meInfo($arr[6]);
				
				$sql_arr['user'] = $arr[0];
				$sql_arr['pass'] = $arr[1];
				$sql_arr['user_id'] = $info['id'];
				$sql_arr['cookie'] = $arr[2].';'.$arr[3].';'.$arr[4];
				$sql_arr['token1'] = $arr[5];
				$sql_arr['token2'] = $arr[6];
				$sql_arr['full_name'] = $info['name'];
				$fb_db = new facebookmanager_common_model();
				$fb_db->_insertRecord($sql_arr);
			}
		}
		return $result;
	}
	
}
/* ファイルの終わり */