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

set_time_limit(0);
define('LOG_SUCCESS', 'LOG_AUTO_ADD_FRIENDS');

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
			
			
			do{
				//========================================
				
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