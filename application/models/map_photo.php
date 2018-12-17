<?php
require_once APPPATH . 'libraries/clients/api_map_photo_client.php';
class Map_Photo extends CI_Model{

	private $objMapPhotoClient; 
		
	public function __construct(){
		$this->objMapPhotoClient = new api_map_photo_client();		
		$this->load->helper('cache');
	}

	/**
	 * @deprecated 
	 * @see fetch()
	 */
	public function getPhoto($eventId){
		$res = $this->objMapPhotoClient->map_photo_get(array('event_id' => $eventId), 'json');
		return $res; 
	}
	
	public function fetch($data) {
		$result = $this->objMapPhotoClient->map_photo_get($data);
		return $result;
	}
	
	public function addPhoto($data = array()) {
		if( empty($data) ) {
			return false;
		}
		//dump($data); exit;
		$res = $this->objMapPhotoClient->map_photo_post($data, 'json');
		delete_all_cache(); 	 
		return $res;
	}
	
	public function deletePhoto($id){
		delete_all_cache(); 
		return $this->objMapPhotoClient->remove_photo_get(array('mapPhotoID'=>$id), 'json');
	}

	public function fetch_multi_by_userid($strUserIds){
		if (empty($strUserIds)) {
			return array();
		}
		return $this->objMapPhotoClient->fetch_multi_by_userid_get(array('uids'=>$strUserIds), 'json');
	}
	
	public function getUploadPath()
	{
		$uploadPath = $this->config->config['upload_path'] . 'map';
		if (!is_dir($uploadPath)) {
			mkdir($uploadPath);
		}
		return $uploadPath;
	}
}