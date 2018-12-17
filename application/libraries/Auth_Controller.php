<?php
require_once APPPATH . 'libraries/clients/api_admin_client.php';

class Auth_Controller extends CI_Controller {

	protected $userData;
	
	public function __construct() {
		session_start(); 
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		
		$this->_getUserSession();
		
		$objAdminClient = new api_admin_client();
		$isAdmin = true;
		
		//retrieve admin id list
		$adminData = $objAdminClient->admins_get();
		$adminIDList = array();
		foreach($adminData['result'] as $adminEntry){
			array_push($adminIDList, $adminEntry['user_id']);
		}
		
		//yo::log($this->userData); exit;    
			// check if admin
		//if (!in_array($_SESSION['_user']['userID'], $adminIDList))
		if (!in_array($this->userData['userID'], $adminIDList))
			$isAdmin = false;
			
		if (empty($this->userData) || !$isAdmin) {
			$query = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';
			$back_url = $this->config->site_url().$this->uri->uri_string(). $query;
			if ($back_url && $back_url != 'admin')
				redirect('login/index?back_url='. $back_url);
			else 
				redirect('login/index');
		}
	}
	
	private function _getUserSession() {
		$this->userData = $this->session->userdata('_user');
		//$this->userData = $_SESSION['_user'];
	}
	
	protected function _clearUserSession() {
		//unset($_SESSION['_user']);  
		//unset($_SESSION['_lastExpandID']);  
		//unset($_SESSION['_redirect']);  
		
		$this->session->unset_userdata('_user');
		$this->session->unset_userdata('_lastExpandID');
		$this->session->unset_userdata('_redirect');
		$this->load->view('admin/login');
	}
	
}
