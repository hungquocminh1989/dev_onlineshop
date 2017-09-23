<?php
$_SERVER["REMOTE_ADDR"] = "batch in process...";
define('ACW_PUBLIC_DIR', str_replace("\\", '/', dirname(dirname(__DIR__))));//Chạy bên trong thư mục
//define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__)); //Chạy ngoài root
define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);

define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');

define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');

require ACW_USER_CONFIG_DIR . '/initialize.php';
require_once(ACW_APP_DIR. "/model/common/friendsrequest.php");
require_once(ACW_APP_DIR. "/model/common/tokenmanager.php");
require_once(ACW_APP_DIR. "/model/lib/curlpost.php");

set_time_limit(0);
define('LOG_SUCCESS', 'LOG_AUTO_ADD_FRIENDS');
define('LOG_FAIL', 'LOG_AUTO_ADD_FRIENDS_ERROR');

class batchaddfriends_model extends ACWModel {
    
    /**
     * 共通初期化
     */
    public static function init() {
        
    }
    
    public function checkStopBatch(){
		if(file_exists(BATH_LOCK_TXT) === FALSE) {
			ACWLog::debug_var(LOG_SUCCESS, "====STOP");
			return TRUE;
		}
		return FALSE;
	}
	
	public function GetListFriends(){
		$model_friends = new friendsrequest_common_model();
		$tryAgain = TRUE;
		$n = 0;
		$listProcess = NULL;
		do{
			if($this->checkStopBatch() == TRUE){
				return FALSE;
			}
			try{
				$listProcess = $model_friends->_getListProcess();
				if($listProcess != NULL && count($listProcess)>0){
					$tryAgain = FALSE;
				}
			} catch (Exception $e1) {
				$tryAgain = TRUE;
			}
			$n++;
		} while($tryAgain ==TRUE && $n <= TRY_AGAIN_GET_UID);
		
		return $listProcess;
	}
	
	public function GetListTokens(){
		$model_token = new tokenmanager_common_model();
		$tryAgain = TRUE;
		$n = 0;
		$tokens = NULL;
		do{
			try{
				$tokens = $model_token->_getActiveToken();
				if($tokens != NULL && count($tokens)>0){
					$tryAgain = FALSE;
				}
			} catch (Exception $e1) {
				$tryAgain = TRUE;
			}
			$n++;
		} while($tryAgain ==TRUE && $n <= TRY_AGAIN_GET_TOKEN);
		
		return $tokens;
	}

    public function main()
    {
		try {
			/*if (time() >= strtotime(TIME_STOP)) {
				ACWLog::debug_var(LOG_SUCCESS, "====STOP");
				goto end_batch;
			}*/
			
			if(file_exists(BATH_LOCK_TXT) === TRUE) {
				unlink(BATH_LOCK_TXT);
				if($this->checkStopBatch() == TRUE){
					goto end_batch;
				}
			}
			else{
				file_put_contents(BATH_LOCK_TXT, 'start');
				ACWLog::debug_var(LOG_SUCCESS, "====Start Batch");
			}
			
			$model_friends = new friendsrequest_common_model();
			$model_token = new tokenmanager_common_model();
			$curl = new curlpost_lib_model();
			do{
				//========================================
				//Lấy danh sách UID cần kết bạn
				$listProcess = $this->GetListFriends();
				if($listProcess === FALSE){
					goto end_batch;
				}
				
				if($listProcess != NULL && count($listProcess) > 0){
					foreach($listProcess as $friends){
						//Lấy danh sách token để gửi yêu cầu kết bạn
						$tokens = $this->GetListTokens();
						if($tokens != NULL && count($tokens)>0){
							foreach($tokens as $token){
								try{
									//Gửi yêu cầu kết bạn
									$res = $curl->setAddFriend($token['token2'],$friends['uid']);
									
									//ACWLog::debug_var('test', $token['full_name']."->".$friends['uid']);
									
									$friend_param['id'] = $friends['id'];
									if(isset($res['success']) == TRUE && $res['success'] == TRUE){
										//Kết bạn thành công
										$friend_param['status'] = 9;
										$model_friends->updateFriend($friend_param);
									}
									else{
										//Kết bạn thất bại
										$friend_param['status'] = 6;
										$model_friends->updateFriend($friend_param);
									}
								} catch (Exception $ex1) {
									//Kết bạn thất bại
									$friend_param['status'] = 6;
									$model_friends->updateFriend($friend_param);
								}
								//Tắt token sau 1 khoảng thời gian mới sử dụng lại được
								$model_token->_deactiveToken($token['id']);
							}
						}
						else{
							//Không có token khả dụng thì update UID về trạng thái chưa xử lý
							$friend_param['id'] = $friends['id'];
							$friend_param['status'] = 0;
							$model_friends->updateFriend($friend_param);
						}
						//test 1 lan chay
						/*if(file_exists(BATH_LOCK_TXT) === TRUE) {
							unlink(BATH_LOCK_TXT);
							if($this->checkStopBatch() == TRUE){
								goto end_batch;
							}
						}*/
						break;
					}
				}
				//========================================
				if($this->checkStopBatch() == TRUE){
					goto end_batch;
				}
			} while(TRUE);
			
			end_batch:
			ACWLog::debug_var(LOG_SUCCESS, "====■■■End Batch");
		}
		catch (Exception $e){
			if(file_exists(BATH_LOCK_TXT) === TRUE) {
				unlink(BATH_LOCK_TXT);
			}
			ACWLog::debug_var(LOG_FAIL, $e->getMessage());
		}
    }
}
echo "Start";
echo "<br>";
$batch = new batchaddfriends_model();
$batch->main();
echo "End";