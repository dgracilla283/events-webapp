<?php
require_once APPPATH . 'libraries/clients/api_user_client.php';
class Users extends CI_Model{

	private $objUserClient;

	public function __construct(){
		$this->objUserClient = new api_user_client();
		$this->load->helper('cache');
	}

	public function getUsers($data = array()){
		$res = $this->objUserClient->users_get($data, 'json');

		$users = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $user){
				$users[$user['userID']] = 	$user;
			}
		}
		// attach photo
		$userIDs = array_keys($users);
		$userIDs = implode(',', $userIDs);
		$photoData = $this->User_Photo->fetch_multi_by_userid($userIDs);
		$photoData = make_new_key($photoData, 'fk_i_uid');
		foreach($users as &$user) {
			if (!empty($photoData[$user['userID']])) {
				$user['uploaded_photo'] = $photoData[$user['userID']];
			}
		}
		return $users;
	}

	public function getNumUsers($options){
		$res = $this->objUserClient->num_users_get($options, 'json');
		return $res['result'];
	}

	public function getUser($id){
		$res = $this->objUserClient->user_get(array('id' => $id), 'json');
		return $res['result'];
	}

	public function addUser($data = array()) {
		if( empty($data) ) {
			return false;
		}
		$res = $this->objUserClient->user_post($data, 'json');
		delete_all_cache();
		return $res;
	}

	public function deleteUser($id){
		delete_all_cache();
		return $this->objUserClient->remove_user_get(array('userID' => $id), 'json');
	}

	public function deleteGuestUser($id){
		delete_all_cache();
		return $this->objUserClient->remove_guest_user_get(array('userID' => $id), 'json');
	}

	public function sendForgetPasswordEmail($userID, $activationKeyData){
		$this->load->helper('url');
		$activationKeyData['url'] = base_url();
		$this->load->library('email');
		$user = $this->getUser($userID);
                $this->email
		->setConfigurations('forgot_password')
		->renderTemplate(array('user' => $user, 'activationKeyData' => $activationKeyData))
		->to($user['email'])
		->send();
	}

	public function sendActivateAccountEmail($userID, $activationKeyData){
		$this->load->helper('url');
		$activationKeyData['url'] = base_url();
		$this->load->library('email');
		$user = $this->getUser($userID);
		$this->email
		->setConfigurations('activate_account')
		->renderTemplate(array('user' => $user, 'activationKeyData' => $activationKeyData))
		->to($user['email'])
		->send();
	}

	public function getFullUserInfo($userId) {
		$userInfo = $this->getUser($userId);
		$dataUserPhoto = array();
		if ($this->config->item('is_allow_user_photo')) {
			$dataUserPhoto = $this->User_Photo->fetch(array(
				'fk_i_uid' => $userInfo['userID']
			));
			if (!empty($dataUserPhoto)) {
				$dataUserPhoto = current($dataUserPhoto);
			}
		}
		//dump($userInfo); exit;
		$userInfo['uploaded_photo'] = $dataUserPhoto;
		//dump($userId);

		$userInfo['primary_user'] = $this->Companion->getPrimaryUserInfo($userId);
		//dump($userInfo); exit;
		return $userInfo;
	}
	
	public function sendJoinNotificationToOwners($owner, $registrant, $event){
		$this->load->helper('url');
		$app['name'] = 'RCG Event Planner';
		$app['url'] = base_url("admin/manage_requests");
		$this->load->library('email');
		$this->email
		->setConfigurations('join_notification')
		->renderTemplate(array(
			'owner' => $owner, 
			'registrant' => $registrant, 
			'app' => $app,
			'event' => $event
		))
		->to($owner['email'])
		->send(); 
	}
}