<?php
/**
 * ログインを行う
*/
class curlpost_lib_model extends ACWModel
{
	
	/**Get UID 
	* curl -i -X GET \
 "https://graph.facebook.com/v2.10/100006991569094?fields=friends.limit(5000)&access_token=EAAAAAYsX7TsBALE8863zMCui5FRqz8oZCVOvi9ZAsxXo50KVQ5jUHlRBt8aqk08bb2kpv8cSm38LfnZCNt9EO3VINVpLJBinRuCx6KC34UH2WsuVZA9ehFtERwqTw0dT1QAZAnk7ZCZAhdZBKfL1fNElCkF4HAlDiR7VjLIEeG6MNd7Mv2epO9zfFVU1WeynE3nsZARZBv9AgYvryx5tKtq6zZAEt3wYxspmIAZD"
	*/
	public function getNewsFeedData($access_token){
		$url = "https://graph.facebook.com/me/home?access_token=".$access_token;
		$res = $this->graphRequest_GET($url);
		
		return $res;
	}
	
	public function getUserProfile($access_token){
		$url = "https://graph.facebook.com/me/feed?access_token=".$access_token;
		$res = $this->graphRequest_GET($url);
		
		return $res;
	}
	
	public function getListFriendsRequest($access_token){
		$url = "https://graph.facebook.com/me/friendrequests?access_token=".$access_token;
		$res = $this->graphRequest_GET($url);
		return $res;
	}
	
	public function setAllowFriendRequest($access_token, $uid){
		$url = "https://graph.facebook.com/me/friends/".$uid."?method=post&access_token=".$access_token;
		$res = $this->graphRequest_GET($url);
		/**
		* Param return
		* [true/false]
		*/
		if($res === TRUE){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function setPost($access_token, $content = '',$link = '', $tags_id = '', $upload_image = array()){
		
		//Upload photo unPublished
		$postField = array(
			'message' => $content,
			'link' => $link,
			'tags' => $tags_id
			
		);
		
		if(count($upload_image) > 0){
			foreach($upload_image as $key => $value){
				$post1 = array(
					'url' => $value,
					'published' => FALSE
					
				);
				/**
				* Param input :
				* [url] 
				* [published] = false
				*/
				$url = 'https://graph.facebook.com/v2.10/me/photos';
				$res = $this->graphRequest_POST($access_token,$url,$post1);
				
				/**
				* Param return
				* [id] : "126749868068985" 
				*/
				if($res != NULL && isset($res['id'])== TRUE){
					$postField['attached_media['.$key.']'] = '{"media_fbid":"'.$res['id'].'"}';
				}
			}
				
		}
		
		/**
		* Param input:
		* [attached_media] = {"media_fbid":"126749868068985"}
		* [message]
		* [tags]
		*/
		$url = 'https://graph.facebook.com/v2.10/me/feed';
		$res = $this->graphRequest_POST($access_token,$url,$postField);
		
		/**
		* Param return
		* [id]: "100022019164667_126750331402272", 
		* [post_supports_client_mutation_id]: true
		*/
		if($res != NULL && count($res) > 0){
			if($res['id'] != '' && $res['post_supports_client_mutation_id'] === TRUE){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function setAvatar($access_token, $upload_image){
		//Upload photo unPublished
		$post1 = array(
			'url' => $upload_image,
			'published' => FALSE
			
		);
		
		/**
		* Param input :
		* [url] 
		* [published] = false
		*/
		$url = 'https://graph.facebook.com/v2.10/me/photos';
		$res = $this->graphRequest_POST($access_token,$url,$post1);
		
		/**
		* Param return
		* [id]: "126749868068985" 
		*/
		if($res != NULL && isset($res['id'])== TRUE){
			$postField = array(
				'photo' => $res['id'],
				'height' => '2000',
				'width' => '2000',
				'method' => 'post'
			);
			$url = 'https://graph.facebook.com/me/picture';
			$res = $this->graphRequest_POST($access_token,$url,$postField);
			
			return $res;
		}
		
		return NULL;
		
	}
	
	public function setCover($access_token, $upload_image){
		//Upload photo unPublished
		$post1 = array(
			'url' => $upload_image,
			'published' => FALSE
			
		);
		
		/**
		* Param input :
		* [url] 
		* [published] = false
		*/
		$url = 'https://graph.facebook.com/v2.10/me/photos';
		$res = $this->graphRequest_POST($access_token,$url,$post1);
		
		/**
		* Param return
		* [id]: "126749868068985" 
		*/
		if($res != NULL && isset($res['id'])== TRUE){
			$postField = array(
				'photo' => $res['id'],
				'method' => 'post'
			);
			$url = 'https://graph.facebook.com/me/cover';
			$res = $this->graphRequest_POST($access_token,$url,$postField);
			
			return $res;
		}
		
		return NULL;
		
	}
	
	public function getMe($access_token){
		$url = 'https://graph.facebook.com/v2.10/me?fields=id%2Cname';
		$res = $this->graphRequest_POST($access_token,$url);
		
		return $res;
	}
	
	public function getUidInfo($access_token, $uid){
		$url = 'https://graph.facebook.com/'.$uid.'?fields=id%2Cname&access_token='.$access_token;
		$res = $this->graphRequest_GET($url);
		
		return $res;
	}
	
	public function setAddFriend($access_token,$toID = "100006991569094"){
		$url = 'https://graph.facebook.com/v2.10/me/friends/'.$toID;
		$res = $this->graphRequest_POST($access_token,$url);
		
		return $res;
	}
	
	public function getCountFriend($access_token,$uid){
		
		/**
		* Param input
		* uid 
		*/
		$url = 'https://graph.facebook.com/v2.10/'.$uid.'?fields=friends';
		$res = $this->graphRequest_POST($access_token,$url);
		
		/**
		* Param return
		* Array  [friends]
		* [id]
		*/
		if(isset($res['friends']) == TRUE && count($res['friends']) > 0){
			return $res['friends']['summary']['total_count'];
		}
		
		return '[Error]';
	}
	
	private function graphRequest_POST($access_token = DEFAULT_TOKEN,$graphUrl,$PostFields = array()){
		$PostFields['access_token'] = $access_token;
		$result = $this->cURL($graphUrl,false,$PostFields);
		return $result;
	}
	
	private function graphRequest_GET($graphUrl){
		$result = $this->cURL($graphUrl,false,null);
		return $result;
	}
	
	public function getPHPExecutableFromPath() {
	  $paths = explode(PATH_SEPARATOR, getenv('PATH'));
	  foreach ($paths as $path) {
	    // we need this for XAMPP (Windows)
	    if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
	        return $path;
	    }
	    else {
	        $php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
	        if (file_exists($php_executable) && is_file($php_executable)) {
	           return $php_executable;
	        }
	    }
	  }
	  return FALSE; // not found
	}
	
	public function cURL($url, $cookie = false, $PostFields = false){
		$c = curl_init();
		$opts = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,//TRUE là return JSON
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
		//ACWLog::debug_var('test1112222', $data);
		$res = json_decode($data,true);
		return $res;
	}
	
	public function execute_batch($url, $cookie = false, $PostFields = false){
		$arrUserAgent = array(
			'Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5',
			"Mozilla/5.0 (Linux; Android 5.0.2; Andromax C46B2G Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/60.0.0.16.76;]",
			"[FBAN/FB4A;FBAV/35.0.0.48.273;FBDM/{density=1.33125,width=800,height=1205};FBLC/en_US;FBCR/;FBPN/com.facebook.katana;FBDV/Nexus 7;FBSV/4.1.1;FBBK/0;]",
			"Mozilla/5.0 (Linux; Android 5.1.1; SM-N9208 Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36",
			"Mozilla/5.0 (Linux; U; Android 5.0; en-US; ASUS_Z008 Build/LRX21V) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.8.0.718 U3/0.8.0 Mobile Safari/534.30",
			"Mozilla/5.0 (Linux; U; Android 5.1; en-US; E5563 Build/29.1.B.0.101) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.10.0.796 U3/0.8.0 Mobile Safari/534.30",
			"Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; Celkon A406 Build/MocorDroid2.3.5) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1"
		);
		shuffle($arrUserAgent);
		$c = curl_init();
		$opts = array(
			CURLOPT_URL => $url,
			CURLOPT_TIMEOUT_MS => 1,
			CURLOPT_NOSIGNAL => 1,
			CURLOPT_RETURNTRANSFER => true,//TRUE là return JSON
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_USERAGENT => $arrUserAgent[0],
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