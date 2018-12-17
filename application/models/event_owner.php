<?php
require_once APPPATH . 'libraries/clients/api_event_owner_client.php';
class Event_Owner extends CI_Model{
	
	private $objEventOwnerClient;
		
	public function __construct(){
		$this->objEventOwner = new api_event_owner_client();
		if (! isset($this->Users)){
			$this->load->model('Users', '', TRUE);
		}
		$this->load->helper('cache');	
	}

	public function getEventOwners($params = false){
		$owners  = array();	
		$lastNames = array();
		$usersParams = array();
		
		$users = $this->Users->getUsers($usersParams);
		
		
		$res = $this->objEventOwner->event_owners_get($params, 'json');
		
		if(!empty($res['result'])) {
			foreach($res['result'] as $owner){
				$uid = $owner['user_id'];
				if(!empty($users[$uid])) {
					$ownerInfo = array_merge($owner,$users[$uid]);				 				 
					$owners[] = $ownerInfo;
					$lastNames[] = $users[$uid]['last_name'];  
				}
			}
		}		 		
	
		if(!empty($owners)) {
			array_multisort($lastNames, SORT_ASC, $owners);			 
		}
		return $owners;   
	}
	
	public function getCount($params = false){
		$count = 0; 
		$res = $this->objEventOwner->event_owners_get($params, 'json');
		if(!empty($res['result']))
			$count = count($res['result']); 
		return $count; 
	}
	
	
	public function addEventOwner($data = array()){		
		$res = $this->objEventOwner->event_owner_post($data, 'json');
		delete_all_cache();	
		return $res['result']; 
	}	
	
	public function deleteEventOwner($user_id, $event_id){
		delete_all_cache();
		return $this->objEventOwner->remove_event_owner_get(array('user_id' => $user_id,'event_id'=> $event_id), 'json');	
	}		
	
	
}