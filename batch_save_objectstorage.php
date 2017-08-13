<?php

$_SERVER["REMOTE_ADDR"] = "batch in process...";
define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__));
//define('ACW_PUBLIC_DIR', str_replace("\\", '/', dirname(dirname(__DIR__))));

define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);
define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork'); // ルートディレクトリ
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');
//define('ACW_SERIES_DIR', ACW_ROOT_DIR . '/data/series');
/**
 * 一時ディレクトリ
 */
//define('AKAGANE_STRAGE_PATH', ACW_ROOT_DIR . '/data/');

define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');


// プロジェクトの初期化処理
require ACW_USER_CONFIG_DIR . '/initialize.php';
require_once ACW_APP_DIR . '/lib/Path.php';
require_once ACW_APP_DIR . '/lib/SeriesFile.php';
require_once ACW_APP_DIR . '/lib/YoyakuSeriesFile.php';
require_once ACW_APP_DIR . '/lib/ReadXml.php';
require_once ACW_APP_DIR . '/lib/SectionHead.php';
require_once ACW_APP_DIR . '/lib/SeriesXMLDom.php';
require_once ACW_APP_DIR . '/lib/SectionData.php';
require_once ACW_APP_DIR . '/lib/Excel.php';
require_once ACW_APP_DIR . '/lib/Xml.php';
require_once ACW_APP_DIR . '/lib/FileWindows.php';
require_once ACW_APP_DIR . '/lib/File.php';
require_once ACW_APP_DIR . '/lib/FilePHPDebug.php';
require_once ACW_APP_DIR . '/lib/Directory.php';
require_once ACW_APP_DIR . '/lib/ImportTags.php';
require_once ACW_APP_DIR . '/lib/ImportExport.php';
require_once ACW_APP_DIR . '/lib/Image.php';
require_once ACW_APP_DIR . '/model/Yoyaku.php';
require_once ACW_APP_DIR . '/model/common/Lock.php';
require_once ACW_APP_DIR . '/model/common/Kumihan.php';
require_once ACW_APP_DIR . '/model/common/Section.php';
require_once ACW_APP_DIR . '/model/common/Sequence.php';
require_once ACW_APP_DIR . '/model/Itemfile.php';
require_once ACW_APP_DIR . '/model/Itemcode.php';
require_once ACW_APP_DIR . '/model/excelreplace.php';
require_once ACW_APP_DIR . '/model/Series.php';
require_once ACW_APP_DIR . '/lib/ObjectStorage.php';
require_once ACW_APP_DIR . '/model/common/Container.php';
require_once ACW_APP_DIR . '/model/BackKumihan.php';


set_time_limit(0);

/**
* Khu vực config batch BP_LIXD-646
* ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
*/
define('TMP_PATH', ACW_ROOT_DIR . '/tmp/series');
define('OBST_PATH_BK', 'backup_important/tmp/series');//-> backup_important/tmp/series
define('LOG_SUCCESS', 'MOVE_TMP_SERIES_646');
define('LOG_FAIL', 'MOVE_TMP_SERIES_646_ERROR');
define('TIME_STOP', '08:30:00');//-> Need Check
//define('TIME_STOP', '23:30:00');//-> Need Check
define('PATH_LOCK_TXT', ACW_TMP_DIR.'/MOVE_TMP_SERIES_646_RUNNING.txt');//-> Need Check
define('EXECUTE_ROW', -1);//-> Chỉ chạy một số folder (Release thì set thành -1 = không giới hạn folder)
/**
* ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
*/

class Patchsync_model extends ACWModel {
	
	function sameDatePreviousMonth(DateTime $currentDate) {
	    $subMon = clone $currentDate;
	    $subMon->sub(new DateInterval("P1M"));

	    $previousMon = clone $currentDate;
	    $previousMon->modify("last day of previous month");

	    if ($subMon->format("n") == $previousMon->format("n")) {
	        $recurDay = $currentDate->format("j");
	        $daysInMon = $subMon->format("t");
	        $currentDay = $currentDate->format("j");
	        if ($recurDay > $currentDay && $recurDay <= $daysInMon) {
	            $subMon->setDate($subMon->format("Y"), $subMon->format("n"), $recurDay);
	        }
	        return $subMon->modify("-1 day");//Cố tình trừ thêm 1 ngày ở đây !!!
	    } else {
	        return $previousMon;
	    }
	}
	
	function GetDirectorySize($dir){
	    $bytestotal = 0;
	    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
			$bytestotal += is_file($each) ? filesize($each) : $this->GetDirectorySize($each);
		}
	    return $bytestotal;
	}
	
	function get_folder_list($date_process){
		$list_return = array();
		$lib_file = new FileWindows_lib();
		
		//COMMENT:Lấy toàn bộ folder trong tmp/series
		$folders = $lib_file->FolderList(TMP_PATH);
		
		if(count($folders) > 0){
			foreach ($folders as $folder) {	
				if($folder != ""){
					$path_folder = TMP_PATH."/".$folder;
				
					//COMMENT:Lấy toàn bộ file trong tmp/series/xxxx
					$files = $lib_file->FileList($path_folder);
					if(count($files) > 0){
						$path_file = $path_folder."/".$files[0];
						$date_modified = date("Y-m-d",filemtime($path_file));
						
						//COMMENT:Lọc chỉ lấy list những folder từ 1 tháng trước trở về sau
						if(strtotime($date_modified) <= strtotime($date_process)){
							$list_return[$folder] = $date_modified;
						}
					}
					else{
						//COMMENT: Folder trống nên xóa luôn cho rồi
						$lib_file->DeleteFolder($path_folder);
						ACWLog::debug_var(LOG_SUCCESS, "====Delete folder tmp empty: ".$path_folder);
					}
				}
			}
		}
		//COMMENT:Sort tăng dần theo ngày tháng năm (data cũ ở trên , mới ở dưới)
		asort($list_return);
		ACWLog::debug_var(LOG_SUCCESS, "====List Folder Move OBST:".count($list_return)."(items).");
		ACWLog::debug_var(LOG_SUCCESS, $list_return);
		
		return $list_return;
	}

	public function main() {
		ACWLog::debug_var(LOG_SUCCESS, "■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■");
		$time_point = microtime(true);
		$date_process = "";
		$total_size = 0;//bytes
		try {
			//COMMENT:Chỉ xử lý từ 4:00 AM (JP) - 8:30 AM (JP)
			if (time() >= strtotime(TIME_STOP)) {
				ACWLog::debug_var(LOG_SUCCESS, "====Execute timeout (4:00 AM (JP) - 8:30 AM (JP)).");
				goto end_batch;
			}
			
			$lib_file = new FileWindows_lib();
			$obst = new ObjectStorage_lib();
			
			//COMMENT:tạo file txt khi batch đang chạy, khi xóa file này thì batch sẽ stop (chạy xong thì sẽ xóa đi)
			$file_check_run = ACW_TMP_DIR.PATH_LOCK_TXT;
			if($lib_file->FileExists(PATH_LOCK_TXT) === TRUE) {
				$lib_file->DeleteFile(PATH_LOCK_TXT);
			}
			file_put_contents(PATH_LOCK_TXT, 'start');
			
			//COMMENT:khởi tạo kết nối với OBST
			$container = $obst->get_container_name(AKAGANE_CONTAINER_KEY_USER);
			if ($obst->set_used_container($container) == false) {
                ACWLog::debug_var(LOG_SUCCESS, "====ObjectStorage NOT connected.");
            }
            else{
				ACWLog::debug_var(LOG_SUCCESS, "====ObjectStorage connected.");
				ACWLog::debug_var(LOG_SUCCESS, "====Path ObjectStorage: ".$container."/".OBST_PATH_BK.".");
			}
			
			//COMMENT:Get datetime cần xử lý (lấy về trước đó 1 tháng)
			$createdDate = new DateTime();
			ACWLog::debug_var(LOG_SUCCESS, "====Current Date:".$createdDate->format("Y-m-d"));
			$prev = $this->sameDatePreviousMonth($createdDate);
			ACWLog::debug_var(LOG_SUCCESS, "====Previous Date (-1 Month):".$prev->format("Y-m-d"));
			$date_process = $prev->format("Y-m-d");//"2017-03-31"
			
			if($date_process != ""){
				
				//COMMENT:Lấy list folder cần move lên OBST
				$result = $this->get_folder_list($date_process);
				
				$exec = 0;
				if (count($result) > 0) {
					ACWLog::debug_var(LOG_SUCCESS, "====Start Process upload to ObjectStorage");
					foreach ($result as $folder => $date) {
						
						//COMMENT:Chỉ xử lý từ 4:00 AM (JP) - 8:30 AM (JP)
						if($lib_file->FileExists(PATH_LOCK_TXT) === FALSE) {
							ACWLog::debug_var(LOG_SUCCESS, "====Batch stopped manual.");
							break;
						}
						if (time() >= strtotime(TIME_STOP)) {
							ACWLog::debug_var(LOG_SUCCESS, "====Execute timeout (4:00 AM (JP) - 8:30 AM (JP)).");
							break;
						}
						
						if($folder != ""){
							$folder_local = TMP_PATH."/".$folder;
							if($lib_file->FolderExists($folder_local) === TRUE){
								$folder_size = $this->GetDirectorySize($folder_local);
								$folder_obst = OBST_PATH_BK . '/' . $folder;
								
								//COMMENT:Copy folder lên OBST
								$time_upload = microtime(true);
								if($obst->put_folder($folder_local, $folder_obst, AKAGANE_CONTAINER_KEY_USER)){
									$time_upload_end = microtime(true);
									$total_size = $total_size + $folder_size;
									ACWLog::debug_var(LOG_SUCCESS, "====Folder: ".$folder_local." uploaded to ObjectStorage.");
									
									//COMMENT:Xóa folder ở SAN
				                    if($lib_file->DeleteFolder($folder_local) == TRUE){
				                    	$time_exec_upload = $time_upload_end - $time_upload;
										ACWLog::debug_var(LOG_SUCCESS, "====Folder:".$folder_local." deleted at SAN.");
										
										//COMMENT:thông tin upload của 1 item.
										ACWLog::debug_var(LOG_SUCCESS, "====■Move Item Complete: ".$folder." Folder size: ". round($folder_size/1024/1024,2) . " MB - Time Upload+Delete folder: ".$time_exec_upload." seconds.");
									}
									else{
										ACWLog::debug_var(LOG_SUCCESS, "====Folder: ".$folder_local." CAN NOT delete at SAN.");
									}
				                    
				                }else{
				                    ACWLog::debug_var(LOG_SUCCESS, "====Folder: ".$folder_local." CANNOT uploaded to ObjectStorage.");
				                }
			                }
			                
			                //COMMENT:dùng để chạy 1 vài row test thử
			                if(EXECUTE_ROW != -1 && EXECUTE_ROW < $exec){
								break;
							}
						}
						$exec++;
					}
				}
				ACWLog::debug_var(LOG_SUCCESS, "====End Process upload to ObjectStorage");
				
				//COMMENT:tổng dung lượng đã xử lý
				ACWLog::debug_var(LOG_SUCCESS, "====■■■Move All Complete: ". round($total_size/1024/1024,2) . " MB.");
			}
			
			if($lib_file->FileExists(PATH_LOCK_TXT) === TRUE) {
				$lib_file->DeleteFile(PATH_LOCK_TXT);
			}
			
			end_batch:
			
			$time_point_end = microtime(true);
			$time_exec = $time_point_end - $time_point;
			
			//COMMENT:kết thúc xử lý
			ACWLog::debug_var(LOG_SUCCESS, "====■■■Time Execute: " . $time_exec." seconds");
			echo "<br>";
			print_r($time_exec);
		} catch (Exception $e) {
			ACWLog::debug_var(LOG_FAIL, $e);
		}
		ACWLog::debug_var(LOG_SUCCESS, "■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■");
	}

}


class BatchSaveObjectstorage extends Kumihan_common_model
{
	private $_obst = null;
	const LOG_NAME = 'SAVE_OBST';
	const LOG_NAME_DTP_C1 = 'SAVE_DTP_OBST_CONDITION_1';
	const LOG_NAME_DTP_C2 = 'SAVE_DTP_OBST_CONDITION_2';
	const LOG_NAME_DTP_C3 = 'SAVE_DTP_OBST_CONDITION_3';
	const UPD_USER_ID = 1;
	const TIME_STOP ="08:30:00";
	//const TIME_STOP ="23:30:00";
	
	public function main($init_flg)
	{
		ACWLog::debug_var(self::LOG_NAME, '■START■');
		
		$db = new BatchSaveObjectstorage();
		
		// 言語取得
		$lang_list = $db->get_lang_list();
		
		// ラッパ設定
		$db->_obst = new ObjectStorage_lib();
		
		if ($init_flg == true) {
			// 初期設定
			ACWLog::debug_var(self::LOG_NAME, '■初期設定■');
			$db->init_setting($lang_list);
		} else {
			// 移行実行
			ACWLog::debug_var(self::LOG_NAME, '■移行実行■');
			$db->begin_transaction();
			$db->exec($lang_list);
			$db->commit();
		}
		
		// エラー
		if (ACWError::count() > 0) {
			ACWLog::debug_var(self::LOG_NAME, '■エラー有■');
			foreach (ACWError::get_all() as $err) {
				ACWLog::debug_var(self::LOG_NAME, $err);
			}
		}
		
		ACWLog::debug_var(self::LOG_NAME, '■EXIT■');
	}
	
	#内部=======================================================================

	/**
	 * オブジェクトストレージ内の初期設定を行う
	 */
	private function init_setting($lang_list)
	{
		// コンテナ確認
		$err = false;
		$lang_container = array();
		
		foreach ($lang_list as $lang) {
			$lang_container[$lang['m_lang_id']] = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_SERIES, $lang['m_lang_id']);
			if ($this->_obst->set_used_container($lang_container[$lang['m_lang_id']]) == false) {
				ACWError::add('LANG_CONTAINER' . $lang['m_lang_id'], sprintf('%sコンテナが存在しません。 : TARGET = %s', $lang['lang_name'], $lang_container[$lang['m_lang_id']]));
				$err = true;
			}
		}
		$kumi_container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_KUMIHAN);
		if ($this->_obst->set_used_container($kumi_container) == false) {
			ACWError::add('KUMI_CONTAINER', sprintf('組版コンテナが存在しません。 : TARGET = %s', $kumi_container));
			$err = true;
		}
		$user_container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_USER);
		if ($this->_obst->set_used_container($user_container) == false) {
			ACWError::add('USER_CONTAINER', sprintf('ユーザコンテナが存在しません。 : TARGET = %s', $user_container));
			$err = true;
		}
		$web_container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_WEB);
		if ($this->_obst->set_used_container($web_container) == false) {
			ACWError::add('WEB_CONTAINER', sprintf('WEB連携コンテナが存在しません。 : TARGET = %s', $web_container));
			$err = true;
		}
		if ($err == true) {
			return;
		}
		
		// コンテナごとに処理を実行
		
		// 言語コンテナ
		foreach ($lang_list as $lang) {
			$this->_obst->set_used_container($lang_container[$lang['m_lang_id']]);

			// data
			$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(ltrim(AKAGANE_STRAGE_PATH, '/')));
			// data/series
			$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(AKAGANE_STRAGE_PATH . 'series'));
			// data/comment
			$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(str_replace('//', '/', AKAGANE_COMMENT_STRAGE_PATH)));			
		}
		
		// 組版コンテナ
		$this->_obst->set_used_container($kumi_container);
		$kumi_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		
		// DTP_IF/Typesetting
		$kumi_lib->combine('Typesetting');
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst($kumi_lib->get_full_path(), AKAGANE_DTPSERVER_IF_PATH));
		// DTP_IF/Typesetting/Approved
		$kumi_app_path = $kumi_lib->get_full_path('Approved');
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst($kumi_app_path, AKAGANE_DTPSERVER_IF_PATH));
		// DTP_IF/Typesetting/Temporary
		$kumi_tmp_path = $kumi_lib->get_full_path('Temporary');
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst($kumi_tmp_path, AKAGANE_DTPSERVER_IF_PATH));
		
		// ユーザコンテナ
		$this->_obst->set_used_container($user_container);
		$remove = ACW_ROOT_DIR . '/user';
		
		// USER/export
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(AKAGANE_EXPORT_USER_PATH, $remove));
		// USER/import
		$this->_obst->create_folder(AKAGANE_IMPORT_USER_PATH_OBJECTSTORAGE);
		// USER/download
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(AKAGANE_DOWNLOAD_INDD_PATH, $remove));
		// USER/upload
		$this->_obst->create_folder($this->_obst->conv_path_local_to_obst(AKAGANE_UPLOAD_INDD_USER_PATH, $remove));		
	}
	
	/**
	 * 移行本処理
	 */
	private function exec($lang_list)
	{
		$file_lib = new File_lib();
		$appeared = array();
		$dtp_container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_KUMIHAN);
		
		foreach ($lang_list as $lang) {
			$m_lang_id = $lang['m_lang_id'];
			$lang_kbn = $lang['lang_kbn'];
			ACWLog::debug_var(self::LOG_NAME, '■実行 : ' . $lang_kbn . '■');
			
			$stop_file = AKAGANE_WEB_PATH . '/' . $lang_kbn;
			
			// 承認停止用のファイルが既にある→WEB連携中
			if ($file_lib->FileExists($stop_file) == true) {
				ACWError::add('LANG' . $m_lang_id, sprintf('指定言語が WEB連携中 or 予約反映中 : TARGET = %s', $lang_kbn));
				continue;
			}
			
			// 承認停止用のファイルを作成
			touch($stop_file);
			
			// 対象取得
			$target_list = $this->get_target_list($m_lang_id);
			
			// 言語コンテナ名設定
			$lang_container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_SERIES, $m_lang_id);
			if ($this->_obst->set_used_container($lang_container) == false) {
				$file_lib->DeleteFile($stop_file);
				ACWError::add('LANG_CONTAINER' . $m_lang_id, sprintf('言語コンテナが存在しません。 : TARGET = %s', $lang_container));
				break;
			}
			
			
			foreach ($target_list as $target) {
				if (time() >= strtotime(self::TIME_STOP)) {
					break;
				}
				ACWLog::debug_var(self::LOG_NAME, $target);
				
				// コメントと組版の処理を版１つにつき一度にする
				if (isset($appeared[$target['t_series_ver_id']]) == true) {
					$app_flg = true;
				} else {
					$app_flg = false;
					$appeared[$target['t_series_ver_id']] = 1;
				}
				
				// 移行NGならば次へ
				if ((is_null($target['leave_t_series_ver_id']) == false) && ($target['leave_t_series_ver_id'] == $target['t_series_ver_id'])) {
					ACWLog::debug_var(self::LOG_NAME, sprintf('移行NG %s : %s.%s = %s', $target['series_id'], $target['major_ver'], $target['minor_ver'], $target['approval_status']));
					continue;
				}
				
				$result = array(
					'series'=>'',
					'comment'=>array()
				);
				$series_data_flg = TRUE;
				if($target['total_media'] != $target['tot_complete'] || $target['tot_complete'] != $target['tot_obst'])
				{
					$series_data_flg = FALSE;
					ACWLog::debug_var(self::LOG_NAME, sprintf('移行NG %s : %s.%s = %s', $target['series_id'], $target['major_ver'], $target['minor_ver'], $target['approval_status']).' - not save data');	
					continue;								
				}
				// シリーズ情報を移行
				if($series_data_flg){
					$this->_obst->set_used_container($lang_container);
					$ser_lib = new SeriesFile_lib($target['t_series_head_id'], $target['t_series_mei_id']);
					
					if ($file_lib->FolderExists($ser_lib->get_full_path()) == true) {
						$obj_sers_path = $this->_obst->conv_path_local_to_obst($ser_lib->get_full_path());
						$obj_head_path = str_replace('/mei_' . sprintf('%010d', $target['t_series_mei_id']), '', $obj_sers_path);

						// headフォルダを作成
						$this->create_folder($obj_head_path);
						// meiフォルダを削除
						$this->_obst->delete_folder($obj_sers_path);
						// 本体を移行
						if ($this->_obst->put_folder($ser_lib->get_full_path(), $obj_sers_path, AKAGANE_SERIES_XML_NAME) == false) {
							//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
							ACWError::add('SERINFO' . $target['t_series_mei_id'], sprintf('シリーズ情報フォルダ移行失敗 : TARGET = %s', $ser_lib->get_full_path()));
							continue;
						}
						
						$result['series'] = $ser_lib->get_full_path();
					} else {
						//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
						ACWError::add('SERINFO' . $target['t_series_mei_id'], sprintf('シリーズ情報フォルダが存在しません。 : TARGET = %s', $ser_lib->get_full_path()));
						continue;
					}
				}
				// コメント・組版を移行
				
				if ($app_flg == false) {
					// コメント
					if (is_null($target['com_t_series_ver_id']) == false && $series_data_flg== TRUE) {
						$com_lib = new Path_lib(str_replace('//', '/', AKAGANE_COMMENT_STRAGE_PATH));
						$com_list = $this->get_ver_comment_list($target['com_t_series_ver_id']);
						
						foreach ($com_list as $key => $com) {
							$com_local_path = $com_lib->get_full_path($com['t_comment_id']);
							$com_object_path = $this->_obst->conv_path_local_to_obst($com_local_path);
							// 削除→登録
							$this->_obst->delete_folder($com_object_path);
							if ($this->_obst->put_folder($com_local_path, $com_object_path) == false) {
								//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
								ACWError::add('SERCOMM' . $com['t_comment_id'], sprintf('コメントフォルダ移行失敗 : TARGET = %s', $com_local_path));
								continue 2;
							}
							
							$result['comment'][$key] = $com_local_path;
						}
					}
											
				}
				
				// 後処理（対象削除）
				if ($result['series'] != '') {
					// series.xmlを残す
					$del_list = $file_lib->FileFolderList($result['series']);
					$del_lib = new Path_lib($result['series']);
					foreach ($del_list as $del) {
						if (is_array($del) == true) {
							continue;
						}
						if (strcmp(AKAGANE_SERIES_XML_NAME, $del) == 0) {
							continue;
						}
						$del_path = $del_lib->get_full_path($del);
						if ($file_lib->FolderExists($del_path) === true) {
							$file_lib->DeleteFolder($del_path);
						} else {
							$file_lib->DeleteFile($del_path);
						}
					}
				}
				if (empty($result['comment']) == false) {
					foreach ($result['comment'] as $res) {
						if ($res != '') {
							$file_lib->DeleteFolder($res);
						}
					}
				}
				if($series_data_flg){
					$this->update_ver_obst_flg($target['t_series_ver_id']);	
				}
			
				ACWLog::debug_var(self::LOG_NAME, $result);
			}
			//get row dtp of series
			$dtp_media_rows = $this->get_target_dtp_list();
			foreach($dtp_media_rows as $target_dtp)
			{
				if (time() >= strtotime(self::TIME_STOP)) {
					break;
				}
				if(($target_dtp['obst_flg'] == 1 && $target_dtp['media_status'] == 1) || ($target_dtp['media_status'] == 2 && is_null($target_dtp['max_ver_id']) == true) || $target_dtp['t_media_head_id'] == 0)
				{
					if($target_dtp['obst_flg'] == 1 && $target_dtp['media_status'] == 1)
					{
						ACWLog::debug_var(self::LOG_NAME_DTP_C1, $target_dtp);
					}else if($target_dtp['media_status'] == 2 && is_null($target_dtp['max_ver_id']) == true){						
						ACWLog::debug_var(self::LOG_NAME_DTP_C2, $target_dtp);						
					}else if($target_dtp['t_media_head_id'] == 0){
						ACWLog::debug_var(self::LOG_NAME_DTP_C3, $target_dtp);
					}else{
						continue;
					}
							// 組版
					if (is_null($target_dtp['t_typeset_id']) == false) 
					{
						// コンテナ切替
						if ($this->_obst->set_used_container($dtp_container) == false) {
							$file_lib->DeleteFile($stop_file);
							ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版コンテナが存在しません。 : TARGET = %s', $dtp_container));
							continue 2;
						}
								
						// 組版結果の階層作成　Typesetting/Approved/"開発用2/4124/JPN"
						$path_lib = new Path_lib('');
						$exp = explode('/', trim($target_dtp['indd_file_path'], '/'));
						for ($i = 0; $i < count($exp); $i++) {
							$path_lib->combine($exp[$i]);
							$this->create_folder($path_lib->get_full_path());
						}

						// indd:フォルダ
						$ind_local_path = $this->put_kumihan_data($target_dtp['indd_file_path'], true);
						if ($ind_local_path == '') {
							//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
							ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版フォルダ移行失敗 : TARGET = %s', $target_dtp['indd_file_path']));							continue;
						}
						// zip:ファイル
						$zip_local_path = $this->put_kumihan_data($target_dtp['zip_file_path'], false);
						if ($zip_local_path == '') {
							//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
							ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版フォルダ移行失敗 : TARGET = %s', $target_dtp['zip_file_path']));
							continue;
						}
						// jpg:ファイル
						$jpg_local_path = $this->put_kumihan_data($target_dtp['jpg_file_path'], false);
						if ($jpg_local_path == '') {
							//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
							ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版フォルダ移行失敗 : TARGET = %s', $target_dtp['jpg_file_path']));
							continue;
						}
						
						// pdf:ファイル
						$pdf_local_path = $this->put_kumihan_data($target_dtp['pdf_file_path'], false);
						if ($pdf_local_path == '') {
							//$file_lib->DeleteFile($stop_file); // Remove - miyazaki Argo - NBKD-1033 - 2015/04/02
							ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版フォルダ移行失敗 : TARGET = %s', $target_dtp['pdf_file_path']));
							continue;
						}							
						$pdf_marked =str_replace(".pdf","_Marked.pdf", $target_dtp['pdf_file_path']);
						$pdf_marked_local_path = '';
						if($file_lib->FileExists(AKAGANE_DTPSERVER_IF_PATH.$pdf_marked)){
							$pdf_marked_local_path = $this->put_kumihan_data($pdf_marked, false);
							if ($pdf_marked_local_path == '') {									
								ACWError::add('KUMI' . $target_dtp['t_typeset_id'], sprintf('組版フォルダ移行失敗 : TARGET = %s', $pdf_marked));
								continue;
							}
						}
						$this->update_files_obst_flg($target_dtp['t_typeset_id']);
							// コンテナ切替
							//$this->_obst->set_used_container($lang_container);
						$result = array();
						$media_arr = 'media_'.$target_dtp['t_media_head_id'];
						
						if ($ind_local_path != '') {
							$file_lib->DeleteFolder($ind_local_path);
						}
						if ($zip_local_path != '') {
							$file_lib->DeleteFile($zip_local_path);
						}
						if ($jpg_local_path != '') {
							$file_lib->DeleteFile($jpg_local_path);
						}
						if ($pdf_local_path != '') {
							$file_lib->DeleteFile($pdf_local_path);
						}
						if ($pdf_marked_local_path != '') {
							$file_lib->DeleteFile($pdf_marked_local_path);
						}
						$arr_img = $this->move_image_all($target_dtp['jpg_file_path']);
						if(count($arr_img)>0){
							foreach($arr_img as $del_img){
								$file_lib->DeleteFile($del_img);
							}
							$result[$media_arr]['arr_img_page'] = $arr_img;
						}
						
						$result[$media_arr]['ind'] = $ind_local_path;
						$result[$media_arr]['zip'] = $zip_local_path;
						$result[$media_arr]['pdf'] = $jpg_local_path;
						$result[$media_arr]['pdf_marked'] = $pdf_marked_local_path;
						$result[$media_arr]['jpg'] = $pdf_local_path;
						
						ACWLog::debug_var(self::LOG_NAME, $result);
					}
				}							
			}
			$this->delete_indd_data();
			// 承認停止用のファイルを削除
			$file_lib->DeleteFile($stop_file);
		}
	}
	
	/**
	 * コンテナに存在しなければ指定のフォルダオブジェクトを作成
	 */
	private function create_folder($object_path)
	{
		if ($this->_obst->object_exists($object_path) == false) {
			$this->_obst->create_folder($object_path);
		}
	}
	
	/**
	 * 組版のデータ以降
	 */
	private function put_kumihan_data($target, $dir_flg)
	{
		$local_path = AKAGANE_DTPSERVER_IF_PATH . $target;
		$object_path = $this->_obst->conv_path_local_to_obst($local_path, AKAGANE_DTPSERVER_IF_PATH);
		if ($dir_flg == true) {
			$this->_obst->delete_folder($object_path);
			if ($this->_obst->put_folder($local_path, $object_path) == false) {
				return '';
			}
		} else {
			$this->_obst->delete_file($object_path);
			if ($this->_obst->put_file($local_path, $object_path) == false) {
				return '';
			}
		}
		return $local_path;
	}
	private function create_folder_bk($object_path)
	{
		$object_path = "dtp_series_media/".$object_path;
		if ($this->_obst->object_exists($object_path) == false) {
			$this->_obst->create_folder($object_path);			
		}
	}
	private function put_kumihan_data_bk($target, $dir_flg)
	{
		$local_path = AKAGANE_DTPSERVER_IF_PATH . $target;
		$object_path = $this->_obst->conv_path_local_to_obst($local_path, AKAGANE_DTPSERVER_IF_PATH);
		$object_path = "dtp_series_media/".$object_path;		
		if ($dir_flg == true) {
			$this->_obst->delete_folder($object_path);
			if ($this->_obst->put_folder($local_path, $object_path) == false) {
				return '';
			}
		} else {
			$this->_obst->delete_file($object_path);
			if ($this->_obst->put_file($local_path, $object_path) == false) {
				return '';
			}
		}
		return $local_path;
	}

	#DB=========================================================================

	/**
	 * 言語一覧
	 */
	private function get_lang_list()
	{
		$sql = "
			SELECT
				m_lang_id
			,	lang_name
			,	lang_kbn
			FROM
				m_lang
			WHERE
				del_flg = 0
				and m_lang_id = 1
			ORDER BY
				disp_seq
		";
		return $this->query($sql);
	}
	
	/**
	 * 対象取得
	 */
	private function get_target_list($m_lang_id)
	{
		// Edit start - miyazaki Argo - NBKD-1029 - 2015/04/13
		$sql = "
			SELECT
				VT.*
			FROM
				(SELECT
					shead.t_series_head_id
				,	shead.series_id
				,	shead.t_ctg_head_id
				,	ctg.ctg_id
				,	slang.t_series_lang_id
				,	sver.t_series_ver_id
				,	sver.major_ver
				,	sver.minor_ver
				,	sver.approval_status
				,	smei.t_series_mei_id
				,	vver.leave_t_series_ver_id
				,	vcom.t_series_ver_id AS com_t_series_ver_id
				,   COALESCE(t1.total_media,0) total_media
				,   COALESCE(t1.tot_complete,0) tot_complete
				,   COALESCE(t1.tot_obst,0) tot_obst
				FROM
					t_series_head shead
				JOIN
					t_ctg_head ctg
					ON	shead.t_ctg_head_id = ctg.t_ctg_head_id
					AND	ctg.del_flg = 0
				JOIN
					t_series_lang slang
					ON	shead.t_series_head_id = slang.t_series_head_id
					AND	slang.del_flg = 0
					AND	slang.m_lang_id = :m_lang_id -- 言語
				JOIN
					t_series_ver sver
					ON	slang.t_series_lang_id = sver.t_series_lang_id
					AND	sver.del_flg = 0
					AND	sver.object_storage_flg = 0 -- サーバのみ対象
				JOIN
					t_series_mei smei
					ON	sver.t_series_ver_id = smei.t_series_ver_id
					AND	smei.del_flg = 0
				LEFT JOIN
					(SELECT
						sver2.t_series_lang_id
					,	MAX(sver2.t_series_ver_id) AS leave_t_series_ver_id
					FROM
						t_series_ver sver2
					WHERE
						sver2.del_flg = 0
					AND	sver2.approval_status IN (:app_comp, :ref_yoyaku) -- MK確認済 or 予約反映済
					AND	sver2.object_storage_flg = 0 -- サーバのみ対象
					GROUP BY
						sver2.t_series_lang_id
					UNION
					SELECT
						sver2.t_series_lang_id
					,	MAX(sver2.t_series_ver_id) AS leave_t_series_ver_id
					FROM
						t_series_ver sver2
					WHERE
						sver2.del_flg = 0
					AND	sver2.object_storage_flg = 0 -- サーバのみ対象
					GROUP BY
						sver2.t_series_lang_id
					) vver
					ON	sver.t_series_lang_id = vver.t_series_lang_id
					AND	sver.t_series_ver_id = vver.leave_t_series_ver_id
				LEFT JOIN
					(SELECT
						com2.t_series_ver_id
					FROM
						t_comment com2
					GROUP BY
						com2.t_series_ver_id
					) vcom
					ON	sver.t_series_ver_id = vcom.t_series_ver_id
				LEFT JOIN (
						select k.t_series_lang_id,k.t_series_head_id,k.t_series_ver_id,
						count(k.t_media_head_id) total_media,
						sum(k.media_status)  tot_complete,
						sum(k.obst_flg) tot_obst
						from (
						SELECT distinct t.t_series_lang_id,t.t_series_head_id,t.t_series_ver_id,m.t_media_head_id,m.media_status,m.obst_flg
						FROM t_media_series ms 
						inner join t_media_head m on m.t_media_head_id = ms.t_media_head_id and m.t_media_head_id <> 0
						inner join t_typeset_files t on t.t_series_lang_id = ms.series_lang_id and m.t_media_head_id = t.t_media_head_id
						) k 
						group by k.t_series_lang_id,k.t_series_head_id,k.t_series_ver_id					
				) t1 on t1.t_series_lang_id = slang.t_series_lang_id and t1.t_series_head_id =shead.t_series_head_id
					and t1.t_series_ver_id = sver.t_series_ver_id
				WHERE
					shead.del_flg = 0
				) VT
				where VT.leave_t_series_ver_id is null
				and VT.total_media = VT.tot_complete
				and VT.total_media = VT.tot_obst
			ORDER BY
				VT.t_series_head_id
			,	VT.t_series_lang_id
			,	VT.t_series_ver_id
			,	VT.t_series_mei_id
		";
		// Edit end - miyazaki Argo - NBKD-1029 - 2015/04/13
		$param = array(
			'm_lang_id'=>$m_lang_id,
			'app_comp'=>AKAGANE_APPROVAL_STATUS_KEY_COMP,
			'ref_yoyaku'=>AKAGANE_APPROVAL_STATUS_KEY_YOYAKU_COMP
		);
		
		$rows = $this->query($sql, $param);
		return $rows;
	}

	private function get_target_dtp_list()
	{
		$sql = "select * from 
		(SELECT distinct
				files.t_ctg_head_id
			,	files.t_series_head_id
			,	files.t_series_ver_id
			,	files.t_series_lang_id
			,	files.object_storage_flg
			,	files.t_typeset_id
			,	files.zip_file_path
			,	files.pdf_file_path
			,	files.jpg_file_path
			,	files.indd_file_path
			,	files.t_media_head_id
			,	mh.media_status
			,	mh.obst_flg
			,	max_dtp.max_ver_id
			FROM
				t_typeset_files files
			INNER JOIN t_media_head mh on mh.t_media_head_id = files.t_media_head_id 
			INNER JOIN t_media_series ms on ms.series_lang_id = files.t_series_lang_id and ms.t_media_head_id = files.t_media_head_id 
				
			
			LEFT JOIN
			(
				SELECT
					max(t_series_ver_id) max_ver_id
					, t_media_head_id
					, t_series_lang_id
				FROM
					t_typeset_files			
				GROUP BY
					t_series_lang_id, t_media_head_id,t_series_lang_id		
			) max_dtp
			ON
				max_dtp.max_ver_id = files.t_series_ver_id
				and max_dtp.t_media_head_id = files.t_media_head_id
				and max_dtp.t_series_lang_id = files.t_series_lang_id
			WHERE
				files.object_storage_flg = 0
			order by t_typeset_id

			) x
			where (x.media_status = 1  and x.obst_flg = 1)
			or (x.media_status = 2 and x.max_ver_id is null)
			
			union 
			select t.t_ctg_head_id
						,	t.t_series_head_id
						,	t.t_series_ver_id
						,	t.t_series_lang_id
						,	t.object_storage_flg
						,	t.t_typeset_id
						,	t.zip_file_path
						,	t.pdf_file_path
						,	t.jpg_file_path
						,	t.indd_file_path
						,	t.t_media_head_id
						,	0
						,	0
						,	null
					 from t_typeset_files t
			where exists(
				select 1
				from v_t_req_queue que
				where que.t_req_src_id = t.t_req_id 
				and corp_nm = :corp_nm
			)
			and t.object_storage_flg = 0
			and t_media_head_id = 0
		";
		
		$rows = $this->query($sql,array('corp_nm' =>SYSTEM_SERVER_NAME));
		return $rows;
	}
	/**
	 * 対象の版に登録されたコメントの一覧
	 */
	private function get_ver_comment_list($t_series_ver_id)
	{
		$sql = "
			SELECT
				com.t_comment_id
			FROM
				t_comment com
			WHERE
				com.t_series_ver_id = :t_series_ver_id
			ORDER BY
				com.t_comment_id ASC
			";
		$param = array('t_series_ver_id'=>$t_series_ver_id);
		return $this->query($sql, $param);
	}
	
	
	/**
	 * フラグ更新
	 */
	private function update_ver_obst_flg($t_series_ver_id)
	{
		$sql = "
			UPDATE
				t_series_ver
			SET
				object_storage_flg = 1
			,	upd_user_id = :m_user_id
			,	upd_datetime = NOW()
			WHERE
				t_series_ver_id = :t_series_ver_id
		";
		$param = array(
			'm_user_id'=>self::UPD_USER_ID,
			't_series_ver_id'=>$t_series_ver_id
		);
		$this->execute($sql, $param);
	}
		
	/**
	 * フラグ更新
	 */
	private function update_files_obst_flg($t_typeset_id)
	{
		$sql = "
			UPDATE
				t_typeset_files
			SET
				object_storage_flg = 1
			,	upd_user_id = :m_user_id
			,	upd_datetime = NOW()
			WHERE
				t_typeset_id = :t_typeset_id
		";
		$param = array(
			'm_user_id'=>self::UPD_USER_ID,
			't_typeset_id'=>$t_typeset_id
		);
		$this->execute($sql, $param);
	}
	
	private function get_list_indd_delete()
	{
		$sql="select * from t_typeset_files t
				where not exists(
					select 1
					from t_media_series ms
					where ms.series_lang_id = t.t_series_lang_id and ms.t_media_head_id = t.t_media_head_id 
				)
				order by t_typeset_id
				";
		return $this->query($sql);
	}
	public function delete_indd_data(){
		$list = $this->get_list_indd_delete();
		$dtp_model = new BatchSaveObjectstorage('DTP_SERVER');
		$file_lib = new File_lib();		
		foreach($list as $row){
			if (time() >= strtotime(self::TIME_STOP)) {
				break;
			}
			$queue = $this->get_v_req_queue_rows($row['t_ctg_head_id'], $row['t_series_head_id'], $row['t_series_lang_id'],$row['t_series_ver_id'],$row['major_ver'],$row['minor_ver'],$row['t_media_head_id']);	
			if(count($queue) > 0){
				foreach($queue as $q){
					$backup_object_storage_flg = 0; 
					if(isset($q['m_media_type_id'])){
						unset($q['m_media_type_id']);
					}
					$this->insert_backup_queue($q,  $backup_object_storage_flg);
					$dtp_model->delete_queue($q['t_req_id']);				
				}
			}
			
			$pa_del = ACWArray::filter($row, array('t_typeset_id',
				't_ctg_head_id'
			,	't_series_head_id'
			,	't_series_ver_id'
			,	't_series_lang_id'
			,	'major_ver'
			,	'minor_ver'
			,	'ver_no'
			,	'indd_file_path'
			,	'zip_file_path'
			,	'pdf_file_path'
			,	'jpg_file_path'
			,	'log_file_path'
			,	't_req_id'
			,	'object_storage_flg'
			,	'add_user_id'
			,	'add_datetime'
			,	'upd_user_id'
			,	'upd_datetime'
		    ,   't_media_head_id'
		    ,   'm_template_id' ));
			$this->insert_backup_files($pa_del,0);
			$this->delete_typeset_files_indd($row['t_typeset_id']);
			$result_delete= array();
			/*if ($this->delete_file($row['indd_file_path']) == false) {                    
                $result_delete['indd_file_path'] = $row['indd_file_path'].'[Not exists file]';
            }else{
				$result_delete['indd_file_path'] = $row['indd_file_path'];
			}

            if ($this->delete_file($row['pdf_file_path']) == false) {  
                $result_delete['pdf_file_path'] = $row['pdf_file_path'].'[Not exists file]';
            }else{
				$result_delete['pdf_file_path'] = $row['pdf_file_path'];
			}

            if ($this->delete_file($row['jpg_file_path']) == false) {                    
                $result_delete['jpg_file_path'] = $row['jpg_file_path'].'[Not exists file]';
            }else{
				$result_delete['jpg_file_path'] = $row['jpg_file_path'];
			}

            if ($this->delete_file($row['zip_file_path']) == false) {                            
                $result_delete['zip_file_path'] = $row['zip_file_path'].'[Not exists file]';
            }else{
				$result_delete['zip_file_path'] = $row['zip_file_path'];
			}      

            $pdf_marked = str_replace(".pdf","_Marked.pdf", $row['pdf_file_path']);
			if($this->delete_file($pdf_marked) == TRUE){
				$result_delete['pdf_marked'] = $pdf_marked;
			}*/
			$dtp_container_bk ="BK";
			if ($this->_obst->set_used_container($dtp_container_bk) == false) {
				$file_lib->DeleteFile($stop_file);
				ACWError::add('KUMI' . $row['t_typeset_id'], sprintf('組版コンテナが存在しません。 : TARGET = %s', $dtp_container_bk));
				continue 2;
			}
			// indd:フォルダ
			$path_lib = new Path_lib('');
			$exp = explode('/', trim($row['indd_file_path'], '/'));			
			for ($i = 0; $i < count($exp); $i++) {
				$path_lib->combine($exp[$i]);				
				$this->create_folder_bk($path_lib->get_full_path());
			}
						$ind_local_path = $this->put_kumihan_data_bk($row['indd_file_path'], true);
						if ($ind_local_path == '') {
							$result_delete[$row['t_typeset_id']]['indd_file_path'] = $row['indd_file_path'].'[Not exists file]';
						}else{
							$result_delete[$row['t_typeset_id']]['indd_file_path'] = $row['indd_file_path'];
						}
						// zip:ファイル
						$zip_local_path = $this->put_kumihan_data_bk($row['zip_file_path'], false);
						if ($zip_local_path == '') {
							$result_delete[$row['t_typeset_id']]['zip_file_path'] = $row['zip_file_path'].'[Not exists file]';
						}else{
							$result_delete[$row['t_typeset_id']]['zip_file_path'] = $row['zip_file_path'];
						}
						// jpg:ファイル
						$jpg_local_path = $this->put_kumihan_data_bk($row['jpg_file_path'], false);
						if ($jpg_local_path == '') {
							$result_delete[$row['t_typeset_id']]['jpg_file_path'] = $row['jpg_file_path'].'[Not exists file]';
						}else{
							$result_delete[$row['t_typeset_id']]['jpg_file_path'] = $row['jpg_file_path'];
						}
						// pdf:ファイル
						$pdf_local_path = $this->put_kumihan_data_bk($row['pdf_file_path'], false);
						if ($pdf_local_path == '') {
							$result_delete[$row['t_typeset_id']]['pdf_file_path'] = $row['pdf_file_path'].'[Not exists file]';
						}else{
							$result_delete[$row['t_typeset_id']]['pdf_file_path'] = $row['pdf_file_path'];
						}							
						$pdf_marked =str_replace(".pdf","_Marked.pdf", $row['pdf_file_path']);
						$pdf_marked_local_path = '';
						if($file_lib->FileExists(AKAGANE_DTPSERVER_IF_PATH.$pdf_marked)){
							$pdf_marked_local_path = $this->put_kumihan_data($pdf_marked, false);
							if ($pdf_marked_local_path == ''){																	
								$result_delete[$row['t_typeset_id']]['pdf_marked'] = $pdf_marked.'[Not exists file]';
							}else{
								$result_delete[$row['t_typeset_id']]['pdf_marked'] = $pdf_marked;
							}
						}		
								
						// コンテナ切替
						
						if ($ind_local_path != '') {
							$file_lib->DeleteFolder($ind_local_path);
						}
						if ($zip_local_path != '') {
							$file_lib->DeleteFile($zip_local_path);
						}
						if ($jpg_local_path != '') {
							$file_lib->DeleteFile($jpg_local_path);
						}
						if ($pdf_local_path != '') {
							$file_lib->DeleteFile($pdf_local_path);
						}
						if ($pdf_marked_local_path != '') {
							$file_lib->DeleteFile($pdf_marked_local_path);
						}
						$arr_img = $this->move_image_all_bk($row['jpg_file_path']);		
						if(count($arr_img) > 0){
							foreach($arr_img as $del_img){
								$file_lib->DeleteFile($del_img);
							}
							$result_delete[$row['t_typeset_id']]['arr_img_page'] = $arr_img;
						}
				
			ACWLog::debug_var(self::LOG_NAME, '---data indd delete---');
			ACWLog::debug_var(self::LOG_NAME, $result_delete);
				
		}
	}
	private function insert_backup_queue($q,  $backup_object_storage_flg)
	{
		$sql = "
			INSERT INTO t_backup_req_queue (
				backup_datetime
			,	backup_object_storage_flg
			,	t_req_id
			,	req_datetime
			,	corp_nm
			,	t_ctg_head_id
			,	t_series_head_id
			,	t_series_lang_id
			,	t_series_ver_id
			,	major_ver
			,	minor_ver
			,	ver_no
			,	exec_kbn
			,	template_path
			,	xml_path
			,	proc_status
			,	image_path
			,	table_path
			,	indd_output_path
			,	pdf_output_path
			,	jpg_output_path
			,	log_output_path
			,	add_user_id
			,	upd_user_id
			,	upd_datetime
            ,   template_name 
		    ,   t_media_head_id 
		    ,   m_template_id 
			) VALUES (
				now()
			,	:backup_object_storage_flg
			,	:t_req_id
			,	:req_datetime
			,	:corp_nm
			,	:t_ctg_head_id
			,	:t_series_head_id
			,	:t_series_lang_id
			,	:t_series_ver_id
			,	:major_ver
			,	:minor_ver
			,	:ver_no
			,	:exec_kbn
			,	:template_path
			,	:xml_path
			,	:proc_status
			,	:image_path
			,	:table_path
			,	:indd_output_path
			,	:pdf_output_path
			,	:jpg_output_path
			,	:log_output_path
			,	:add_user_id
			,	:upd_user_id
			,	:upd_datetime
            ,   :template_name 
		    ,   :t_media_head_id
		    ,   :m_template_id 
			);
		";
		
		$param = $q;		
		$param['backup_object_storage_flg'] = $backup_object_storage_flg;
		
		$this->execute($sql, $param);
	}
	
	/**
	 * 組版ファイルレコードバックアップ
	 * Add - miyazaki Argo - NBKD-1029 - 2015/04/08
	 */
	private function insert_backup_files($f,  $backup_object_storage_flg)
	{
		$sql = "
			INSERT INTO t_backup_typeset_files (
				backup_datetime
			,	backup_object_storage_flg
			,	t_typeset_id
			,	t_ctg_head_id
			,	t_series_head_id
			,	t_series_ver_id
			,	t_series_lang_id
			,	major_ver
			,	minor_ver
			,	ver_no
			,	indd_file_path
			,	zip_file_path
			,	pdf_file_path
			,	jpg_file_path
			,	log_file_path
			,	t_req_id
			,	object_storage_flg
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
		    ,   t_media_head_id
		    ,   m_template_id 
			) VALUES (
				now()
			,	:backup_object_storage_flg
			,	:t_typeset_id
			,	:t_ctg_head_id
			,	:t_series_head_id
			,	:t_series_ver_id
			,	:t_series_lang_id
			,	:major_ver
			,	:minor_ver
			,	:ver_no
			,	:indd_file_path
			,	:zip_file_path
			,	:pdf_file_path
			,	:jpg_file_path
			,	:log_file_path
			,	:t_req_id
			,	:object_storage_flg
			,	:add_user_id
			,	:add_datetime
			,	:upd_user_id
			,	:upd_datetime
		    ,   :t_media_head_id
		    ,   :m_template_id 
			);
		";
		
		$param = $f;		
		$param['backup_object_storage_flg'] = $backup_object_storage_flg;
		
		$this->execute($sql, $param);
	}
	private function delete_typeset_files_indd($t_typeset_id)
	{
		$sql = "
			DELETE FROM
				t_typeset_files
			WHERE
				t_typeset_id = :t_typeset_id
		";
		
		$param = array('t_typeset_id'=>$t_typeset_id);
		
		$this->execute($sql, $param);
	}
	private function delete_file($path)
    {
		// 空チェック
		if (strlen($path)==0) {
			return false;
		}

		// フルパスを取得
		$del_path = AKAGANE_DTPSERVER_IF_PATH . str_replace('\\', '/', $path);

        $file = new File_lib();
        
		// 削除を実行
        if ($file->FolderExists($del_path)) {
            // フォルダの存在確認
            if ($file->DeleteFolder($del_path)) {
                return true;
            }
        } else if ($file->FileExists($del_path)) {
            // ファイルの存在確認
            if ($file->DeleteFile($del_path)) {
                return true;
            }
        }

		return false;
	}
	protected function get_v_req_queue_rows($t_ctg_head_id, $t_series_head_id, $t_series_lang_id, $t_series_ver_id , $major_ver, $minor_ver , $media_head_id )
	{
	
		$sql  = "SELECT 
					t_req_id, req_datetime, corp_nm, t_ctg_head_id, t_series_head_id, 
					t_series_ver_id, t_series_lang_id, major_ver, minor_ver, exec_kbn, 
					template_path, xml_path, proc_status, image_path, table_path, 
					indd_output_path, pdf_output_path, jpg_output_path, log_output_path, 
					upd_datetime, ver_no, add_user_id, upd_user_id, template_name, 
					t_media_head_id, m_template_id
				FROM v_t_req_queue ";		
		$sql .= "WHERE 1 = 1
				  AND corp_nm = :corp_nm
				  AND t_ctg_head_id    = :t_ctg_head_id
				  AND t_series_head_id = :t_series_head_id
				  AND t_series_lang_id = :t_series_lang_id
				  AND t_series_ver_id  = :t_series_ver_id
				  AND major_ver   = :major_ver
				  AND minor_ver   = :minor_ver
				  AND COALESCE(t_media_head_id,-1) = :t_media_head_id
				  ORDER BY t_series_ver_id desc,t_req_id DESC
		";

		$search_param = array();
		$search_param['corp_nm'] = SYSTEM_SERVER_NAME;
		$search_param['t_ctg_head_id']    = $t_ctg_head_id;
		$search_param['t_series_head_id'] = $t_series_head_id;
		$search_param['t_series_lang_id'] = $t_series_lang_id;
		$search_param['t_series_ver_id'] = $t_series_ver_id;
		$search_param['major_ver'] = $major_ver;
		$search_param['minor_ver'] = $minor_ver;
		$search_param['t_media_head_id'] = $media_head_id; 
		
		if(strlen($media_head_id)==0){
			$search_param['t_media_head_id'] = -1;	
		} 

		$res = $this->query($sql, $search_param);
		return $res;
	}
	public function move_image_all($target){		
		$local_path = AKAGANE_DTPSERVER_IF_PATH . $target;		
		//return $local_path;
		$arr_resutl = array();
		
		$file = new File_lib();
		$image = $file->GetFileName($local_path);		
		$image_name = $file->GetBaseName($image);
		$path_folder = str_replace($image, '', $local_path);
		
		$object_path = $this->_obst->conv_path_local_to_obst($path_folder, AKAGANE_DTPSERVER_IF_PATH);
		
		$list_file = $file->FileList($path_folder,'jpg');		
		if(count($list_file) > 0){
			foreach($list_file as $item){
				$ext = $file->GetExtensionName($item);				
				if($ext == 'jpg' && strpos($item, $image_name) !== false){					
					if ($this->_obst->put_file($path_folder.$item, $object_path.$item) == TRUE) {
						$arr_resutl[] = $path_folder.$item;
					}
				}
			}
		}
		return $arr_resutl;
	}
	public function move_image_all_bk($target){		
		$local_path = AKAGANE_DTPSERVER_IF_PATH . $target;		
		//return $local_path;
		$arr_resutl = array();
		
		$file = new File_lib();
		$image = $file->GetFileName($local_path);		
		$image_name = $file->GetBaseName($image);
		$path_folder = str_replace($image, '', $local_path);
		
		$object_path ="dtp_series_media/". $this->_obst->conv_path_local_to_obst($path_folder, AKAGANE_DTPSERVER_IF_PATH);
		
		$list_file = $file->FileList($path_folder,'jpg');		
		if(count($list_file) > 0){
			foreach($list_file as $item){
				$ext = $file->GetExtensionName($item);				
				if($ext == 'jpg' && strpos($item, $image_name) !== false){					
					if ($this->_obst->put_file($path_folder.$item, $object_path.$item) == TRUE) {
						$arr_resutl[] = $path_folder.$item;
					}
				}
			}
		}
		return $arr_resutl;
	}
}

/**
* 
* Run batch BP_LIXD-646
* 
*/
echo "Start Batch BP_LIXD-646 ...";
$batch = new Patchsync_model();
$batch->main();
echo "<br>";
echo "End Batch BP_LIXD-646";

/**
* 
* Run batch batch_save_objectstorage (liên quan BP_LIXD-638)
* 
*/
echo "<br>";
echo "Start BatchSaveObjectstorage ...";
$param = array();
$init_flg = filter_input(INPUT_GET, 'init', FILTER_VALIDATE_BOOLEAN);
if (is_null($init_flg) == true) {
	$init_flg = false;
}

$model = new BatchSaveObjectstorage();
$model->main($init_flg);
echo "<br>";
echo "End BatchSaveObjectstorage";