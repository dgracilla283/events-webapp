<?php
require_once APPPATH . 'libraries/clients/api_role_client.php';
class Role extends CI_Model{
	
	private $objRoleClient; 
		
	public function __construct(){
		$this->objRoleClient = new api_role_client();		
	}
	
	public function getRoles(){
		$res = $this->objRoleClient->roles_get(false, 'json');
		return $res['result']; 
	}	
}