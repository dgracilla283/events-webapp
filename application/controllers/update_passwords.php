<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'libraries/clients/api_user_client.php';
require_once APPPATH . 'libraries/clients/api_admin_client.php';

class Update_Passwords extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$objUserClient = new api_user_client(); 
		$objAdminClient = new api_admin_client();
		
		// Default Passwords
		$adminPassword = 'rcgeventsadmin'; 
		$userPassword = 'rcgevents2013';

		$admins = $objAdminClient->admins_get();
		
		$admins = make_new_key($admins['result'], 'user_id'); 

		$users = $objUserClient->users_get();
		 
		foreach($users['result'] as $user) {
			$params = array(
				'userID' => $user['userID'], 
				'email' => $user['email'], 
				'first_name' => $user['first_name'], 
				'last_name' =>  $user['last_name'],
				'affiliation' =>  $user['affiliation'],
				'industry' =>  $user['industry'],
				'title' =>  $user['title'],
				'bio' =>  $user['bio'], 
				'is_primary' =>  $user['is_primary']
			);
						
			if(array_key_exists($user['userID'], $admins)) {
				$params['password'] = $adminPassword; 
			}else {
				$params['password'] = $userPassword;
			}
			if	(1 == $user['is_primary']){
				$post = $objUserClient->user_post($params);
			}	  
		}
		
		echo 'Done ...';  
		exit; 
	}	
}