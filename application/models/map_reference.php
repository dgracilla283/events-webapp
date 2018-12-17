<?php
require_once APPPATH . 'libraries/clients/api_map_reference_client.php';
class Map_Reference extends CI_Model{

	private $objMapReferenceClient; 
		
	public function __construct(){
		$this->objMapReferenceClient = new api_map_reference_client();		
		$this->load->helper('cache');
	}

	public function fetch($data) {
		$result = $this->objMapReferenceClient->map_reference_get($data);
		return $result;
	}
	
	public function add($data = array()) {
		if( empty($data) ) {
			return false;
		}
		//dump($data); exit;
		$res = $this->objMapReferenceClient->map_reference_post($data, 'json');
		delete_all_cache(); 	 
		return $res;
	}
	
	public function deletePhoto($id){
		delete_all_cache(); 
		return $this->objMapReferenceClient->remove_map_reference_get(array('mapPhotoID'=>$id), 'json');
	}

	public function fetch_multi_by_userid($strUserIds){
		/*if (empty($strUserIds)) {
			return array();
		}
		return $this->objMapReferenceClient->fetch_multi_by_userid_get(array('uids'=>$strUserIds), 'json');
		*/
	}
	
}