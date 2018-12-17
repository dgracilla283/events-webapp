<?php
require_once APPPATH . 'libraries/clients/api_activation_key_client.php';
class Activation_Key extends CI_Model{

	//private $objCompanionClient; 
		
	public function __construct(){
		$this->objActivationKeyClient = new api_activation_key_client();		
		$this->load->helper('cache');	
	}
	
	public function getActivationKey($data){
		$res = $this->objActivationKeyClient->activation_key_get(array('user_id'=>$data['user_id'], 'key' => $data['key']), 'json');
		return $res;
	}

	function addActivationKey($userID){
		//generate random chars needed for key
		$key_len = 20;
		$key = '';
		$i = 0;
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		while ($i < $key_len) {
			$key .= $chars{mt_rand(0,(strlen($chars)) - 1 )};
			$i++;
		}	
		$res = $this->objActivationKeyClient->add_activation_key_get(array('user_id'=>$userID, 'key' => $key), 'json');
		return $res;	
	}	
	
	function updateActivationKey($data){			
		$res = $this->objActivationKeyClient->update_activation_key_post($data, 'json');
		return $res;	
	}
}