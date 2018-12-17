<?php
require_once APPPATH . 'libraries/clients/api_presentation_category_client.php';
class Presentation_Category extends CI_Model{
	
	private  $objPresentationCategory; 
	
	public function __construct(){		
		$this->load->helper('cache');	
		$this->objPresentationCategory = new api_presentation_category_client();		
	}
	
	public function getPresentationCategory($data) {
		$params = array(
			'presentationCategoryID' => $data['presentationCategoryID']
		); 
		$res = $this->objPresentationCategory->presentation_category_get($params); 	

		return $res['result'];
	}
	
	public function getPresentationCategories($data) {
		$params = array(
			'presentationCategoryID' => $data['presentationCategoryID'], 
			'event_id' => $data['event_id'],
		); 
		$res = $this->objPresentationCategory->presentation_category_get($params); 	

		return $res['result'];
	}
	
	public function savePresentationCategory($data) {
		$params = array(
			'presentationCategoryID' => $data['presentationCategoryID'], 
			'name' => $data['name'],
			'event_id'  => $data['event_id'], 
		);	
		
		$res = $this->objPresentationCategory->presentation_category_post($params, 'json');
		return  $res['result'];
	}

	public function deletePresentationCategory($data) {
		$params = array(
			'presentationCategoryID' => $data['presentationCategoryID'], 
			'event_id'  => $data['event_id']
		); 
		return $this->objPresentationCategory->presentation_category_remove_get($params); 
	}
	
}