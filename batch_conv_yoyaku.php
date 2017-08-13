<?php
/**
 * 基本ファイル
 *
 * フレームワーク開始処理です。必要な部分を最初に変更します
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/

define('ACW_PROJECT', 'lixil_dev');	// プロジェクト名

/**
* 公開ディレクトリ
*/
define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__));

// 1階層上をルートディレクトリに設定
//define('ACW_ROOT_DIR', str_replace("\\", '/', dirname(__DIR__)));
define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);

// 上記のACW_PROJECTのプロジェクト名はinitialize内でdefineを切り替えるために主に使います。
/**
* デフォルトディレクトリ定義
*/
define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');	// ルートディレクトリ
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');
/**
* 一時ディレクトリ
*/
define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');

// プロジェクトの初期化処理
require ACW_USER_CONFIG_DIR . '/initialize.php';

require_once ACW_APP_DIR . '/lib/Path.php';

require_once ACW_APP_DIR . '/lib/SeriesFile.php';
require_once ACW_APP_DIR . '/lib/YoyakuSeriesFile.php';

require_once ACW_APP_DIR . '/lib/FileWindows.php';
require_once ACW_APP_DIR . '/lib/File.php';

require_once ACW_APP_DIR . '/model/Yoyaku.php';

require_once ACW_APP_DIR . '/model/common/Lock.php';
require_once ACW_APP_DIR . '/model/common/Kumihan.php';
require_once ACW_APP_DIR . '/model/User.php'; //add LIXD-501 Phong VNIT-20160504 

set_time_limit(0);

class BatchConvYoyaku extends Kumihan_common_model
{
	private $_result;
	const LOG_FILE_NAME = 'BATCH_CONV_YOYAKU';
	const UPD_USER_ID = 1;
	
	public function main()
	{
		// 実行日
		$exe_date = date('Y/m/d');
		
		$file_lib = new File_lib();
		
		$y_model = new Yoyaku_model();

		// ログファイル作成
		$log_lib = new Path_lib(AkAGANE_YOYAKU_BATCH_LOG_PATH);
		if ($file_lib->FolderExists($log_lib->get_full_path()) === false) {
			$file_lib->CreateFolder($log_lib->get_full_path());
		}
		$log_lib->combine(date('Ymd_His') . '.log');

		// 品番登録用
		$item_col = $y_model->get_table_colmun(
			  array('table_name'=>'t_item_info')
			, array('t_item_info_id', 'add_user_id', 'add_datetime', 'upd_user_id', 'upd_datetime')
		);
		//add start LIXD-501 Phong VNIT-20160504 
		//$user_login = ACWSession::get('user_info');
		$usr = new User_model();
		$usr_data = $usr->get_user_row(self::UPD_USER_ID);
		if($usr_data['user_ver_auth'] !='1'){
			ACWLog::debug_var(self::LOG_FILE_NAME, 'User have not privilege up version');
			return ;
		}
		//add end LIXD-501 Phong VNIT-20160504
		$db = new BatchConvYoyaku();
		$db->begin_transaction();

		$m_user_id = self::UPD_USER_ID;
		
		$db->_result = array();
		
		ACWLog::debug_var(self::LOG_FILE_NAME, '■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■');
		
		// 言語
		$lang_list = $db->get_lang_list();
		foreach ($lang_list as $lang) {
			if (empty($db->_result) == false)  {
				$db->put_log(PHP_EOL, true);
				$db->put_log(PHP_EOL, true);
			}
			$db->put_log('■■■' . $lang['lang_name'] . '■■■');
			
			// 承認停止用のファイルが既にある→WEB連携中
			if ($file_lib->FileExists(AKAGANE_WEB_PATH . '/' . $lang['lang_kbn']) == true) {
				$db->put_log($lang['lang_name'] . 'はWEB連携中のため処理されませんでした。');
				continue;
			}
			
			// 承認停止用のファイルを作成
			touch(AKAGANE_WEB_PATH . '/' . $lang['lang_kbn']);
			
			// 予約シリーズ
			$param = array('exe_date'=>$exe_date, 'm_lang_id'=>$lang['m_lang_id']);
			$yoyaku_list = $db->get_conv_yoyaku_list($param);
			
			ACWLog::debug_var(self::LOG_FILE_NAME, $lang['lang_name']);
			
			$now_t_yoyaku_head_id = null;
			foreach ($yoyaku_list as $yoyaku) {
				if ((is_null($now_t_yoyaku_head_id) == true) || ($now_t_yoyaku_head_id != $yoyaku['t_yoyaku_head_id'])) {
					$now_t_yoyaku_head_id = $yoyaku['t_yoyaku_head_id'];
					$db->put_log(PHP_EOL, true);
					$db->put_log('予約情報 ： ' . $yoyaku['yoyaku_head_date']);
				}
				
				ACWLog::debug_var(self::LOG_FILE_NAME, $yoyaku);
				$db->put_log('●' . $yoyaku['series_id'] . ' 反映開始');

				// 更新先状況チェック
				if ($db->validate_conv($yoyaku) == false) {
					continue;
				}
				
				// シリーズ情報更新
				$yoyaku['m_user_id'] = $m_user_id;
				$new_id = $db->conv_series_info($yoyaku, $file_lib);
				
				// 品番更新
				$db->conv_item_info($yoyaku, $item_col);
				
				// 関連系更新
				//Remove Start NBKD-1107 MinhVnit 2015/05/28
				/*$param_ins = ACWArray::filter($yoyaku, array('t_yoyaku_head_id', 't_yoyaku_series_lang_id'));
				$param_ins['user_id_1'] = $m_user_id;
				$param_ins['user_id_2'] = $m_user_id;
				$db->conv_relation($param_ins);
				$db->conv_relation_info($param_ins);
				$db->conv_kyoyu($param_ins);
				$db->conv_syuyaku($param_ins);*/
				//Remove End NBKD-1107 MinhVnit 2015/05/28
				
				// 後始末
				$db->update_comp_series($yoyaku, $new_id['t_series_ver_id']);
				$db->delete_yoyaku_ctg($yoyaku['t_yoyaku_ctg_id'], $yoyaku['t_yoyaku_head_id']);
				$db->delete_kumihan_save_data($yoyaku['t_typeset_id']);
				
				$db->put_log('反映完了');
			}
			
			ACWLog::debug_var(self::LOG_FILE_NAME, '■' . $lang['lang_name'] . 'の処理結果■');
			$db->put_log(PHP_EOL, true);
			$db->put_log('■予約反映状況確認■');
			
			// 完了フラグ更新
			$ser_cmp_list = $this->get_ser_cmp_count($param);
			foreach ($ser_cmp_list as $cmp) {
				ACWLog::debug_var(self::LOG_FILE_NAME, $cmp);
				$db->put_log(PHP_EOL, true);
				$db->put_log('予約情報 ： ' . $cmp['yoyaku_head_date']);
				$db->put_log('商品数   ： ' . $cmp['ser_count']);
				$db->put_log('完了数   ： ' . $cmp['ser_cmp_count']);
				
				if ($cmp['ser_count'] == $cmp['ser_cmp_count']) {
					// シリーズ数と完了数が同じならば、ヘッダも完了にする
					$cmp['m_user_id'] = $m_user_id;
					$db->update_comp_head($cmp);
				}
			}
			
			// 承認停止用のファイルを削除
			$file_lib->DeleteFile(AKAGANE_WEB_PATH . '/' . $lang['lang_kbn']);
		}

		$db->commit();
		
		$fp = fopen($log_lib->get_full_path(), "w");
		foreach ($db->_result as $res) {
			fputs($fp, $res);
		}
		fclose($fp);
	}
	
	private function put_log($msg, $date_off = false)
	{
		if ($date_off == true) {
			$this->_result[] = $msg;
		} else {
			$this->_result[] = date('Y/m/d H:i:s') . "\t" . $msg . "\n";
		}
	}


	/**
	 * 更新前のチェック
	 */
	private function validate_conv(&$param)
	{
		// 最新版が承認済みでないなら処理しない
		if ($param['approval_status'] != AKAGANE_APPROVAL_STATUS_KEY_COMP) {
			$this->put_log('最新版が' . AKAGANE_APPROVAL_STATUS_NAME_COMP . 'でないため反映できません');
			return false;
		}
		
		// 反映先が組版中なら処理をしない
		$dtpmodel = new Kumihan_common_model('DTP_SERVER');
		$t_req_queue = $dtpmodel->get_req_queue_rows(
			  $param['t_ctg_head_id']
			, $param['t_series_head_id']
			, $param['t_series_lang_id']
			, $param['t_series_ver_id']
			, $param['major_ver']
			, $param['minor_ver']
		);

		if (empty($t_req_queue) == false) {
			if (strcmp($t_req_queue[0]['proc_status'], self::PROC_STATUS_END) != 0) {
				$this->put_log('他のユーザによって組版処理が実行されているため反映できません');
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * シリーズ情報の反映
	 */
	private function conv_series_info($info, $file_lib)
	{
		// シリーズ反映・追加
		$new_id = $this->update_series_info($info);
		ACWLog::debug_var(self::LOG_FILE_NAME, $new_id);
		
		// ファイルコピー
		$ser_lib = new SeriesFile_lib($info['t_series_head_id'], $new_id['t_series_mei_id']);
		$yoyaku_lib = new YoyakuSeriesFile_lib($info['t_yoyaku_series_lang_id'], $info['t_yoyaku_series_mei_id']);
		
		if ($file_lib->FolderExists($ser_lib->get_full_path()) == false) {
			$file_lib->CreateFolder($ser_lib->get_full_path());
		}
		
		$file_lib->CopyFolder($yoyaku_lib->get_full_path(), $ser_lib->get_full_path());

		// 予約コメント反映
		$this->conv_comment($info, $new_id, $file_lib);
		
		// 予約組版反映
		$this->conv_kumihan($info, $new_id, $file_lib);
		
		return $new_id;
	}
	
	/**
	 * 予約コメント反映
	 */
	private function conv_comment($info, $new_id, $file_lib)
	{
		// コメント反映
		$com_list = $this->get_comment_list($info);
		foreach ($com_list as $com) {
			$com['t_series_ver_id'] = $new_id['t_series_ver_id'];
			$com['user_id_1'] = $info['m_user_id'];
			$com['user_id_2'] = $info['m_user_id'];
			
			$src_com_id = $com['t_yoyaku_comment_id'];
			unset($com['t_yoyaku_comment_id']);
			
			$dst_com_id = $this->insert_comment($com);
			
			// 添付ファイルの移動準備
			$src_lib = new Path_lib(AKAGANE_YOYAKU_COMMENT_STRAGE_PATH);
			$src_lib->combine($src_com_id);
			$dst_lib = new Path_lib(AKAGANE_COMMENT_STRAGE_PATH);
			$dst_lib->combine($dst_com_id);
			
			// 添付ファイルの移動
			if ($file_lib->FolderExists($src_lib->get_full_path()) === true) {
				if ($file_lib->FolderExists($dst_lib->get_full_path() === false)) {
					$file_lib->CreateFolder($dst_lib->get_full_path());
				}
				$file_lib->CopyFolder($src_lib->get_full_path(), $dst_lib->get_full_path());
			}
		}
	}
	
	/**
	 * 品番情報の反映
	 */
	private function conv_item_info($info, $item_col)
	{
		$param = ACWArray::filter($info, array('m_lang_id', 'series_id'));
		$param['user_id_1'] = $info['m_user_id'];
		$param['user_id_2'] = $info['m_user_id'];
		$this->delete_item_info($param);
		$this->insert_item_info($param, $item_col);
	}
	
	/**
	 * 組版情報
	 */
	private function conv_kumihan($info, $new_id, $file_lib)
	{
		// 最新の完了のキュー情報を取得
		$dtpmodel = new BatchConvYoyaku('DTP_SERVER');
		$t_req_queue = $dtpmodel->get_req_queue_rows(
			  $info['t_yoyaku_ctg_id']
			, $info['t_yoyaku_series_lang_id']
			, $info['t_yoyaku_series_lang_id']
			, ''
			, ''
			, ''
			, self::PROC_STATUS_END
		);
		
		ACWLog::debug_var(self::LOG_FILE_NAME, $t_req_queue);
		if (empty($t_req_queue) == true) {
			return;
		}
		
		// Add Start LIXD-18 hungtn VNIT 20150827
		foreach ($t_req_queue as $item) {
		    if(!empty($item)) {
		        $tmp = array();
		        $tmp[] = $item;
		        $this->conv_kumihan_child($tmp, $info, $new_id, $file_lib);
		    }
		}
		// Add End LIXD-18 hungtn VNIT 20150827
	}
	// Add Start LIXD-18 hungtn VNIT 20150827
	private function conv_kumihan_child($t_req_queue, $info, $new_id, $file_lib) {
	    $dtpmodel = new BatchConvYoyaku('DTP_SERVER');
	    // 共通パラメータ
	    $param = ACWArray::filter($info, array('t_series_head_id', 't_series_lang_id', 't_ctg_head_id', 'm_user_id'));
	    $y_ver = $this->get_series_ver_row($new_id['t_series_ver_id']);
	    // Add start - MinhVnit - LIXD-18 - 2015/08/20
	    $m_media_type_id = $t_req_queue[0]['m_media_type_id'];
	    $m_template_id = $t_req_queue[0]['m_template_id'];
	    $template_name = $t_req_queue[0]['template_name'];
	    // Add end - MinhVnit - LIXD-18 - 2015/08/20
	    $param['t_series_ver_id'] = $new_id['t_series_ver_id'];
	    $param['major_ver'] = $y_ver['major_ver'];
	    $param['minor_ver'] = $y_ver['minor_ver'];
	    
	    // Add start - miyazaki Argo - NBKD-1081 - 2015/04/03
	    // キューを新規で登録
	    $param_q_new = $param;
	    $param_q_new['req_datetime'] = $t_req_queue[0]['req_datetime'];
	    $param_q_new['exec_kbn'] = $t_req_queue[0]['exec_kbn'];
	    $param_q_new['proc_status'] = $t_req_queue[0]['proc_status'];
	    // Add start - MinhVnit - LIXD-18 - 2015/08/20
	    $param_q_new['m_media_type_id'] = $m_media_type_id;
	    $param_q_new['m_template_id'] = $m_template_id;
	    $param_q_new['template_name'] = $template_name;
	    // Add end - MinhVnit - LIXD-18 - 2015/08/20
	    $new_req_id = $dtpmodel->insert_conv_queue($param_q_new);
	    // Add end - miyazaki Argo - NBKD-1081 - 2015/04/03
	    
	    // キューテーブルを反映
	    // Edit start - miyazaki Argo - NBKD-1081 - 2015/04/03
	    //$param_q = $param;
	    //$param_q['t_req_id'] = $t_req_queue[0]['t_req_id'];
	    $param_q = array();
	    $param_q['t_req_id'] = $new_req_id;
	    // Edit end - miyazaki Argo - NBKD-1081 - 2015/04/03
	    $param_q['indd_output_path'] = $this->copy_kumihan_file($t_req_queue[0]['indd_output_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '\\');
	    $param_q['pdf_output_path'] = $this->copy_kumihan_file($t_req_queue[0]['pdf_output_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '\\');
	    $param_q['jpg_output_path'] = $this->copy_kumihan_file($t_req_queue[0]['jpg_output_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '\\');
	    $param_q['template_path'] = $this->copy_kumihan_tmp_file($t_req_queue[0]['template_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver']);
	    $this->copy_kumihan_ex_file(
	        sprintf('tmp\\%d\\basexml\\', $t_req_queue[0]['t_req_id'])
	        , $file_lib
	        , $t_req_queue[0]['t_series_head_id']
	        , $t_req_queue[0]['major_ver']
	        , $t_req_queue[0]['minor_ver']
	        , $info['t_series_head_id']
	        , $y_ver['major_ver']
	        , $y_ver['minor_ver']
	    );
	    // Add start - miyazaki Argo - NBKD-1081 - 2015/04/03
	    $param_q['log_output_path'] = $this->move_kumihan_log($t_req_queue[0]['log_output_path'], $new_req_id, $file_lib, '\\');
	    $param_q['template_path'] = $this->rename_kumihan_tmp_path($param_q['template_path'], $new_req_id);
	    $param_q['xml_path'] = $this->rename_kumihan_tmp_path($t_req_queue[0]['xml_path'], $new_req_id);
	    $param_q['image_path'] = $this->rename_kumihan_tmp_path($t_req_queue[0]['image_path'], $new_req_id);
	    $param_q['table_path'] = $this->rename_kumihan_tmp_path($t_req_queue[0]['table_path'], $new_req_id);
	    
	    $tmp_lib = new Path_lib(AKAGANE_DTPSERVER_TMP_PATH);
	    $tmp_src_path = $tmp_lib->get_full_path($t_req_queue[0]['t_req_id']);
	    $tmp_dst_path = $tmp_lib->get_full_path($new_req_id);
	    if ($file_lib->FolderExists($tmp_src_path) === true) {
	        $file_lib->MoveFolder($tmp_src_path, $tmp_dst_path);
	    }
	    // Add end - miyazaki Argo - NBKD-1081 - 2015/04/03
	    
	    $dtpmodel->update_conv_queue($param_q);
	    
	    // Add start - miyazaki Argo - NBKD-1081 - 2015/04/03
	    $dtpmodel->delete_conv_queue(array('t_req_id'=>$t_req_queue[0]['t_req_id']));
	    // Add end - miyazaki Argo - NBKD-1081 - 2015/04/03
	    
	    // 組版テーブルを反映
	    $typeset_files = $this->select_t_typeset_files(
	        $info['t_yoyaku_ctg_id']
	        , $info['t_yoyaku_series_lang_id']
	        , $info['t_yoyaku_series_lang_id']
	    );
	    
	    ACWLog::debug_var(self::LOG_FILE_NAME, $typeset_files);
	    if (empty($typeset_files) == true) {
	        return;
	    }
	    
	    $param_f = $param;
	    $param_f['t_typeset_id'] = $typeset_files[0]['t_typeset_id'];
	    $param_f['indd_file_path'] = $this->copy_kumihan_file($typeset_files[0]['indd_file_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '/');
	    $param_f['zip_file_path'] = $this->copy_kumihan_file($typeset_files[0]['zip_file_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '/');
	    $param_f['pdf_file_path'] = $this->copy_kumihan_file($typeset_files[0]['pdf_file_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '/');
	    $param_f['jpg_file_path'] = $this->copy_kumihan_file($typeset_files[0]['jpg_file_path'], $info['t_series_head_id'], $file_lib, $y_ver['major_ver'], $y_ver['minor_ver'], '/');
	    
	    // Edit start - miyazaki Argo - NBKD-1081 - 2015/04/03
	    $param_f['log_file_path'] = $this->move_kumihan_log($typeset_files[0]['log_file_path'], $new_req_id, $file_lib, '/');
	    $param_f['t_req_id'] = $new_req_id;
	    // Add start - MinhVnit - LIXD-18 - 2015/08/20
	    $param_f['m_media_type_id'] = $m_media_type_id;
	    $param_f['m_template_id'] = $m_template_id;
	    // Add end - MinhVnit - LIXD-18 - 2015/08/20
	    unset($param_f['t_typeset_id']);
	    
	    $this->insert_conv_typeset_files($param_f);
	    $this->delete_conv_typeset_files(array('t_typeset_id'=>$typeset_files[0]['t_typeset_id']));
	    //$this->update_conv_typeset_files($param_f);
	    // Edit end - miyazaki Argo - NBKD-1081 - 2015/04/03
	}
	// Add End LIXD-18 hungtn VNIT 20150827
	/*
	 * 組版一時ファイルコピー
	 */
	private function copy_kumihan_tmp_file($file_path, $new_id, $file_lib, $new_major_ver, $new_minor_ver)
	{
		if ((is_null($file_path) == true) || ($file_path == '')) {
			return null;
		}
		
		$src_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		$src_lib->combine($file_path);
		
		// パスを分解
		$src_array = explode('\\', $file_path);
		
		// 反映後のファイル名
		$src_array[4] = sprintf(
			  '%s_%d.%d.%s'
			, $new_id
			, $new_major_ver
			, $new_minor_ver
			, $file_lib->GetExtensionName($src_lib->get_full_path())
		);
		
		// 文字列に戻す
		$dst_path = implode('\\', $src_array);
		
		if ($file_lib->FileExists($src_lib->get_full_path()) === true) {
			// ファイルコピー
			$dst_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			$dst_lib->combine($dst_path);
			
			$file_lib->CopyFile($src_lib->get_full_path(), $dst_lib->get_full_path());
		}
		
		return $dst_path;
	}
	
	/*
	 * 組版一時ファイルコピー（その他）
	 */
	private function copy_kumihan_ex_file($path, $file_lib, $old_id, $old_major_ver, $old_minor_ver, $new_id, $new_major_ver, $new_minor_ver)
	{
		// 探すフォルダ
		$src_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		$src_lib->combine($path);
		
		// 検索する予約のIDを作成
		$old_id_str = sprintf('%s_%d.%d', $old_id, $old_major_ver, $old_minor_ver);
		
		// 置き換える現在のIDを作成
		$new_id_str = sprintf('%s_%d.%d', $new_id, $new_major_ver, $new_minor_ver);
		
		$file_list = $file_lib->FileList($src_lib->get_full_path());
		
		foreach ($file_list as $file) {
			// ファイル名から拡張子を取り除く
			$base_name = str_replace('.'.$file_lib->GetExtensionName($src_lib->get_full_path($file)), '', $file);

			// 検索する予約のIDのファイルがあれば新しいものに置き換える
			if (strcmp($base_name, $old_id_str) == 0) {
				$dst_name = str_replace($old_id_str, $new_id_str, $file);
				$file_lib->CopyFile($src_lib->get_full_path($file), $src_lib->get_full_path($dst_name));
				$file_lib->DeleteFile($src_lib->get_full_path($file));
			}
		}
	}
	
	/**
	 * キューのカラム編集
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function rename_kumihan_tmp_path($path, $new_id)
	{
		if ((is_null($path) == true) || ($path == '')) {
			return null;
		}
		
		// パスを分解
		$src_array = explode('\\', $path);
		// 反映後のフォルダ名
		$src_array[2] = $new_id;
		// 文字列に戻す
		$dst_path = implode('\\', $src_array);
		
		return $dst_path;
	}

	/*
	 * 組版ファイルコピー
	 */
	private function copy_kumihan_file($file_path, $new_id, $file_lib, $new_major_ver, $new_minor_ver, $rep_str)
	{
		if ((is_null($file_path) == true) || ($file_path == '')) {
			return null;
		}
		
		$src_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		$src_lib->combine($file_path);

		// パスを分解
		$src_array = explode($rep_str, $file_path);
		$old_name = $src_array[7];
		
		//  \Typesetting\Temporary\開発4\3915\JPN\MEDIA_TYPE_ID\3915_0.0.indd
		// 0 1           2         3     4    5         6            7
		$src_array[4] = $new_id;
		// フォルダ確認
		$dir_path = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		$dir_path->combine($src_array[1]);
		$dir_path->combine($src_array[2]);
		$dir_path->combine($src_array[3]);
		$dir_path->combine($src_array[4]);
		// ヘッダIDのフォルダ
		if ($file_lib->FolderExists($dir_path->get_full_path()) === false) {
			$file_lib->CreateFolder($dir_path->get_full_path());
		}
		// 言語のフォルダ
		$dir_path->combine($src_array[5]);
		if ($file_lib->FolderExists($dir_path->get_full_path()) === false) {
			$file_lib->CreateFolder($dir_path->get_full_path());
		}
		
		// Add start - MinhVnit - LIXD-18 - 2015/08/20
		$dir_path->combine($src_array[6]);
		if ($file_lib->FolderExists($dir_path->get_full_path()) === false) {
			$file_lib->CreateFolder($dir_path->get_full_path());
		}
		// Add end - MinhVnit - LIXD-18 - 2015/08/20
		$src_array[7] = sprintf(
			  '%s_%d.%d.%s'
			, $new_id
			, $new_major_ver
			, $new_minor_ver
			, $file_lib->GetExtensionName($src_lib->get_full_path())
		);
		
		// 文字列に戻す
		$dst_path = implode($rep_str, $src_array);
		
		if ($file_lib->FileExists($src_lib->get_full_path()) === true) {
			// ファイルコピー
			$dst_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			$dst_lib->combine($dst_path);
			
			$file_lib->CopyFile($src_lib->get_full_path(), $dst_lib->get_full_path());
		} else if ($file_lib->FolderExists($src_lib->get_full_path()) === true) {
			// フォルダコピー
			$dst_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			$dst_lib->combine($dst_path);
			
			$file_lib->CopyFolder($src_lib->get_full_path(), $dst_lib->get_full_path());
			
			// フォルダの中身もコピー
			$file_list = $file_lib->FileList($dst_lib->get_full_path());
			if (count($file_list) > 0) {
				foreach ($file_list as $file) {
					if (strcmp($old_name, $file) == 0) {
						// フォルダ名と同じファイルがあればリネーム
						$file_lib->CopyFile($dst_lib->get_full_path($file), $dst_lib->get_full_path($src_array[7]));
						$file_lib->DeleteFile($dst_lib->get_full_path($file));
						break;
					}
				}
			}
		}

		return $dst_path;
	}
	
	/**
	 * 組版ログファイルの移動
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function move_kumihan_log($old_log_path, $new_req_id, $file_lib, $rep_str)
	{	
		if ((is_null($old_log_path) == true) || ($old_log_path == '')) {
			return null;
		}
		
		// 元のファイル
		$src_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
		$src_lib->combine($old_log_path);
		$src_path = $src_lib->get_full_path();
		
		// パスを分解
		// \Typesetting\Log\1071.txt
		$src_array = explode($rep_str, $old_log_path);
		$src_array[3] = sprintf('%s.%s', $new_req_id, $file_lib->GetExtensionName($src_path));
		// 文字列に戻す
		$dst_path = implode($rep_str, $src_array);
		
		if ($file_lib->FileExists($src_path) === true) {
			$dst_lib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			$dst_lib->combine($dst_path);
			$file_lib->MoveFile($src_path, $dst_lib->get_full_path());
		}
		
		return $dst_path;
	}
	
	/**
	 * 予約反映後、不要なカテゴリを削除
	 */
	private function delete_yoyaku_ctg($t_yoyaku_ctg_id , $t_yoyaku_head_id)
	{
		// カテゴリに紐つくシリーズを探す
		$rows = $this->get_yoyaku_ctg_child($t_yoyaku_ctg_id, $t_yoyaku_head_id);
		
		// 紐つくカテゴリ,シリーズがおらず、予約ヘッダのカテゴリでない場合のみ削除する
		if (($rows[0]['oya_t_yoyaku_ctg_id'] != -1) && ($rows[0]['ser_cnt'] == 0) && ($rows[0]['ctg_cnt'] == 0)) {
			$sql_del = "
				DELETE FROM
					t_yoyaku_ctg 
				WHERE
					t_yoyaku_ctg_id = :t_yoyaku_ctg_id
			";
			
			$this->execute($sql_del, array('t_yoyaku_ctg_id'=>$t_yoyaku_ctg_id));
			
			// 親を探す
			$this->delete_yoyaku_ctg($rows[0]['oya_t_yoyaku_ctg_id'], $t_yoyaku_head_id);
		}
		
		return;
	}
	
	/**
	 * 予約後、退避した組版レコードと組版ファイルを削除
	 */
	private function delete_kumihan_save_data($t_typeset_id)
	{
		if (is_null($t_typeset_id) == true) {
			return;
		}
		
		$sql_typ_bk = "
			DELETE FROM 
				t_yoyaku_bk_typeset_files
			WHERE
				t_yoyaku_bk_typeset_id = :t_typeset_id
		";
		$this->execute($sql_typ_bk, array('t_typeset_id'=>$t_typeset_id));
		
		// 組版退避先フォルダ削除
		$file_lib = new File_lib();
		$save_path = new Path_lib(AkAGANE_YOYAKU_KUMIHAN_BK_PATH);
		$save_path->combine($t_typeset_id);
		if ($file_lib->FolderExists($save_path->get_full_path()) === true) {
			$file_lib->DeleteFolder($save_path->get_full_path());
		}
	}

	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 関連商品
	 */
	/*private function conv_relation($param_ins)
	{	
		// 更新
		$this->update_yoyaku_relation($param_ins);
		
		// 後処理
		$this->update_comp_relation($param_ins);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 関連情報
	 */
	/*private function conv_relation_info($param_ins)
	{
		// 更新
		$this->update_yoyaku_relation_info($param_ins);
		
		// 後処理
		$this->update_comp_relation_info($param_ins);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 共有情報
	 */
	/*private function conv_kyoyu($param_ins)
	{
		// 更新
		$this->update_yoyaku_kyoyu($param_ins);
		
		// 後処理
		$this->update_comp_kyoyu($param_ins);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/*private function conv_syuyaku($param_ins)
	{
		// 更新
		$this->update_yoyaku_syuyaku($param_ins);
		
		// 後処理
		$this->update_comp_syuyaku($param_ins);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28

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
			ORDER BY
				disp_seq
		";
		return $this->query($sql);
	}
	
	/**
	 * 反映する予約シリーズ情報一覧
	 */
	private function get_conv_yoyaku_list($param)
	{
		$sql = "
			SELECT
				V1.*
			,	yctg.t_yoyaku_ctg_id
			FROM
				(SELECT
					yhead.t_yoyaku_head_id
				,	to_char(yhead.conv_start_date, 'YYYY/MM/DD') AS conv_start_date
				,	to_char(yhead.conv_end_date, 'YYYY/MM/DD') AS conv_end_date
				,	to_char(yhead.web_ref_date, 'YYYY/MM/DD') AS web_ref_date
				,	yhead.comp_flg AS yhead_comp_flg
				,	yhead.m_lang_id
				,	ylang.t_yoyaku_series_lang_id
				,	ylang.t_series_head_id
				,	ylang.t_series_lang_id
				,	slang.t_series_ver_id
				,	shead.t_ctg_head_id
				,	shead.series_id
				,	sver.major_ver
				,	sver.minor_ver
				,	ylang.comp_flg AS ylang_comp_flg
				,	yver.t_yoyaku_series_ver_id
				,	ymei.t_yoyaku_series_mei_id
				,	COALESCE(to_char(yhead.conv_start_date, 'YYYY/MM/DD'), '')
					|| '-' || COALESCE(to_char(yhead.conv_end_date, 'YYYY/MM/DD'), '')
					|| '(' || COALESCE(to_char(yhead.web_ref_date, 'YYYY/MM/DD'), '') || ')'
					AS yoyaku_head_date
				,	yver.approval_status
				,	ylang.t_typeset_id
				FROM
					t_yoyaku_head yhead
				JOIN
					t_yoyaku_series_lang ylang
					ON	ylang.t_yoyaku_head_id = yhead.t_yoyaku_head_id
					AND	ylang.comp_flg = 0
				JOIN
					t_yoyaku_series_ver yver
					ON	yver.t_yoyaku_series_ver_id = ylang.t_yoyaku_series_ver_id
				JOIN
					t_yoyaku_series_mei ymei
					ON	ymei.t_yoyaku_series_mei_id = yver.t_series_mei_id
				JOIN
					t_series_lang slang
					ON	slang.t_series_lang_id = ylang.t_series_lang_id
					AND	slang.del_flg = 0
				JOIN
					t_series_head shead
					ON	shead.t_series_head_id = ylang.t_series_head_id
					AND	shead.del_flg = 0
				JOIN
					t_series_ver sver
					ON	sver.t_series_ver_id = slang.t_series_ver_id
					AND	sver.del_flg = 0
				WHERE
					:exe_date BETWEEN to_char(yhead.conv_start_date, 'YYYY/MM/DD') AND to_char(yhead.conv_end_date, 'YYYY/MM/DD')
				AND	yhead.m_lang_id = :m_lang_id
				AND	yhead.comp_flg = 0
			) V1
			JOIN
				t_yoyaku_ctg yctg
				ON	yctg.org_t_ctg_head_id = V1.t_ctg_head_id
				AND	yctg.t_yoyaku_head_id = V1.t_yoyaku_head_id
			ORDER BY
				V1.conv_start_date
			,	V1.conv_end_date
			,	V1.web_ref_date
			,	V1.series_id
		";
		
		//$param['approval_status'] = AKAGANE_APPROVAL_STATUS_KEY_COMP;
		return $this->query($sql, $param);
	}
	
	/**
	 * レコード反映・追加
	 */
	private function update_series_info($info)
	{
		// 特記事項更新
		$sql_com = "
			UPDATE
				t_series_lang
			SET
				memo = (SELECT	memo
						FROM	t_yoyaku_series_lang
						WHERE	t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id)
			WHERE
				t_series_lang_id = :t_series_lang_id
		";
		$param_com = array(
			 't_series_lang_id'=>$info['t_series_lang_id']
			,'t_yoyaku_series_lang_id'=>$info['t_yoyaku_series_lang_id']
		);
		$this->execute($sql_com, $param_com);
		
		// Add start - miyazaki U_SYS - 2014/12/22
		// 現行の最新の版の承認ステータスを更新（承認済を除く）
		$sql_sel = "
			SELECT
				MAX(t_series_ver_id) AS t_series_ver_id
			FROM
				t_series_ver
			WHERE
				t_series_lang_id = :t_series_lang_id
			AND	del_flg = 0
		";
		$srows = $this->query($sql_sel, array(
			't_series_lang_id' => $info['t_series_lang_id']
		));
		
		if (count($srows) > 0) {
			$sql_ver3 = "
				UPDATE
					t_series_ver
				SET
					approval_status = :approval_status
				WHERE
					t_series_ver_id = :t_series_ver_id
				AND approval_status <> :approval_status_comp
			";
			$param_ver3 = array(
				't_series_ver_id' => $srows[0]['t_series_ver_id'],
				'approval_status' => AKAGANE_APPROVAL_STATUS_KEY_YOYAKU_COMP,
				'approval_status_comp' => AKAGANE_APPROVAL_STATUS_KEY_COMP
			);
			$this->execute($sql_ver3, $param_ver3);
		}
		// Add end - miyazaki U_SYS - 2014/12/22
		
		// メジャーアップ
		$sql_ver = "
			INSERT INTO t_series_ver (
				  t_series_lang_id
				, major_ver
				, minor_ver
				, t_series_mei_id
				, translate_status
				, approval_status
				, translate_base_lang_id
				, translate_base_major_ver
				, translate_base_minor_ver
				, image_flg
				, spec_flg
				, draw_flg
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			) SELECT
				  sver.t_series_lang_id
				, sver.major_ver + 1
				, 0
				, null
				, yver.translate_status
				, yver.approval_status
				, yver.translate_base_lang_id
				, yver.translate_base_major_ver
				, yver.translate_base_minor_ver
				, yver.image_flg
				, yver.spec_flg
				, yver.draw_flg
				, yver.del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_yoyaku_series_ver yver
			JOIN
				t_yoyaku_series_lang ylang
				ON	yver.t_yoyaku_series_lang_id = ylang.t_yoyaku_series_lang_id
			JOIN
				t_series_lang slang
				ON	slang.t_series_lang_id = ylang.t_series_lang_id
				AND	slang.del_flg = 0
			JOIN
				t_series_ver sver
				ON	sver.t_series_ver_id = slang.t_series_ver_id
				AND	sver.del_flg = 0
			WHERE
				yver.t_yoyaku_series_ver_id = :t_yoyaku_series_ver_id
		";
		$param_ver = array(
			't_yoyaku_series_ver_id' => $info['t_yoyaku_series_ver_id'],
			'add_user_id' => $info['m_user_id'],
			'upd_user_id' => $info['m_user_id']
		);
		$this->execute($sql_ver, $param_ver);
		
		// シリーズ版ID取得
		$rows = $this->query("SELECT LASTVAL() AS t_series_ver_id");
		$new_t_series_ver_id = $rows[0]['t_series_ver_id'];
		
//		// ヘッダ更新
//		$sql_head = "
//			UPDATE
//				t_series_head
//			SET
//				upd_user_id = :upd_user_id
//			,	upd_datetime = NOW()
//			WHERE
//				t_series_head_id = :t_series_head_id
//		";
//		$param_head = array(
//			't_series_head_id' => $info['t_series_head_id'],
//			'upd_user_id' => $info['m_user_id']
//		);
//		$this->execute($sql_head, $param_head);
		
		// シリーズ言語更新
		$sql_lang = "
			UPDATE
				t_series_lang
			SET
				t_series_ver_id = :t_series_ver_id
			WHERE
				t_series_lang_id = :t_series_lang_id
		";
		$param_lang = array(
			't_series_ver_id' => $new_t_series_ver_id,
			't_series_lang_id' => $info['t_series_lang_id']
		);
		$this->execute($sql_lang, $param_lang);
		
		// シリーズ明細
		$sql_mei = "
			INSERT INTO t_series_mei (
				  t_series_ver_id
				, series_name
				, kou_no
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			) SELECT
				  :t_series_ver_id
				, series_name
				, 999 -- kou_no 校NO 校了
				, 1 -- upd_fld_kbn
				, null -- upd_fld_name
				, 5 -- upd_kbn 更新区分 5：版改訂
				, 0 -- del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_yoyaku_series_mei
			WHERE
				t_yoyaku_series_mei_id = :t_yoyaku_series_mei_id
		";
		$param_mei = array(
			't_series_ver_id' => $new_t_series_ver_id,
			't_yoyaku_series_mei_id' => $info['t_yoyaku_series_mei_id'],
			'add_user_id' => $info['m_user_id'],
			'upd_user_id' => $info['m_user_id']
		);
		$this->execute($sql_mei, $param_mei);
		
		// シリーズ明細ID取得
		$rows = $this->query("SELECT LASTVAL() AS t_series_mei_id");
		$new_t_series_mei_id = $rows[0]['t_series_mei_id'];
		
		// シリーズ版更新
		$sql_ver2 = "
			UPDATE
				t_series_ver
			SET
				t_series_mei_id = :t_series_mei_id
			WHERE
				t_series_ver_id = :t_series_ver_id
		";
		$param_ver2 = array(
			't_series_mei_id' => $new_t_series_mei_id,
			't_series_ver_id' => $new_t_series_ver_id
		);
		$this->execute($sql_ver2, $param_ver2);

		// シリーズ明細履歴
		$sql_meih = "
			INSERT INTO t_series_mei_his (
				  t_series_mei_id
				, t_series_ver_id
				, series_id
				, series_name
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			) SELECT
				  t_series_mei_id
				, t_series_ver_id
				, (SELECT series_id FROM t_series_head WHERE t_series_head_id = :t_series_head_id) -- シリーズID
				, series_name
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			FROM
				t_series_mei
			WHERE
				t_series_mei_id = :t_series_mei_id
		";
		$param_meih = array(
			't_series_mei_id' => $new_t_series_mei_id,
			't_series_head_id' => $info['t_series_head_id']
		);
		$this->execute($sql_meih, $param_meih);

		// シリーズ承認
		$sql_app = "
			INSERT INTO t_series_approval (
				  t_series_mei_id
				, approval_user_kbn
				, approval_user_id
				, approval_status
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			) SELECT
				  :t_series_mei_id
				, yapp.approval_user_kbn
				, yapp.approval_user_id
				, yapp.approval_status
				, del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_yoyaku_series_approval yapp
			WHERE
				yapp.t_yoyaku_series_mei_id = :t_yoyaku_series_mei_id
		";
		$param_app = array(
			't_yoyaku_series_mei_id' => $info['t_yoyaku_series_mei_id'],
			't_series_mei_id' => $new_t_series_mei_id,
			'add_user_id' => $info['m_user_id'],
			'upd_user_id' => $info['m_user_id']
		);
		$this->execute($sql_app, $param_app);
		
		return array('t_series_mei_id'=>$new_t_series_mei_id, 't_series_ver_id' => $new_t_series_ver_id);
	}
	
	/**
	 * 対象のコメント一覧を取得
	 */
	private function get_comment_list($param)
	{
		$sql = "
			SELECT
				ycomm.xml_section_id
			,	ycomm.comment
			,	ycomm.m_user_id
			,	ycomm.comment_datetime
			,	ycomm.confirm_flg
			,	ycomm.t_yoyaku_comment_id
			,	ycomm.kou_no
			FROM
				t_yoyaku_series_mei ymei
			JOIN
				t_yoyaku_series_ver yver
				ON	ymei.t_yoyaku_series_ver_id = yver.t_yoyaku_series_ver_id
			JOIN
				t_yoyaku_comment ycomm
				ON	yver.t_yoyaku_series_ver_id = ycomm.t_yoyaku_series_ver_id
				AND	ymei.kou_no = ycomm.kou_no
			WHERE
				ymei.t_yoyaku_series_mei_id = :t_yoyaku_series_mei_id
		";
		
		$filter = ACWArray::filter($param, array('t_yoyaku_series_mei_id'));
		return $this->query($sql, $filter);
	}
	
	/**
	 * コメント情報登録
	 */
	private function insert_comment($param)
	{
		$sql = "
			INSERT INTO t_comment (
				t_series_ver_id
			,	kou_no
			,	xml_section_id
			,	comment
			,	m_user_id
			,	comment_datetime
			,	confirm_flg
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) VALUES (
				:t_series_ver_id
			,	:kou_no
			,	:xml_section_id
			,	:comment
			,	:m_user_id
			,	:comment_datetime
			,	:confirm_flg
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			)
		";
		
		$this->execute($sql, $param);
		
		$rows = $this->query("SELECT LASTVAL() AS t_yoyaku_comment_id");
		return $rows[0]['t_yoyaku_comment_id'];
	}
	
	/**
	 * 品番情報削除
	 */
	private function delete_item_info($param)
	{
		$sql = "
			DELETE FROM
				t_item_info
			WHERE
				m_lang_id = :m_lang_id
			AND series_item_no = :series_item_no
		";
		$sql_params = array(
			'm_lang_id' => $param['m_lang_id'],
			'series_item_no' => $param['series_id']
		);
		$this->execute($sql, $sql_params);
	}
	
	/**
	 * 品番情報登録
	 */
	private function insert_item_info($param, $sql_col)
	{
		$sql = "
			INSERT INTO t_item_info (
				";
		$sql .= $sql_col;
		$sql .= "
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) SELECT
				";
		$sql .= $sql_col;
		$sql .= "
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			FROM
				t_yoyaku_item_info
			WHERE
				series_item_no = :series_id
			AND	m_lang_id = :m_lang_id
			ORDER BY
				t_yoyaku_item_info_id
		";

		$this->execute($sql, $param);
	}
	
	/**
	 * キュー登録
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function insert_conv_queue($param)
	{
		$sql = "
			INSERT INTO \"T_REQ_QUEUE\" (
				req_datetime
			,	corp_nm
			,	t_ctg_head_id
			,	t_series_head_id
			,	t_series_lang_id
			,	t_series_ver_id
			,	major_ver
			,	minor_ver
			,	ver_no
			,	exec_kbn
			,	proc_status
			,	add_user_id
			,	upd_user_id
			,	upd_datetime
			,   template_name
			,	m_media_type_id
			,	m_template_id
			) VALUES (
				:req_datetime
			,	:corp_nm
			,	:t_ctg_head_id
			,	:t_series_head_id
			,	:t_series_lang_id
			,	:t_series_ver_id
			,	:major_ver
			,	:minor_ver
			,	null
			,	:exec_kbn
			,	:proc_status
			,	:m_user_id
			,	:m_user_id
			,	now()
			,   :template_name
			,	:m_media_type_id
			,	:m_template_id
			)
			RETURNING t_req_id
		";
		
		$param['corp_nm'] = SYSTEM_SERVER_NAME;
		$result = $this->query($sql, $param);
		
		return $result[0]['t_req_id'];
	}
	
	/**
	 * キュー更新
	 */
	private function update_conv_queue($param)
	{
		// Edit start - miyazaki Argo - NBKD-1081 - 2015/04/03
//		$sql = "
//			UPDATE
//				\"T_REQ_QUEUE\"
//			SET
//				template_path = :template_path
//			,	indd_output_path = :indd_output_path
//			,	pdf_output_path = :pdf_output_path
//			,	jpg_output_path = :jpg_output_path
//			,	log_output_path = :log_output_path
//			,	xml_path = :xml_path
//			,	image_path = :image_path
//			,	table_path = :table_path
//			WHERE
//				t_req_id = :t_req_id
//			AND	corp_nm = :corp_nm
//		";
		$sql = "
			UPDATE
				\"T_REQ_QUEUE\"
			SET
				template_path = :template_path
			,	indd_output_path = :indd_output_path
			,	pdf_output_path = :pdf_output_path
			,	jpg_output_path = :jpg_output_path
			,	log_output_path = :log_output_path
			,	xml_path = :xml_path
			,	image_path = :image_path
			,	table_path = :table_path
			WHERE
				t_req_id = :t_req_id
			AND	corp_nm = :corp_nm
		";
		// Edit end - miyazaki Argo - NBKD-1081 - 2015/04/03

		$param['corp_nm'] = SYSTEM_SERVER_NAME;
		$this->execute($sql, $param);
	}
	
	/**
	 * キューの削除
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function delete_conv_queue($param)
	{
		$sql = "
			DELETE FROM 
				\"T_REQ_QUEUE\"
			WHERE
				t_req_id = :t_req_id
			AND corp_nm = :corp_nm
		";
		
		$param['corp_nm'] = SYSTEM_SERVER_NAME;
		$this->execute($sql, $param);
	}
	
	/**
	 * 組版結果登録
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function insert_conv_typeset_files($param)
	{
		$sql = "
			INSERT INTO t_typeset_files (
				t_ctg_head_id
			,	t_series_head_id
			,	t_series_lang_id
			,	t_series_ver_id
			,	major_ver
			,	minor_ver
			,	ver_no
			,	indd_file_path
			,	zip_file_path
			,	pdf_file_path
			,	jpg_file_path
			,	log_file_path
			,	t_req_id
			,	add_user_id
			,	upd_user_id
			,	m_media_type_id
			,	m_template_id
			) VALUES (
				:t_ctg_head_id
			,	:t_series_head_id
			,	:t_series_lang_id
			,	:t_series_ver_id
			,	:major_ver
			,	:minor_ver
			,	null
			,	:indd_file_path
			,	:zip_file_path
			,	:pdf_file_path
			,	:jpg_file_path
			,	:log_file_path
			,	:t_req_id
			,	:m_user_id
			,	:m_user_id
			,	:m_media_type_id
			,	:m_template_id
			)
			RETURNING t_typeset_id
		";
		
		$result = $this->query($sql, $param);
		
		return $result[0]['t_typeset_id'];
	}
	
	/**
	 * 組版更新
	 */
	private function update_conv_typeset_files($param)
	{
		$sql = "
			UPDATE
				t_typeset_files
            SET
				t_ctg_head_id = :t_ctg_head_id
			,	t_series_head_id = :t_series_head_id
			,	t_series_lang_id = :t_series_lang_id
			,	t_series_ver_id = :t_series_ver_id
			,	major_ver = :major_ver
			,	minor_ver = :minor_ver
			,	ver_no = null
			,	indd_file_path = :indd_file_path
			,	zip_file_path = :zip_file_path
			,	pdf_file_path = :pdf_file_path
			,	jpg_file_path = :jpg_file_path
            ,   upd_user_id = :m_user_id
            ,   upd_datetime = NOW()
			WHERE   
				t_typeset_id = :t_typeset_id
		";
		$this->execute($sql, $param);
	}
	
	/**
	 * 組版結果削除
	 * Add - miyazaki Argo - NBKD-1081 - 2015/04/03
	 */
	private function delete_conv_typeset_files($param)
	{
		$sql = "
			DELETE FROM
				t_typeset_files
			WHERE
				t_typeset_id = :t_typeset_id
		";
		$this->execute($sql, $param);
	}
	
	/**
	 * 予約シリーズ反映完了更新
	 */
	private function update_comp_series($param, $new_t_series_ver_id)
	{
		$sql_ver = "
			SELECT 
				major_ver
			FROM
				t_series_ver
			WHERE
				t_series_ver_id = :t_series_ver_id
		";
		$ver = $this->query($sql_ver, array('t_series_ver_id'=>$new_t_series_ver_id));
		
		$sql = "
			UPDATE
				t_yoyaku_series_lang
			SET
				comp_flg = 1
			,	conv_t_series_ver_id = :conv_t_series_ver_id
			,	conv_major_ver = :conv_major_ver
			,	upd_user_id = :m_user_id
			,	upd_datetime = NOW()
			WHERE
				t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		
		$filter = ACWArray::filter($param, array('t_yoyaku_series_lang_id', 'm_user_id'));
		$filter['conv_t_series_ver_id'] = $new_t_series_ver_id;
		$filter['conv_major_ver'] = $ver[0]['major_ver'];
		$this->execute($sql, $filter);
	}
	
	/**
	 * 処理後の完了リストを取得
	 */
	private function get_ser_cmp_count($param)
	{
		$sql = "
			SELECT
				yhead.t_yoyaku_head_id
			,	COALESCE(COUNT(ylang.*), 0) AS ser_count
			,	COALESCE(SUM(ylang.comp_flg), 0) AS ser_cmp_count
			,	COALESCE(to_char(yhead.conv_start_date, 'YYYY/MM/DD'), '')
				|| '-' || COALESCE(to_char(yhead.conv_end_date, 'YYYY/MM/DD'), '')
				|| '(' || COALESCE(to_char(yhead.web_ref_date, 'YYYY/MM/DD'), '') || ')'
				AS yoyaku_head_date
			FROM
				t_yoyaku_head yhead
			LEFT JOIN
				t_yoyaku_series_lang ylang
				ON	ylang.t_yoyaku_head_id = yhead.t_yoyaku_head_id
			WHERE
				:exe_date BETWEEN to_char(yhead.conv_start_date, 'YYYY/MM/DD') AND to_char(yhead.conv_end_date, 'YYYY/MM/DD')
			AND	yhead.m_lang_id = :m_lang_id
			AND	yhead.comp_flg = 0
			GROUP BY
				yhead.t_yoyaku_head_id
			ORDER BY
				yhead.conv_start_date
			,	yhead.conv_end_date
			,	yhead.web_ref_date
		";
		return $this->query($sql, $param);
	}
	
	/**
	 * 予約ヘッダ反映完了更新
	 */
	private function update_comp_head($param)
	{
		$sql = "
			UPDATE
				t_yoyaku_head
			SET
				comp_flg = 1
			,	upd_user_id = :m_user_id
			,	upd_datetime = NOW()
			WHERE
				t_yoyaku_head_id = :t_yoyaku_head_id
		";
		
		$filter = ACWArray::filter($param, array('t_yoyaku_head_id', 'm_user_id'));
		$this->execute($sql, $filter);
	}
	
	/*
	 * カテゴリに紐つく子要素を取得
	 */
	private function get_yoyaku_ctg_child($t_yoyaku_ctg_id, $t_yoyaku_head_id)
	{
		// カテゴリに紐つくシリーズを探す
		$sql_sel = "
			SELECT 
				yctg.t_yoyaku_ctg_id
			,	yctg.org_t_ctg_head_id
			,	yctg.t_yoyaku_head_id
			,	yctg.oya_t_yoyaku_ctg_id
			,	COALESCE(V1.cnt, 0) AS ser_cnt
			,	COALESCE(V2.cnt, 0) AS ctg_cnt
			FROM
				t_yoyaku_ctg yctg
			LEFT JOIN
				(SELECT
					shead.t_ctg_head_id
				,	ylang.t_yoyaku_head_id
				,	COUNT(*) AS cnt
				FROM
					t_yoyaku_series_lang ylang
				JOIN
					t_series_head shead
					ON	ylang.t_series_head_id = shead.t_series_head_id
				WHERE
					ylang.t_yoyaku_head_id = :t_yoyaku_head_id
				AND	ylang.comp_flg = 0
				GROUP BY
					shead.t_ctg_head_id
				,	ylang.t_yoyaku_head_id
				) V1
				ON	yctg.org_t_ctg_head_id = V1.t_ctg_head_id
				AND	yctg.t_yoyaku_head_id = V1.t_yoyaku_head_id
			LEFT JOIN
				(SELECT 
					yctg.oya_t_yoyaku_ctg_id
				,	yctg.t_yoyaku_head_id
				,	COUNT(*) AS cnt
				FROM
					t_yoyaku_ctg yctg
				WHERE
					yctg.oya_t_yoyaku_ctg_id != -1
				AND	yctg.t_yoyaku_head_id = :t_yoyaku_head_id
				GROUP BY
					yctg.oya_t_yoyaku_ctg_id
				,	yctg.t_yoyaku_head_id
				) V2
				ON	yctg.t_yoyaku_ctg_id = V2.oya_t_yoyaku_ctg_id
				AND	yctg.t_yoyaku_head_id = V2.t_yoyaku_head_id
			WHERE
				yctg.t_yoyaku_ctg_id = :t_yoyaku_ctg_id
		";
		
		$filter = array('t_yoyaku_ctg_id'=>$t_yoyaku_ctg_id, 't_yoyaku_head_id'=>$t_yoyaku_head_id);
		$rows = $this->query($sql_sel, $filter);
		return $rows;
	}
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約関連商品更新
	 */
	/*private function update_yoyaku_relation($param_ins)
	{
		// 対象
		$sql_sel = "
			SELECT
				vsr.*
			,	sr.t_series_relation_id AS sr_t_series_relation_id
			FROM
				(SELECT
					ysr1.t_series_head_id
				,	ysr1.relation_t_series_head_id
				,	ysr1.t_series_relation_id
				,	ysr1.exec_kbn
				FROM
					t_yoyaku_series_relation ysr1
				WHERE
					ysr1.t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr1.comp_flg = 0
				UNION
				SELECT
					ysr2.t_series_head_id
				,	ysr2.relation_t_series_head_id
				,	ysr2.t_series_relation_id
				,	ysr2.exec_kbn
				FROM
					t_yoyaku_series_relation ysr2
				WHERE
					ysr2.relation_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr2.comp_flg = 0
				) vsr
			LEFT JOIN
				t_series_relation sr
				ON	sr.t_series_head_id = vsr.t_series_head_id
				AND	sr.relation_t_series_head_id = vsr.relation_t_series_head_id
			ORDER BY
				vsr.t_series_relation_id
		";
		$rows = $this->query($sql_sel, array('t_yoyaku_series_lang_id'=>$param_ins['t_yoyaku_series_lang_id']));
		
		$sql_ins = "
			INSERT INTO t_series_relation (
				m_lang_id
			,	t_series_head_id
			,	relation_t_series_head_id
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) VALUES (
				0
			,	:t_series_head_id
			,	:relation_t_series_head_id
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			)
		";
		
		$sql_del = "
			DELETE FROM
				t_series_relation 
			WHERE
				t_series_relation_id = :t_series_relation_id
		";
		
		foreach ($rows as $row) {
			if ($row['exec_kbn'] == 0) {
				continue;
			} else if ($row['exec_kbn'] == 1) {
				if (is_null($row['sr_t_series_relation_id']) == true) {
					// 追加
					$this->execute($sql_ins, array(
						 't_series_head_id' => $row['t_series_head_id']
						,'relation_t_series_head_id' => $row['relation_t_series_head_id']
						,'user_id_1'=>$param_ins['user_id_1']
						,'user_id_2'=>$param_ins['user_id_2']
					));
				}
			} else {
				if (is_null($row['sr_t_series_relation_id']) == false) {
					// 削除
					$this->execute($sql_del, array('t_series_relation_id' => $row['sr_t_series_relation_id']));
				}
			}
		}
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約関連商品後処理
	 */
	/*private function update_comp_relation($param)
	{
		unset($param['user_id_2']);
		
		$sql = "
			UPDATE
				t_yoyaku_series_relation
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql, $param);
		
		$sql_par = "
			UPDATE
				t_yoyaku_series_relation
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	relation_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql_par, $param);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 関連情報（自分親）の更新
	 */
	/*private function update_yoyaku_relation_info($param_ins)
	{
		$sql_sel = "
			SELECT
				vsr.*
			,	sr.t_series_relation_info_id AS sr_t_series_relation_info_id
			FROM
				(SELECT
					ysr1.t_series_head_id
				,	ysr1.relation_t_series_head_id
				,	ysr1.t_series_relation_info_id
				,	ysr1.exec_kbn
				FROM
					t_yoyaku_series_relation_info ysr1
				WHERE
					ysr1.t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr1.comp_flg = 0
				UNION
				SELECT
					ysr2.t_series_head_id
				,	ysr2.relation_t_series_head_id
				,	ysr2.t_series_relation_info_id
				,	ysr2.exec_kbn
				FROM
					t_yoyaku_series_relation_info ysr2
				WHERE
					ysr2.relation_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr2.comp_flg = 0
				) vsr
			LEFT JOIN
				t_series_relation_info sr
				ON	sr.t_series_head_id = vsr.t_series_head_id
				AND	sr.relation_t_series_head_id = vsr.relation_t_series_head_id
			ORDER BY
				vsr.t_series_relation_info_id
		";
		$rows = $this->query($sql_sel, array('t_yoyaku_series_lang_id'=>$param_ins['t_yoyaku_series_lang_id']));
		
		$sql_ins = "
			INSERT INTO t_series_relation_info (
				m_lang_id
			,	t_series_head_id
			,	relation_t_series_head_id
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) VALUES (
				1
			,	:t_series_head_id
			,	:relation_t_series_head_id
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			)
		";
		
		$sql_del = "
			DELETE FROM
				t_series_relation_info 
			WHERE
				t_series_relation_info_id = :t_series_relation_info_id
		";
		
		foreach ($rows as $row) {
			if ($row['exec_kbn'] == 0) {
				continue;
			} else if ($row['exec_kbn'] == 1) {
				if (is_null($row['sr_t_series_relation_info_id']) == true) {
					// 追加
					$this->execute($sql_ins, array(
						 't_series_head_id' => $row['t_series_head_id']
						,'relation_t_series_head_id' => $row['relation_t_series_head_id']
						,'user_id_1'=>$param_ins['user_id_1']
						,'user_id_2'=>$param_ins['user_id_2']
					));
				}
			} else {
				if (is_null($row['sr_t_series_relation_info_id']) == false) {
					// 削除
					$this->execute($sql_del, array('t_series_relation_info_id' => $row['sr_t_series_relation_info_id']));
				}
			}
		}
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約関連情報後処理
	 */
	/*private function update_comp_relation_info($param)
	{
		unset($param['user_id_2']);
		
		$sql = "
			UPDATE
				t_yoyaku_series_relation_info
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				t_yoyaku_head_id = :t_yoyaku_head_id
			AND	t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
			AND	comp_flg = 0
		";
		$this->execute($sql, $param);
		
		$sql_par = "
			UPDATE
				t_yoyaku_series_relation_info
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				t_yoyaku_head_id = :t_yoyaku_head_id
			AND	relation_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
			AND	comp_flg = 0
		";
		$this->execute($sql_par, $param);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約共有情報更新
	 */
	/*private function update_yoyaku_kyoyu($param_ins)
	{
		// 現行
		$sql_sel = "
			SELECT
				vsr.*
			,	sr.t_series_share_id AS sr_t_series_share_id
			FROM
				(SELECT
					ysr1.t_series_head_id
				,	ysr1.share_t_series_head_id
				,	ysr1.t_series_share_id
				,	ysr1.exec_kbn
				FROM
					t_yoyaku_series_share ysr1
				WHERE
					ysr1.t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr1.comp_flg = 0
				UNION
				SELECT
					ysr2.t_series_head_id
				,	ysr2.share_t_series_head_id
				,	ysr2.t_series_share_id
				,	ysr2.exec_kbn
				FROM
					t_yoyaku_series_share ysr2
				WHERE
					ysr2.share_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr2.comp_flg = 0
				) vsr
			LEFT JOIN
				t_series_share sr
				ON	sr.t_series_head_id = vsr.t_series_head_id
				AND	sr.share_t_series_head_id = vsr.share_t_series_head_id
			ORDER BY
				vsr.t_series_share_id
		";
		$rows = $this->query($sql_sel, array('t_yoyaku_series_lang_id'=>$param_ins['t_yoyaku_series_lang_id']));
		
		$sql_ins = "
			INSERT INTO t_series_share (
				m_lang_id
			,	t_series_head_id
			,	share_t_series_head_id
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) VALUES (
				1
			,	:t_series_head_id
			,	:share_t_series_head_id
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			)
		";
		
		$sql_del = "
			DELETE FROM
				t_series_share 
			WHERE
				t_series_share_id = :t_series_share_id
		";
		
		foreach ($rows as $row) {
			if ($row['exec_kbn'] == 0) {
				continue;
			} else if ($row['exec_kbn'] == 1) {
				if (is_null($row['sr_t_series_share_id']) == true) {
					// 追加
					$this->execute($sql_ins, array(
						 't_series_head_id' => $row['t_series_head_id']
						,'share_t_series_head_id' => $row['share_t_series_head_id']
						,'user_id_1'=>$param_ins['user_id_1']
						,'user_id_2'=>$param_ins['user_id_2']
					));
				}
			} else {
				if (is_null($row['sr_t_series_share_id']) == false) {
					// 削除
					$this->execute($sql_del, array('t_series_share_id' => $row['sr_t_series_share_id']));
				}
			}
		}
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約共有情報後処理
	 */
	/*private function update_comp_kyoyu($param)
	{
		unset($param['user_id_2']);
		
		$sql = "
			UPDATE
				t_yoyaku_series_share
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql, $param);
		
		$sql_par = "
			UPDATE
				t_yoyaku_series_share
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	share_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql_par, $param);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約集約情報更新
	 */
	/*private function update_yoyaku_syuyaku($param_ins)
	{
		// 現行
		$sql_sel = "
			SELECT
				vsr.*
			,	sr.t_req_id AS sr_t_req_id
			FROM
				(SELECT
					ysr1.t_series_head_id
				,	ysr1.parent_t_series_head_id
				,	ysr1.t_req_id
				,	ysr1.exec_kbn
				FROM
					t_yoyaku_ser_relation ysr1
				WHERE
					ysr1.t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr1.comp_flg = 0
				UNION
				SELECT
					ysr2.t_series_head_id
				,	ysr2.parent_t_series_head_id
				,	ysr2.t_req_id
				,	ysr2.exec_kbn
				FROM
					t_yoyaku_ser_relation ysr2
				WHERE
					ysr2.parent_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
				AND	ysr2.comp_flg = 0
				) vsr
			LEFT JOIN
				t_ser_relation sr
				ON	sr.t_series_head_id = vsr.t_series_head_id
				AND	sr.parent_t_series_head_id = vsr.parent_t_series_head_id
			ORDER BY
				vsr.t_req_id
		";
		$rows = $this->query($sql_sel, array('t_yoyaku_series_lang_id'=>$param_ins['t_yoyaku_series_lang_id']));
		
		$sql_ins = "
			INSERT INTO t_ser_relation (
				t_series_head_id
			,	parent_t_series_head_id
			,	inherited_flg
			,	del_flg
			,	add_user_id
			,	add_datetime
			,	upd_user_id
			,	upd_datetime
			) VALUES (
				:t_series_head_id
			,	:parent_t_series_head_id
			,	0
			,	0
			,	:user_id_1
			,	NOW()
			,	:user_id_2
			,	NOW()
			)
		";
		
		$sql_up = "
			UPDATE
				t_ser_relation
			SET
				del_flg = :del_flg
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				t_req_id = :t_req_id
		";
		
		foreach ($rows as $row) {
			if ($row['exec_kbn'] == 0) {
				continue;
			} else if ($row['exec_kbn'] == 1) {
				if (is_null($row['sr_t_req_id']) == true) {
					// 追加
					$this->execute($sql_ins, array(
						 't_series_head_id' => $row['t_series_head_id']
						,'parent_t_series_head_id' => $row['parent_t_series_head_id']
						,'user_id_1'=>$param_ins['user_id_1']
						,'user_id_2'=>$param_ins['user_id_2']
					));
				} else {
					// 追加
					$this->execute($sql_up, array(
						 'del_flg' => 0
						,'t_req_id' => $row['sr_t_req_id']
						,'user_id_1'=>$param_ins['user_id_1']
					));
				}
			} else {
				if (is_null($row['sr_t_req_id']) == false) {
					// 削除
					$this->execute($sql_up, array(
						 'del_flg' => 1
						,'t_req_id' => $row['sr_t_req_id']
						,'user_id_1'=>$param_ins['user_id_1']
					));
				}
			}
		}
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
	
	//Remove Start NBKD-1107 MinhVnit 2015/05/28
	/**
	 * 予約集約情報後処理
	 */
	/*private function update_comp_syuyaku($param)
	{
		unset($param['user_id_2']);
		
		$sql = "
			UPDATE
				t_yoyaku_ser_relation
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql, $param);
		
		$sql_par = "
			UPDATE
				t_yoyaku_ser_relation
			SET
				comp_flg = 1
			,	upd_user_id = :user_id_1
			,	upd_datetime = NOW()
			WHERE
				comp_flg = 0
			AND	t_yoyaku_head_id = :t_yoyaku_head_id
			AND	parent_t_yoyaku_series_lang_id = :t_yoyaku_series_lang_id
		";
		$this->execute($sql_par, $param);
	}*/
	//Remove End NBKD-1107 MinhVnit 2015/05/28
}

$model = new BatchConvYoyaku();
$model->main();

// 実行
//ACWCore::acwork();
/* ファイルの終わり */