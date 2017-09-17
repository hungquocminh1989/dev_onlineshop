<?php
/**
 * ログインを行う
*/
class curlpost_lib_model extends ACWModel
{
	public function meUpdateStatus($access_token){
		$cnf = array(
			'message' => 'ngum.',
			'access_token' =>  $access_token
		);

		$result = $this->cURL('https://graph.facebook.com/v2.10/me/feed',false,$cnf);
		
		return $result;
	}
	
	public function meInfo($access_token){
		$cnf = array(
			'access_token' =>  $access_token
		);

		$result = $this->cURL('https://graph.facebook.com/v2.10/me?fields=id%2Cname',false,$cnf);
		
		return $result;
	}
	
	public function addFriend($access_token,$toID = "100006991569094"){
		$cnf = array(
			'access_token' =>  $access_token
		);
		
		$result = $this->cURL('https://graph.facebook.com/v2.10/me/friends/$toID',false,$cnf);
		
		return $result;
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
		
		if($cookie){
			unlink($random);
		}
		$res = json_decode($data,true);
		return $res;
	}
}
/* ファイルの終わり */