<?php
/**
 * ルート定義ファイル
 *
 * フレームワークのルートを任意に設定したい場合ここに書きます
 * 既定は /model/action/parameter0/parameter1… です
 * /aaa/bbb/1 の場合 $level[0] は aaa $level[1] は bbb $level[2] は 1 です
 * 最終的には$levelにはパラメタしか残してはいけません
 *
 * 規定値は
 * $action = 'index';
 * $model = 'index';
 * です。
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
function acw_routes(&$level, &$model, &$action, &$dir_level)
{
	/**
	* 何も指定しなかったときの飛び先
	* 任意に変えてもOK
	*/
	if (count($level) == 0) {
		return true;
	}

	/**
	 * 
	 * 例えば /admin/order/list のような時に adminをディレクトリ扱いし、admin/Order_modelを呼び出してほしい場合は
	 * 以下のif文を参考にしてください
	 * 
	*/
	/*
	if ($level[0] == 'admin') {
		$dir_level[] = array_shift($level);	// adminはディレクトリ
	}
	*/

	/**
	* modelのみの指定
	*/
	if (count($level) == 1) {
		$model = array_shift($level);
		return true;
	}

	/**
	* 既定の動作
	*/
	$model = array_shift($level);
	$action = array_shift($level);
	return true;

	/**
	 * 
	 * 例えば /admin/order/list のような時に AdmninOrder_modelを呼び出してほしい場合は
	 * 以下のif文を参照してください
	 * 
	*/
	/*
	if ($level[0] == 'admin') {
		array_shift($level);	// admnin捨てる
		$model = 'admin' . array_shift($level);
		$action = array_shift($level);
		return true;
	}
	*/

	/**
	* 厳密にチェックし404を返したい場合はreturn false; にしてください
	*/
	/* return false; */
}
/* 終わり */
