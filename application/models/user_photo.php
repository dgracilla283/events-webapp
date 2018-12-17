<?php
require_once APPPATH . 'libraries/clients/api_user_photo_client.php';
class User_Photo extends CI_Model{

	private $objUserClient; 
		
	public function __construct(){
		$this->objUserClient = new api_user_photo_client();		
		$this->load->helper('cache');
	}

	/**
	 * @deprecated 
	 * @see fetch()
	 */
	public function getPhoto($uid){
		$res = $this->objUserClient->user_photo_get(array('fk_i_uid' => $uid), 'json');
		return $res; 
	}
	
	public function fetch($data) {
		$result = $this->objUserClient->user_photo_get($data);
		return $result;
	}
	
	public function addPhoto($data = array()) {
		if( empty($data) ) {
			return false;
		}
		$res = $this->objUserClient->user_photo_post($data, 'json');
		delete_all_cache(); 	 
		return $res;
	}
	
	public function deletePhoto($pid){
		delete_all_cache(); 
		return $this->objUserClient->remove_photo_get(array('userPhotoID'=>$pid), 'json');
	}

	public function fetch_multi_by_userid($strUserIds){
		if (empty($strUserIds)) {
			return array();
		}
		return $this->objUserClient->fetch_multi_by_userid_get(array('uids'=>$strUserIds), 'json');
	}
}