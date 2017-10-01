<?php
/**
 * ログインを行う
*/
class sample_model extends ACWModel
{
	public static function action_index()
	{
		return ACWView::template('sample.html');
	}
}
/* ファイルの終わり */