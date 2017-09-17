<?php
/**
 * ログインを行う
*/
class iphonetoken_lib_model extends ACWModel
{
	
	const API_SECRET = "c1e620fa708a1d5696fb991c1bde5662";
	const URL_API_FB = "https://api.facebook.com/restserver.php";

	public function get_token($user, $pass){
		header('Origin: https://facebook.com');
		$data = array(
			"api_key" => "3e7c78e35a76a9299309885393b02d97",
			//"credentials_type" => "password",
			"email" => $user,
			"format" => "JSON",
		//	"generate_machine_id" => "1",
		//	"generate_session_cookies" => "1",
			"locale" => "vi_vn",
			"method" => "auth.login",
			"password" => $pass,
			"return_ssl_resources" => "0",
			"v" => "1.0"
		);
		$this->sign_creator($data);
		$response = $this->cURL('GET', false, $data);
		$res = json_decode($response,true);
		$return_token = "";
		if(isset($res['access_token'])){
			$return_token = $res['access_token'];
		}
		return $return_token;
	}
	
	private function sign_creator(&$data){
		$sig = "";
		foreach($data as $key => $value){
			$sig .= "$key=$value";
		}
		$sig .= self::API_SECRET;
		$sig = md5($sig);
		return $data['sig'] = $sig;
	}
	private function cURL($method = 'GET', $url = false, $data){
	//sign_creator($data);
		//print_r($data);
		$c = curl_init();
		$user_agents = array(
			"Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5",
			"Mozilla/5.0 (iPhone; CPU iPhone OS 9_0_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13A452 Safari/601.1.46 Sleipnir/4.2.2m","Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E199 Safari/601.1","Mozilla/5.0 (iPod; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/45.0.2454.89 Mobile/13D15 Safari/600.1.4","Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E198 Safari/601.1"
		);
		$useragent = $user_agents[array_rand($user_agents)];
		$opts = array(
			CURLOPT_URL => ($url ? $url : self::URL_API_FB).($method == 'GET' ? '?'.http_build_query($data) : ''),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_USERAGENT => $useragent
		);
		if($method == 'POST'){
			$opts[CURLOPT_POST] = true;
			$opts[CURLOPT_POSTFIELDS] = $data;
		}
		curl_setopt_array($c, $opts);
		$d = curl_exec($c);
		curl_close($c);
		return $d;
	}
	
}
/* ファイルの終わり */