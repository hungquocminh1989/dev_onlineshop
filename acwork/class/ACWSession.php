<?php
/**
 * セッションを操作するクラス $_SESSIONを直接操作してはいけません。
 * ログを取ったりするので
 * 
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
class ACWSession
{
	/**
	* 前回のトークンの値
	*/
	static protected $pre_token;
	/**
	* 初期化
	*/
	public static function init()
	{
		// Add start - miyazaki Argo - 2016/12/26
		// セッション保存パス設定
		if (defined('ACW_SESSION_DIR')) {
			ini_set('session.save_path', ACW_SESSION_DIR);
			//Add Start LIXD-631 Nuoi VNIT 20161227
	        $file = new COM ('Scripting.FileSystemObject', null, CP_UTF8);
			
			$folderspec1 = new VARIANT(ACW_SESSION_PARENT_DIR, VT_BSTR, CP_UTF8);
			
			$exist_folder1 = $file->FolderExists($folderspec1);
			if($exist_folder1 != TRUE)
			{
				$file->CreateFolder($folderspec1);
			}
			
			$folderspec2 = new VARIANT(ACW_SESSION_DIR, VT_BSTR, CP_UTF8);
			
			$exist_folder2 = $file->FolderExists($folderspec2);
			if($exist_folder2 != TRUE)
			{
				$file->CreateFolder($folderspec2);
			}
	        //Add End LIXD-631 Nuoi VNIT 20161227
		}
	        
		session_name(str_replace('.', '_', ACW_PROJECT)); // dot不可（PHP仕様）
		// Add end   - miyazaki Argo - 2016/12/26
		session_start();
		$token = md5(uniqid(rand(),1));
		static::$pre_token = static::get('acw_token');
		static::set('acw_token', $token);
	}
	/**
	* セッションID再作成
	*/
	public static function regenerate()
	{
		session_regenerate_id(true);
	}
	/**
	* セッションID獲得
	*/
	public static function get_id()
	{
		return session_id();
	}
	/**
	* セッションID設定（initより前に書く必要あり）
	*/
	public static function set_id($id)
	{
		return session_id($id);
	}
	/**
	* 設定
	*/
	public static function set($key, $val)
	{
		$_SESSION[$key] = $val;
	}
	/**
	* 獲得
	*/
	public static function get($key)
	{
		if (isset($_SESSION[$key]) == false) {
			return null;
		}
		return $_SESSION[$key];
	}
	/**
	* トークンチェック
	*/
	public static function check_token($param_token)
	{
		return ($param_token === static::get('acw_token')) ? true : false;
	}
	/**
	* 削除
	*/
	public static function del($key)
	{
		unset($_SESSION[$key]);
	}
	/**
	* 全部削除
	*/
	public static function destroy()
	{
		$_SESSION = array(); 
		session_destroy();
	}
	/**
	* セッションのロックを解放
	*/
	public static function write_close($restart=false)
	{
		session_write_close();
		if ($restart()) {
			session_start();
		}
	}
}
/* ファイルの終わり */