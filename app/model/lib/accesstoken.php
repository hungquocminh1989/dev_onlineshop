<?php
/**
 * ログインを行う
*/
class accesstoken_lib_model extends ACWModel
{
	public function ios_token($user, $pass){
		$token = array();
		$token['access_token'] = "";
		$token['error_msg'] = "";
		$cnf = array(
			'email' => $user,
			'pass' =>  $pass
		);

		//Login
		$cnf['login'] = 'Login';
		$random = ACW_TMP_DIR . "/" . md5(rand(00000000,99999999)).'.txt';
		$login = $this->cURL('https://m.facebook.com/login.php', $random, $cnf);
		
		$cookie = $this->getCookie($random);
		
		//print $login;
		if(preg_match('/name=\\\\"fb_dtsg\\\\" value=\\\\"(.*?)\\\\"/', $login, $response)){
			$fb_dtsg = $response[1];
			$responseToken = $this->cURL('https://www.facebook.com/v1.0/dialog/oauth/confirm', $random, 'fb_dtsg='.$fb_dtsg.'&app_id=165907476854626&redirect_uri=fbconnect://success&display=popup&access_token=&sdk=&from_post=1&private=&tos=&login=&read=&write=&extended=&social_confirm=&confirm=&seen_scopes=&auth_type=&auth_token=&auth_nonce=&default_audience=&ref=Default&return_format=access_token&domain=&sso_device=ios&__CONFIRM__=1');
			if(preg_match('/access_token=(.*?)&/', $responseToken, $token2)){
				$token['access_token'] = $user . ";" . $pass . ";" . $cookie . ";" . $token2[1];
				//echo json_encode($token);
			}else{
				$token['error_msg'] = 'Please allow Location Must ..';
				//echo json_encode($token);
			}
		}else{
			$token['error_msg'] = 'Email or Password is Invalid..';
			//echo json_encode($token);
		}
		unlink($random);
		return $token;
	}
	
	private function getCookie($path){
		$content = file_get_contents($path);
		$cookie = "";
		if(preg_match('/(c_user)(.*)(.*?)/', $content, $c_user)){
			$cookie .= str_replace("	","=",$c_user[0]);
		}
		
		if(preg_match('/(xs)(.*)(.*?)/', $content, $xs)){
			$cookie .= ";" . str_replace("	","=",$xs[0]);
		}
		
		if(preg_match('/(datr)(.*)(.*?)/', $content, $datr)){
			$cookie .= ";" . str_replace("	","=",$datr[0]);
		}
		$cookie = preg_replace("/\r\n|\r|\n/", '', $cookie);
		return $cookie;
	}
	
	private function cURL($url, $cookie = false, $PostFields = false){
		$c = curl_init();
		$opts = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5',
			CURLOPT_FOLLOWLOCATION => true
		);
		if($PostFields){
			$opts[CURLOPT_POST] = true;
			$opts[CURLOPT_POSTFIELDS] = $PostFields;
		}
		if($cookie){
			$opts[CURLOPT_COOKIE] = true;
			$opts[CURLOPT_COOKIEJAR] = $cookie;
			$opts[CURLOPT_COOKIEFILE] = $cookie;
		}
		curl_setopt_array($c, $opts);
		$data = curl_exec($c);
		curl_close($c);
		return $data;
	}
}
/* ファイルの終わり */