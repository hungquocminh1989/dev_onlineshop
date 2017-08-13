<?php

/**
 * モデル基本クラス
 *
 * 基本クラスであり、継承して使います
 *
 * @category   ACWork
 * @copyright  2013
 * @version    0.9
 */
class ACWModel extends ACWDB
{
	private static $validate_count = 0;

	public static function init()
	{

	}

	/**
	 * パラメタを持ってきます
	 * @param array $param パラメタ名の配列
	 * @param string $validate_id validateメソッドに渡す識別子
	 */
	protected static function get_param($param_key)
	{
		/* if (is_null($param_key)) {
		  $param_key = array();
		  } acw_url は　必ずではなくする */
		$param = ACWCore::$ctl->set_param_all($param_key);

		self::$validate_count++;
		if (self::$validate_count <= 1) { // validate内のget_paramで呼ばれないように
			$model_name = ACWCore::get_var('model_name');
			$validate_action = ACWCore::get_var('action');
			// call_user_funcでなく、call_user_func_arrayなのは引数が参照で渡せないから
			$ret = call_user_func_array(array($model_name, 'validate'), array($validate_action, &$param));
			ACWCore::set_var('last_validate', $ret);
			ACWCore::set_var('last_param', $param);
		}
		self::$validate_count--;
		return $param;
	}

	protected static function get_validate_result()
	{
		return ACWCore::get_var('last_validate');
	}

	public static function validate($validate_action, &$param)
	{
		return 'no validate!';
	}
}

/* ファイルの終わり */