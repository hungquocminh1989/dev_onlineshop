<?php
/**
 * ログインを行う
*/
class Login_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
	}


	/**
	* ログイン画面表示
	*/
	public static function action_index()
	{
		return ACWView::template('login.html', array('user_id' => ''));
	}
	
	// ログインエラー画面表示
	public static function action_error()
	{
		return ACWView::template('error.html', array());
	}

	/**
	* ログイン認証
	*/
	public static function action_auth()
	{
		$param = self::get_param(array('user_id', 'passwd'));
		if (self::get_validate_result()) {
			// パラメタ正常
			$login = new Login_model();
			$user_info = $login->check_login($param);			
			if (is_null($user_info) == false) {
				// セッション設定
				ACWSession::set('user_info', $user_info);
				// メニューへリダイレクト
				return ACWView::redirect(ACW_BASE_URL . 'top');
			} else {
				ACWError::add('message', 'ユーザーIDまたはパスワードが違います。');
			}
		}

		// もう一度表示
		return ACWView::template('login.html', $param);
	}

	/**
	* ログアウト
	*/
	public static function action_delete()
	{
		// セッション削除
		ACWSession::del('user_info');
		// ログイン画面へリダイレクト
		return ACWView::redirect(ACW_BASE_URL . 'login');
	}

	/**
	* 入力チェック
	*/
	public static function validate($action, &$param)
	{
		if ($action == 'auth') {
			if (isset($param['user_id']) == false) {
				ACWError::add('message', 'ユーザーIDを入力してください。');
			} else if ($param['passwd'] == false) {
				ACWError::add('message', 'パスワードを入力してください。');
			}
			if (ACWError::count() == 0) {
				return true;	// チェックOK
			}
		}
		return false;
	}

	/**
	* 他モデルからのログインチェック
	*/
	public static function check()
	{
		// セッション取得
		$user_info = ACWSession::get('user_info');
		$file_upload = $_FILES; //Add LIXD-614 Tin VNIT 20161129
		if (is_null($user_info)) {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					// ajaxリクエストでセッション切れ判明
					header('HTTP/1.0 401 Unauthorized');
					exit();	// 終わり
				}
			}
			//Add start LIXD-614 Tin VNIT 20161129
			elseif(count($file_upload) > 0)
			{
				if(isset($_SERVER['REQUEST_URI']))
				{
					if (strpos($_SERVER['REQUEST_URI'], 'mscolor') == false) {
					    // ajaxリクエストでセッション切れ判明
						echo json_encode(array('status' => '401','error' => array(0=>'401 Unauthorized')));
						exit();	// 終わり
					}						
				}
					
			}
			//Add end LIXD-614 Tin VNIT 20161129
			ACWView::redirect(ACW_BASE_URL . 'login/error');
			exit();
		}
		if (isset($user_info['user_id'])) {
			// user_idがある事を条件に
			ACWView::init_template_var(array('user_info' => $user_info));
			// ログをユーザーごとに
			ACWLog::set_user_suffix($user_info['m_user_id']);
			return true;
		}
		return false;
	}


	///////////////////////////////////////////////////////////////////////////
	// 以下DB処理
	///////////////////////////////////////////////////////////////////////////
	/**
	* DBでのログインチェック
	*/
	public function check_login($param)
	{
		$param['passwd'] = md5(AKAGANE_SALT . $param['passwd']);
		$result = $this->query('
			SELECT
				  m_user_id
				, user_id
				, user_name
				, mail_address
				, user_auth
				, user_ver_auth
				, user_kbn
			FROM
				m_user
			WHERE
				user_id = :user_id
			AND
				pass = :passwd
			AND
				del_flg = 0
			', $param);
		if (count($result) != 1) {
			return null;
		}
		return $result[0];
	}
	//Add start LIXD-463 TinVNIT 3/30/2016
	public static function check_permission()
	{
		$model_name = ACWCore::get_var('model');
		$user_info = ACWSession::get('user_info');
		$user_kbn = $user_info['user_kbn'];
		
		//user_kbn integer, -- ユーザー区分: 1：管理者（マスタ変更可能）、2：一般
		if($user_kbn == 2)
		{
			$list_deny = unserialize (AKAGANE_PERMISION_DENY_NORMAL_USER);
			if(in_array($model_name,$list_deny))
			{
				echo "Sorry, you do not have permission to access this page<br/>";
				echo "Go to <a href='".ACW_BASE_URL."'>Home page</a>";
				exit;
			}
		}
	}
	//Add end LIXD-463 TinVNIT 3/30/2016
}
/* ファイルの終わり */