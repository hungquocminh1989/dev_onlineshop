<?php
/**
 * ログインを行う
*/
class postfeed_model extends ACWModel
{
	public static function action_index()
	{
		$model = new tokenmanager_common_model();
		$curl = new curlpost_lib_model();
		$res = $model->_getAcc();
		
		$return['list'] = $res;
		
		return ACWView::template('postfeed.html',$return);
	}
	
	public static function action_post()
	{
		$result = array('error_msg' => '');
		try{
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'account_select'
	                    , 'list_tags'
	                    , 'post_content'
	        ));
	        
	        $paramFiles = array();
	        if(count($_FILES) > 0){
				$paramFiles = $_FILES;
			}
			
			//Copy to tmp folder
			$arrPathImages = array();
			$arrLinkImages = array();
			foreach($paramFiles as $k => $file){
				if(count($file) > 0){
					$type = $file['type'];
					$arr_type = explode('/',$type);
					$filename = md5(uniqid(rand().time(),1)).'.'.$arr_type[count($arr_type)-1];
					
					$sourcePath = $file['tmp_name'];
					$desPath = ACW_TMP_DIR.'/upload_images/'.$filename;
					copy($sourcePath,$desPath);
					$arrPathImages[] = $desPath;
					$arrLinkImages[] = ACW_BASE_URL.'tmp/upload_images/'.$filename;
				}
			}
			
			
			$model = new tokenmanager_common_model();
			$curl = new curlpost_lib_model();
			$listToken = $model->_getAcc($param['account_select']);
			
			foreach($listToken as $k => $value){
				$res = $curl->setPost($value['token2'],$param['post_content'],'',$param['list_tags'],$arrLinkImages);
			}
			
			foreach($arrPathImages as $key => $fileDelete){
				unlink($fileDelete);
			}
			
			
	        //print_r($param['image_select']);
	        //ACWLog::debug_var('abcccc', $paramFiles[0]);
			return ACWView::json($result);
			
		} catch (Exception $e) {
			$result['error_msg'] = EXCEPTION_CATCH_ERROR_MSG;
			return ACWView::json($result);
		}
	}
	
	
	
}
/* ファイルの終わり */