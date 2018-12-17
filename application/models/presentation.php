<?php
require_once APPPATH . 'libraries/clients/api_presentation_client.php';
class Presentation extends CI_Model{

	private  $objPresentation;

	public function __construct(){
		$this->load->helper('cache');
		$this->objPresentation = new api_presentation_client();
	}

	public function getPresentation($data) {
		$params = array(
			'presentationID' => $data['presentationID']
		);
		$res = $this->objPresentation->presentation_get($params);

		return $res['result'];
	}

	public function getPresentationsByCategory($data) {
		$params = array(
			'presentation_category_id' => $data['presentation_category_id'],
			'sort_order' => 'ASC'
		);
		$res = $this->objPresentation->presentation_get($params);

		return $res['result'];
	}

	public function getPresentations($data) {
		$params = array(
			'presentationID' => $data['presentationID'],
			'presentation_category_id' => $data['presentation_category_id'],
			'title' => $data['title'],
			'url' => $data['url'],
			'sort_field' => 'order',
			'sort_order' => 'ASC'
		);
		$res = $this->objPresentation->presentation_get($params);

		return $res['result'];
	}

	public function savePresentation($data) {
		$params = array(
			'presentationID' => $data['presentationID'],
			'presentation_category_id' => $data['presentation_category_id'],
			'title' => $data['title'],
			'url'  => prep_url($data['url']),
			'order'  =>  $data['order'],
			'display_type' => $data['display_type']
		);

		if (isset($data['document_meta'])) $params['document_meta'] = $data['document_meta'];
		$res = $this->objPresentation->presentation_post($params);
		return  $res['result'];
	}

	public function deletePresentation($data) {
		return $this->objPresentation->presentation_remove_get($data);
	}

	public function getUploadPath()
	{
		$uploadPath = $this->config->config['upload_path'] . 'presentation';
		if (!is_dir($uploadPath)) {
			mkdir($uploadPath);
		}
		return $uploadPath;
	}
}