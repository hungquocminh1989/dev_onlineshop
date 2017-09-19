<?php
/**
 * ログインを行う
*/
class curlpost_lib_model extends ACWModel
{
	
	public function setPost_NewsFeed($access_token, $message){
		$postField = array(
			'message' => $message
		);
		$url = 'https://graph.facebook.com/v2.10/me/feed';
		$res = $this->graphRequest($access_token,$url,$postField);
		
		return $res;
	}
	
	public function getMe($access_token, $uid){
		$url = 'https://graph.facebook.com/v2.10/me?fields=id%2Cname';
		$res = $this->graphRequest($access_token,$url);
		
		return $res;
	}
	
	public function getUidInfo($access_token, $uid){
		$url = 'https://graph.facebook.com/v2.10/'.$uid.'?fields=id%2Cname';
		$res = $this->graphRequest($access_token,$url);
		
		return $res;
	}
	
	public function setAddFriend($access_token,$toID = "100006991569094"){
		$url = 'https://graph.facebook.com/v2.10/me/friends/$toID';
		$res = $this->graphRequest($access_token,$url);
		
		return $res;
	}
	
	public function getCountFriend($access_token,$uid){
		$url = 'https://graph.facebook.com/v2.10/'.$uid.'?fields=friends';
		$res = $this->graphRequest($access_token,$url);
		
		if(isset($res['friends']) == TRUE){
			return $res['friends']['summary']['total_count'];
		}
		
		return 0;
	}
	
	private function graphRequest($access_token = DEFAULT_TOKEN,$graphUrl,$PostFields = array()){
		$PostFields['access_token'] = $access_token;
		$result = $this->cURL($graphUrl,false,$PostFields);
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