<?php
require_once APPPATH . 'libraries/clients/api_companion_client.php';
class Companion extends CI_Model{

	private $objCompanionClient;

	public function __construct(){
		$this->objCompanionClient = new api_companion_client();
		if (! isset($this->Users)){
			$this->load->model('Users', '', TRUE);
		}
		if(!isset($this->User_Photo)) {
			$this->load->model('User_Photo', '', TRUE);
		}
		$this->load->helper('cache');
	}

	public function getPrimaryUser($id){
		$res = $this->objCompanionClient->companion_primary_user_get(array('user_id' => $id), 'json');
		return $res['result'];
	}

	public function getGuestItineraries($uid, $event_id = 0){
		$res = $this->objCompanionClient->guests_get(array('userID' => $uid, 'event_id' => $event_id), 'json');
		return $res['result'];
	}

	public function addEventGuests($data){
		if(empty($data)){
			return false;
		}
		$postData = $res = array();
		foreach($data['program_id'] as $program => $program_id) {
			$postData = array(
				'event_id' => $data['event_id'],
				'user_id' => $data['user_id'],
				'reference_id' => $program_id,
			 	'reference_type' =>  $data['reference_type'][$program_id],
				'role_id' => $data['role_id'][$program_id]
				);
			$res[] = $this->objCompanionClient->guest_post($postData, 'json');
		}
		delete_all_cache();
		return $res;
	}

	public function addEventGuest($data = array()){
		$res = $this->objCompanionClient->guest_post($data, 'json');
		delete_all_cache();
		return $res['result'];
	}

	public function addEventSpeaker($data){
		delete_all_cache();
		return $this->objCompanionClient->guest_post($data, 'json');
	}

	public function getEventSpeaker($iid){
		$res = $this->objCompanionClient->guests_get(array('reference_id' => $iid, 'role_id' => 2), 'json');
		return $res['result'];
	}

	/**
	 * Get Event Speakers
	 * @param int $iid (0 = return all event speakers else return itinerary speaker)
	 */
	public function getEventSpeakers($iid = 0, $eid = 0){
		$res = $this->objCompanionClient->guests_get(array('event_id' => $eid, 'reference_id' => $iid, 'role_id' => 2), 'json');
		$users = $this->Users->getUsers();
		$eventSpeakers = array();
		$speakerIds = array();
		if(!empty($res['result'])){
			if($iid) {
				$eventSpeakers = $res['result'];
			}else {
				$lastName = array();
				foreach($res['result'] as $guest){
					$uid = $guest['user_id'];
					if(!$uid)
						continue;
					if(!in_array($users[$uid]['last_name'], $eventSpeakers)){
						$eventSpeakers[$uid] = array(
							'user_id' => $uid,
							'first_name' => $users[$uid]['first_name'],
							'last_name' => $users[$uid]['last_name'],
							'title' => $users[$uid]['title'],
							'uploaded_photo' => !empty($users[$uid]['uploaded_photo']) ? $users[$uid]['uploaded_photo'] : null
						);
					}
					$lastName[$uid] = $users[$uid]['last_name'];
				}
				if(!empty($eventSpeakers)) {
					array_multisort($lastName, SORT_ASC, $eventSpeakers);
				}
			}
		}
		return $eventSpeakers;
	}

	public function getGuestGroupByTeams($iid){
		$res = $this->objCompanionClient->guests_get(false, 'json');
		$teams = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $guest){
				if($guest['reference_id'] == $iid && $guest['team']){
					$teams[trim($guest['team'])][] = $guest;
				}
			}
		}
		return $teams;
	}

	public function getGuestTeams($iid){
		$res = $this->objCompanionClient->guests_get(false, 'json');
		$teams = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $guest){
				if($guest['reference_id'] == $iid && $guest['team']){
					$teams[] = $guest['team'];
				}
			}
		}
		return array_unique($teams);
	}

	public function getGuestByReferenceID($id){
		$res = $this->objCompanionClient->guests_get(array('reference_id' => $id), 'json');
		$guests = array();
		if(!empty($res['result'])){
			foreach($res['result'] as $guest){
				$guests[$guest['user_id']] = $guest;
			}
		}
		return $guests;
	}



	public function deleteGuest($options){
		delete_all_cache();
		return $this->objCompanionClient->remove_guest_get($options, 'json');
	}

	public function deleteGuestsByReferenceID($id){
		delete_all_cache();
		return $this->objCompanionClient->remove_guest_by_referenceid_get(array('reference_id' => $id), 'json');
	}

	public function deleteEventGuest($user_id, $event_id){
		delete_all_cache();
		return $this->objCompanionClient->remove_event_guest_get(array('user_id' => $user_id,'event_id'=> $event_id), 'json');
	}

	public function getCompanions($userId = 0, $type = '')
	{
		$res = $this->objCompanionClient->companions_get(array('primary_user_id' => $userId, 'type' => $type));

		return isset( $res['result'] ) ? $res['result'] : array();
	}

	public function getCompanion($userId)
	{
		$res = $this->objCompanionClient->companions_get(array('user_id' => $userId));

		return isset( $res['result'] ) ? $res['result'] : array();
	}

	public function getPrimaryUserInfo($userId)
	{
		$primaryUser = $this->getPrimaryUser($userId);
		if(!empty($primaryUser)) {
			$primaryUser = current($primaryUser);
			return $this->Users->getUser($primaryUser['primary_user_id']);
		}
		return array();
	}
}