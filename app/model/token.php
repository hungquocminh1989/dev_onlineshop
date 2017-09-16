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
	
	public static function action_gettoken()
	{
		$param = self::get_param(array(
                    'acw_url'
                    , 'email'
                    , 'pass'
        ));
        $token_model = new accesstoken_lib_model();
        $result = $token_model->ios_token($param['email'],$param['pass']);
		return ACWView::json($result);
	}
	
}
/* ファイルの終わり */