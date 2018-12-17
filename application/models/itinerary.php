<?php
require_once APPPATH . 'libraries/clients/api_itinerary_client.php';

class Itinerary extends CI_Model{

	private $objItineraryClient;

	public function __construct(){
		if (! isset($this->Guest)){
			$this->load->model('Guest', '', TRUE);
		}
		if (! isset($this->Role)){
			$this->load->model('Role', '', TRUE);
		}
		if (! isset($this->Breakout)){
			$this->load->model('Breakout', '', TRUE);
		}
		$this->objItineraryClient = new api_itinerary_client();
		$this->load->helper('cache');
	}

	public function getAllItineraries(){
		$res = $this->objItineraryClient->itineraries_get();
		return $res['result'];
	}

	public function getEventItineraries($eid){
		$res = $this->objItineraryClient->event_itineraries_get(array('event_id' => $eid), 'json');
		return $res['result'];
	}

	public function getEventItinerariesGroupByDate($eid){
		$res = $this->objItineraryClient->event_itineraries_get(array('event_id' => $eid), 'json');
		$itineraries = array();
		if(!empty($res['result'])) {
			foreach($res['result'] as $key => $value) {
				$dateStart = date('Y/m/d', strtotime(str_replace('-','/',$value['start_date_time'])));
				$itineraries[$dateStart][] = $value;
			}
		}
		return $itineraries;
	}

	public function getItinerary($id){
		if( empty($id) ) {
			return false;
		}
		$res = $this->objItineraryClient->itinerary_get(array('id' => $id), 'json');
		return $res['result'];
	}

	public function deleteItinerary($params){
		if( empty($params) ) {
			return false;
		}
		$res = $this->objItineraryClient->itinerary_remove_get($params, 'json');
		delete_all_cache();
		return $res;
	}

	public function addItinerary($data = array()) {

		if( empty($data) ) {
			return false;
		}

		$data['start_time'] = isset($data['start_time']) ? $data['start_time'] : '00:00:00';
		$data['end_time'] = isset($data['end_time']) ? $data['end_time'] : '00:00:00';
		$data['start_date_time'] = date('Y-m-d H:i', strtotime(str_replace('-', '/',$data['start_date_time']).' '.$data['start_time']));
		$data['end_date_time'] = date('Y-m-d H:i', strtotime(str_replace('-', '/',$data['end_date_time']).' '.$data['end_time']));

		// if breakout status updated delete current breakouts under itinerary
		if(!empty($data['itineraryID'])) {
			$deleteBreakout = false;
			$oldItineraryInfo = $this->getItinerary($data['itineraryID']);
			if(!empty($data['breakout_status'])) {
				if($data['breakout_status'] != $oldItineraryInfo['breakout_status'])
					$deleteBreakout = true;
			}else {
				if( $oldItineraryInfo['breakout_status'] == 1)
					$deleteBreakout = true;
			}
			if($deleteBreakout){
				$breakouts = $this->Breakout->getItineraryBreakouts($data['itineraryID']);
				if(!empty($breakouts)){
					foreach($breakouts as $breakout) {
						$this->Breakout->deleteBreakout($breakout['breakoutID']);
					}
				}
			}
		}

		// save itinerary
		$res = $this->objItineraryClient->itinerary_post($data, 'json');
		$speakerRoleID = 2;

		if(!empty($res['result']) && !empty($data['user_id'])) {
			$param = array(
					'eventAttendeeID' => null,
					'event_id' =>  $data['event_id'],
					'reference_type' => 'agenda',
					'reference_id' => $res['result']['itineraryID'],
					'role_id' => $speakerRoleID
			);
			$this->Guest->deleteGuest($param);
			foreach($data['user_id'] as $speaker) {
				$param['user_id'] = $speaker;
				$param['status'] = 'approved';
				$this->Guest->addEventGuest($param);
			}
		}
		$param = array(
				'eventAttendeeID' => null,
				'event_id' =>  $data['event_id'],
				'reference_type' => 'agenda',
				'reference_id' => $res['result']['itineraryID'],
				'role_id' => 4
		);
		$this->Guest->deleteGuest($param);

		if(!empty($data['team_members'])) {
			foreach($data['team_members'] as $key => $value) {
				$guestData = array(
					'user_id' => $value,
					'event_id' => $data['event_id'],
					'reference_id' => $res['result']['itineraryID'],
					'reference_type' => 'agenda',
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

}