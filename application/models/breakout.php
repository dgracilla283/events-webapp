<?php
require_once APPPATH . 'libraries/clients/api_breakout_client.php';
class Breakout extends CI_Model{

	private $objBreakoutClient;

	public function __construct(){
		$this->load->helper('cache');
		$this->objBreakoutClient = new api_breakout_client();
		if (! isset($this->Guest)){
			$this->load->model('Guest', '', TRUE);
		}
		if (! isset($this->Role)){
			$this->load->model('Role', '', TRUE);
		}
	}

	public function getAllBreakouts(){
		$res = $this->objBreakoutClient->breakouts_get();
		return $res['result'];
	}

	public function getItineraryBreakouts($iid){
		$res = $this->objBreakoutClient->breakouts_get(array('itinerary_id' => $iid), 'json');
		$breakouts = array();
		if(isset($res['result']) && !empty($res['result'])) {
			$breakouts = make_new_key($res['result'], 'breakoutID');

			foreach ($breakouts as $breakout) {
				$breakouts[$breakout['breakoutID']]['teams'] = $this->Guest->getGuestTeams($breakout['breakoutID']);
			}
		}
		return $breakouts;
	}

	public function getBreakout($bid){
		$res = $this->objBreakoutClient->breakout_get(array('id' => $bid), 'json');
		return $res['result'];
	}

	public function saveBreakout($data){
		if( empty($data) ) {
			return false;
		}

		$res = $this->objBreakoutClient->breakout_post($data, 'json');
		// remove breakout guest first

		$init = $init['speakers']['existing'] = $init['speakers']['deleted'] = array();

		$breakoutID = $res['result']['breakoutID'];
		if (!empty($data['breakoutID'])) {
			if (!empty($data['user_id'])) {
				$eventAttendee = $this->Guest->getEventSpeaker($data['breakoutID']);
				foreach ($eventAttendee as $attendeeDetails) {
					if (in_array($attendeeDetails['user_id'], $data['user_id'])) {
						switch ($attendeeDetails['status']) {
							case 'pending':
								$init['speakers']['deleted'][] = $attendeeDetails['user_id'];
								$this->Guest->deleteGuest(array(
									'reference_id' => $data['breakoutID'],
									'event_id'     => $data['event_id'],
									'role_id'      => 2,
									'user_id'      => $attendeeDetails['user_id']
								));
								break;
							default:
								$init['speakers']['existing'][] = $attendeeDetails['user_id'];
								break;
						}
					} else {
						$this->Guest->deleteGuest(array(
							'reference_id' => $data['breakoutID'],
							'event_id'     => $data['event_id'],
							'role_id'      => 2,
							'user_id'      => $attendeeDetails['user_id']
						));
					}
				}
			}

			$this->Guest->deleteGuest(array(
				'reference_id' => $data['breakoutID'],
				'event_id'     => $data['event_id'],
				'role_id'      => 4,
				'user_id'      => $user
			));
			$breakoutID = $data['breakoutID'];
		}

		if(!empty($data['user_id'])){
			$newData = array();
			if (isset($init['speakers'])) {
				if (empty($init['speakers']['existing'])) $init['speakers']['existing'] = array();
				if (empty($init['speakers']['deleted'])) $init['speakers']['deleted'] = array();

				$newData['users'] = array_diff($data['user_id'], $init['speakers']['existing']);
				$newData['users'] = array_merge($newData['users'], $init['speakers']['deleted']);
				$newData['users'] = array_unique($newData['users']);
			} else {
				$newData['users'] = $data['user_id'];
			}

			$postData = array();
			$userRoles = $this->Role->getRoles();
			$speaker_id = 0;
			if(!empty($userRoles)) {
				foreach($userRoles as $role){
					if('speaker' == strtolower($role['title']))
						$speaker_id = $role['roleID'];
				}
			}
			foreach($newData['users'] as $speaker) {
				if($speaker != '' && $speaker){
					$postData = array(
						'event_id' => $data['event_id'],
						'user_id' => $speaker,
						'reference_id' => $breakoutID,
					 	'reference_type' => 'activity',
						'role_id' => $speaker_id,
						'team' => '',
						'status' => 'approved',
						);
					$this->Guest->addEventSpeaker($postData);
				}
			}
		}
		// save team
		if(!empty($data['team_members'])) {
			foreach($data['team_members'] as $key => $value) {
				$guestData = array(
					'user_id' => $value,
					'event_id' => $data['event_id'],
					'reference_id' => $breakoutID,
					'reference_type' => 'activity',
					'role_id' => 4,
					'team' => $data['team_name'][$key],
					'status' => 'approved'
				);
				$this->Guest->addEventGuest($guestData);
			}
		}
		delete_all_cache();
		return $res['result'];
	}

	public function deleteBreakout ($bid){
		delete_all_cache();
		return $this->objBreakoutClient->remove_breakout_get(array('bid' => $bid), 'json');
	}

	public function getBreakoutInformation($itineraryId) {
		$breakouts = $this->getItineraryBreakouts($itineraryId);

		$breakoutInfo = array();
		$hasTeam = false;
		$hasAttendee = false;

		if(1 == count($breakouts)) {
			$breakout = current($breakouts);
			$breakoutDetails = $this->Breakout->getBreakout($breakout['breakoutID']);
			$breakoutInfoGuests = $this->Guest->getGuestByReferenceID($breakout['breakoutID']);
			if(!empty($breakoutInfoGuests)) {
				foreach($breakoutInfoGuests as $breakoutInfoGuest) {
					if(!empty($breakoutInfoGuest['team'])){
						$hasTeam = true;
					}
					if(3 == $breakoutInfoGuest['role_id']){
						$hasAttendee = true;
					}
				}
			}
			$breakoutInfo = array(
				$breakoutDetails,
				'hasTeam' => $hasTeam,
				'hasAttendee' => $hasAttendee,
				'speakers' => array()
			);

		}

	}

}