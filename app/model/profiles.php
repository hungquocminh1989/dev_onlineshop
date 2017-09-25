<?php
/**
 * ログインを行う
*/
class profiles_model extends ACWModel
{
	public static function action_index()
	{
		$model = new tokenmanager_common_model();
		$curl = new curlpost_lib_model();
		$res = $model->_getAccUpdateInfo();
		
		$return['list'] = $res;
		
		return ACWView::template('profiles.html',$return);
	}
	
	public static function action_updatepicture()
	{
		$result = array('error_msg' => '');
		try{
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'account_select'
	        ));
	        $model = new profiles_model();
	        $model->update_photo_profile($param,TRUE);
			
			
	        //print_r($param['image_select']);
	        //ACWLog::debug_var('abcccc', $paramFiles[0]);
			return ACWView::json($result);
			
		} catch (Exception $e) {
			$result['error_msg'] = EXCEPTION_CATCH_ERROR_MSG;
			return ACWView::json($result);
		}
	}
	
	public static function action_updatecover()
	{
		$result = array('error_msg' => '');
		try{
			$param = self::get_param(array(
	                    'acw_url'
	                    , 'account_select'
	        ));
	        $model = new profiles_model();
	        $model->update_photo_profile($param,FALSE);
			
			
	        //print_r($param['image_select']);
	        //ACWLog::debug_var('abcccc', $paramFiles[0]);
			return ACWView::json($result);
			
		} catch (Exception $e) {
			$result['error_msg'] = EXCEPTION_CATCH_ERROR_MSG;
			return ACWView::json($result);
		}
	}
	
	public function update_photo_profile($param,$avatar){
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
		$listToken = $model->_getAccUpdateInfo($param['account_select']);
		if($listToken != NULL && count($listToken) > 0){
			foreach($listToken as $k => $value){
				if(isset($arrLinkImages[$k]) == TRUE ){
					if($avatar == TRUE){
						$res = $curl->setAvatar($value['token2'],$arrLinkImages[$k]);
					}
					else{
						$res = $curl->setCover($value['token2'],$arrLinkImages[$k]);
					}
				}
				else{
					if($avatar == TRUE){
						$res = $curl->setAvatar($value['token2'],$arrLinkImages[count($arrLinkImages)-1]);
						
					}
					else{
						$res = $curl->setCover($value['token2'],$arrLinkImages[count($arrLinkImages)-1]);
					}
				}
				
			}
		}
		
		
		foreach($arrPathImages as $key => $fileDelete){
			unlink($fileDelete);
		}
	}
	
	
}
/* ファイルの終わり */