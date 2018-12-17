<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'libraries/clients/api_user_client.php';
require_once APPPATH . 'libraries/clients/api_admin_client.php';

class login extends CI_Controller {

	public function index() {
		// session_start(); 
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$back_url = ($this->input->post('back_url') != "")? $this->input->post('back_url'):  $this->input->get('back_url');
		
		$this->session->set_userdata('username', $username);
		//$_SESSION['username'] = $username; 
		
		$objEventClient = new api_user_client();
		$objAdminClient = new api_admin_client();
		
		$data = array();
		if (!empty($username) && !empty($password)) {
			$this->load->helper('url');
			$isAdmin = true;
			
			//retrieve admin id list
			$adminData = $objAdminClient->admins_get();
			$adminIDList = array();
			foreach($adminData['result'] as $adminEntry){
				array_push($adminIDList, $adminEntry['user_id']);
			}
			$userData = $objEventClient->login_get(array('email'=>$username, 'password'=>$password));
			// check if admin
			if (!in_array($userData['result']['userID'], $adminIDList)){
				$data['loginErrorMsg'] = 'Access not valid';
				$isAdmin = false;
			} 
			if (!empty($userData['result']) && $isAdmin) {
				$back_url = $this->input->post('back_url');
				$this->session->set_userdata('_user', $userData['result']);
				//$_SESSION['_user'] = $userData['result'];
				// echo $this->session->userdata('_redirect'); exit; 
				if (isset($back_url) && $back_url != "") {
				//if ($_SESSION['_redirect'] && $redirect = $_SESSION['_redirect']) {
					//$this->session->unset_userdata('_redirect');
					//unset($_SESSION['_redirect']); 
					redirect($back_url);
				} else {					
					redirect('admin/dashboard');
				}
			} else if (!empty($userData['error'])) {
				$back_url = $this->input->post('back_url');
				$data['loginErrorMsg'] = $userData['error'];
			}
		}	
		
		$data['username'] = $this->session->userdata('username');
		$data['back_url'] = $back_url;
		
		$this->load->view('admin/login', $data);
	}
	
} 
