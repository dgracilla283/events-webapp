<?php
require_once APPPATH . 'libraries/clients/api_guest_client.php';
require_once APPPATH . 'libraries/clients/api_speaker_comment_client.php';
class Guest extends CI_Model{

	private $objGuestClient;
	private $objSpeakerCommentClient;
	private $_addOffset = '';

	public function __construct(){
		$this->objGuestClient = new api_guest_client();
		$this->objSpeakerCommentClient = new api_speaker_comment_client();
		if (! isset($this->Users)){
			$this->load->model('Users', '', TRUE);
		}
		if(!isset($this->User_Photo)) {
			$this->load->model('User_Photo', '', TRUE);
		}
		if(!isset($this->Companion)) {
			$this->load->model('Companion', '', TRUE);
		}
		$this->load->helper('cache');
		
		//$context = stream_context_create(array('http'=>array('ignore_errors'=>true)));
		//$file = file_get_contents("http://api.hostip.info/get_html.php?ip=".$_SERVER['REMOTE_ADDR']."", FALSE, $context);		
		//if (strpos($file, 'PHILIPPINES') !== false) {
			$this->_addOffset = '1';
		//} 
	}

	public function getAttendees($params = false){
		$attendees  = array();
		$lastNames = array();
		$usersParams = array();
		if (isset($params['is_primary'])){
			$usersParams = array('is_primary' => $params['is_primary']);
		}
		$users = $this->Users->getUsers($usersParams);

		$res = $this->objGuestClient->guests_get($params, 'json');

		if(!empty($res['result'])) {
			foreach($res['result'] as $attendee){
				$uid = $attendee['user_id'];
				if(!empty($users[$uid])) {
					$attendeeInfo = array_merge($attendee,$users[$uid]);
					$attendees[] = $attendeeInfo;
					$lastNames[] = $users[$uid]['last_name'];
				}
			}
		}
		/*
		if(!empty($attendees)) {
			array_multisort($lastNames, SORT_ASC, $attendees);
		}
		*/
		return $attendees;
	}

	public function getCount($params = false){
		$count = 0;
		$res = $this->objGuestClient->guests_get($params, 'json');
		if(!empty($res['result']))
			$count = count($res['result']);
		return $count;
	}

	public function getGuestItineraries($uid, $event_id = 0){
		$res = $this->objGuestClient->guests_get(array('user_id' => $uid, 'event_id' => $event_id), 'json');

		return isset( $res['result'] ) ? $res['result'] : array();
	}

	public function addEventGuest($data = array()){
		$res = $this->objGuestClient->guest_post($data, 'json');
		delete_all_cache();
		return $res['result'];
	}

	public function addEventSpeaker($data){
		delete_all_cache();
		return $this->objGuestClient->guest_post($data, 'json');
	}

	public function getEventSpeaker($iid){
		$res = $this->objGuestClient->guests_get(array('reference_id' => $iid, 'role_id' => 2), 'json');
		return $res['result'];
	}

	/**
	 * Get Event Speakers
	 * TODO: //This method is deprecated. Use getAttendees()
	 * @param int $iid (0 = return all event speakers else return itinerary speaker)
	 */
	public function getEventSpeakers($iid = 0, $eid = 0){
		$res = $this->objGuestClient->guests_get(array('event_id' => $eid, 'reference_id' => $iid, 'role_id' => 2, 'status' => 'approved'), 'json');
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
		$res = $this->objGuestClient->guests_get(false, 'json');
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
		$res = $this->objGuestClient->guests_get(false, 'json');
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
		$res = $this->objGuestClient->guests_get(array('reference_id' => $id), 'json');
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
		return $this->objGuestClient->remove_guest_get($options, 'json');
	}

	public function deleteGuestsByReferenceID($id){
		delete_all_cache();
		return $this->objGuestClient->remove_guest_by_referenceid_get(array('reference_id' => $id), 'json');
	}

	public function deleteEventGuest($user_id, $event_id){
		delete_all_cache();
		return $this->objGuestClient->remove_event_guest_get(array('user_id' => $user_id,'event_id'=> $event_id), 'json');
	}


	public function emailGuestsEventInvite($eventId)
	{
		$event = $this->Events->getEvent($eventId);
		if(isset($event['status']) && !$event['status']) {
			return array(
				'error' => 'You cannot send an email to the attendees if the event is not published yet'
			);
		}

		$this->load->library('email');
		$this->email->setConfigurations('event_invite');

		//Get Event Details and format date display
		$event = $this->Events->getEvent($eventId);
		if( isset($event['start_date_time']) && !empty($event['start_date_time']) ) {
			$event['start_date_time'] = $this->email->formatDisplayDate($event['start_date_time']);
		}

		if( isset($event['end_date_time']) && !empty($event['end_date_time']) ) {
			$event['end_date_time'] = $this->email->formatDisplayDate($event['end_date_time']);
		}

		//Get Event Attendees
		$eventAttendees = $this->getAttendees(array(
			'event_id' 	=> $eventId,
			'role_id' 	=> 3,
			'reference_type' => 'event',
			'reference_id'	=> $eventId
		));
		//dump($eventAttendees); exit;
		$eventAttendees = make_new_key($eventAttendees, 'user_id');
		//dump($eventAttendees); exit;
		//Iterate over attendees, then email them one by one
		$isSent = FALSE;
		foreach( $eventAttendees as $eventAttendee ) {
			if(!empty($eventAttendee['email'])) {
				$contentValues['user'] = $eventAttendee;
				$isSent = $this->email->renderTemplate(array(
								'event' => $event,
								'user'	=> $eventAttendee
							))
							//force to my email in the meantime.
							//->to('geraldine.esmilla@rcggs.com')
							//but use this in prod
							->to($eventAttendee['email'])
							->send();
				//TODO: mail debugger logger
				//force to email once only, for testing purposes, remove once in prod
				//break;
			}
		}

		return ($isSent)
			? array()
			: array('error' => 'Unable to send email as of the moment. Please try again later.');
	}

	public function getGuestProgramsList($userId, $eventId, $status = 'approved')
	{

		$itineraries = $this->objGuestClient->guests_get(array(
			'user_id' => $userId,
			'event_id' => $eventId,
			'status' => $status
		), 'json');

		$itinerarylist = array();
		if(!empty($itineraries['result'])){
			foreach($itineraries['result'] as $itinerary) {
				switch($itinerary['reference_type']) {
					case 'agenda':
						$itineraryDetails = $this->Itinerary->getItinerary($itinerary['reference_id']);
						$itinerarylist[] = $itineraryDetails;
						break;
					case 'activity':
						$breakoutDetails = $this->Breakout->getBreakout($itinerary['reference_id']);
						$itinerarylist[] = $breakoutDetails;
						break;
					case 'event':
					default:
						break;

				}
			}
		}
		return $itinerarylist;
	}


	public function isConcurrentActivity($userId, $itineraryId, $breakoutId)
	{
		$breakouts = $this->getAttendees(array(
			'user_id' => $userId,
			'reference_type' => 'activity'
		));

		foreach($breakouts as $breakout) {
			$breakoutInfo = $this->Breakout->getBreakout($breakout['reference_id']);
			if($breakoutInfo['itinerary_id'] == $itineraryId) {
				return true;
			}
		}
		return false;
	}


	/**
	 *
	 * Gets primary user's companions to the event
	 * @param int $primaryUserId
	 * @param int $eventId
	 * @return array list of companions with their user details
	 */
	public function getGuestCompanions($primaryUserId, $eventId, $status = 'approved')
	{
		$companions = make_new_key($this->Companion->getCompanions($primaryUserId), 'user_id');
		$eventAttendees = make_new_key($this->getAttendees(array(
			'event_id'	=> $eventId,
			'reference_type'	=> 'event',
			'reference_id'	=> $eventId,
			'status'	=> $status,
		)), 'user_id');
		//-- return only the same values
		return array_intersect_key($eventAttendees, $companions);
	}

	/**
	 *
	 * Gets user's events that the user is a part of
	 * @param int $userId
	 * @return array list of events
	 */
	public function getGuestEvents($userId)
	{
		
		$events = make_new_key($this->Events->getEvents(array('add_offset'=> $this->_addOffset, 'status' => '1',  'sort_field' => 'start_date_time', 'sort_order' => 'ASC', 'has_started' => 1, 'my_event' => 1)), 'eventID');
		$eventAttendees = make_new_key($this->getAttendees(array(
			'user_id' => $userId,
			'reference_type' => 'event',
			'status' => 'approved'
		)), 'event_id');

		return !empty($events) && !empty($eventAttendees)
			? array_intersect_key($events, $eventAttendees)
			: array();
	}

	/**
	 *
	 * Gets user's events that the user is a part of including past events
	 * @param int $userId
	 * @return array list of events
	 */
	public function getGuestAllEvents($userId)
	{		
		//only get events that are published, hence, the status = 1
		$events = make_new_key($this->Events->getEvents(array('add_offset'=> $this->_addOffset, 'sort_field' => 'start_date_time', 'sort_order' => 'ASC')), 'eventID');
		$eventAttendees = make_new_key($this->getAttendees(array(
				'user_id' => $userId,
				'reference_type' => 'event',
				'status' => 'approved'
		)), 'event_id');

		return !empty($events) && !empty($eventAttendees)
		? array_intersect_key($events, $eventAttendees)
		: array();
	}

	public function getGuestPendingEvents($userId)
	{		
		$events = make_new_key($this->Events->getEvents(array('add_offset'=> $this->_addOffset,'status' => 1, 'sort_field' => 'start_date_time', 'sort_order' => 'ASC', 'has_started' => 1)), 'eventID');
		$eventAttendees = make_new_key($this->getAttendees(array(
			'user_id' => $userId,
			'reference_type' => 'event',
			'status' => 'pending'
		)), 'event_id');

		return !empty($events) && !empty($eventAttendees)
			? array_intersect_key($events, $eventAttendees)
			: array();
	}

	/**
	 *
	 * Gets user's other events that the user is not an attendee of
	 * @param int $userId
	 * @return array list of events
	 */
	public function getOtherEvents($userId)
	{		
		//-- only get events that are published
		$events = make_new_key($this->Events->getEvents(array('add_offset'=> $this->_addOffset,'status' => 1, 'has_started' => 1, 'sort_field' => 'start_date_time', 'sort_order' => 'ASC')), 'eventID');
		$test = date();
		$eventAttendees = make_new_key($this->getAttendees(array(
			'user_id' => $userId,
			'reference_type' => 'event',
		)), 'event_id');


		foreach ($eventAttendees as $eventAttendee){
			if ('rejected' == $eventAttendee['status']){
				unset($eventAttendees[$eventAttendee['event_id']]);
			}
		}

		if(!empty($events) && !empty($eventAttendees)) {
			return array_diff_key($events, $eventAttendees);
		} elseif(!empty($events) && empty($eventAttendees)) {
			return $events;
		} else {
			return array();
		}
		/*
		return !empty($events) && !empty($eventAttendees)
			? array_diff_key($events, $eventAttendees)
			: array();*/
	}

	/**
	 *
	 * Gets user's past events that the user is a part of
	 * @param int $userId
	 * @return array list of events
	 */
	public function getGuestPastEvents($userId)
	{		
		//only get events that are published, hence, the status = 1
		$events = make_new_key($this->Events->getEvents(array('add_offset'=> $this->_addOffset, 'status'=>1, 'sort_field' => 'start_date_time', 'sort_order' => 'ASC', 'is_past_event' => 1)), 'eventID');

		$eventAttendees = make_new_key($this->getAttendees(array(
				'user_id' => $userId,
				'reference_type' => 'event',
				'status' => 'approved'
		)), 'event_id');

		return !empty($events) && !empty($eventAttendees)
		? array_intersect_key($events, $eventAttendees)
		: array();
	}

	public function getUserConcurrentActivities($userId, $startDate, $endDate)
	{
		$params = array('user_id' => $userId, 'start_date' => $startDate, 'end_date' => $endDate);
		$res = $this->objGuestClient->user_concurrent_activities_get($params, 'json');
		$guests = array();
		$totalItems = count($res)-1;
		if(!empty($res)){
			for($ctr=0; $ctr<= $totalItems; $ctr++){
				$guests[$res[$ctr]['user_id']][] = $res[$ctr];
				/*
				if(array_key_exists($res[$ctr]['user_id'], $guests)){
				$guests[$guest['user_id']][] = $guest;
				}
				*/
			}
		}
		return $res;
	}

	public function addSpeakerComment($data = array()){
		$res = $this->objSpeakerCommentClient->speaker_comment_post($data, 'json');
		delete_all_cache();
		return $res['result'];
	}

	public function join($data = array())
	{
		if (isset($data['user_id']) && !empty($data['user_id']) &&
			isset($data['reference_id']) && !empty($data['reference_id']) &&
			isset($data['reference_type']) && !empty($data['reference_type'])) {
			return $this->objGuestClient->guest_post($data, 'json');
		}
		return false;
	}

	public function cancelJoin($data = array())
	{
		if (isset($data['user_id']) && !empty($data['user_id']) &&
			isset($data['reference_id']) && !empty($data['reference_id']) &&
			isset($data['reference_type']) && !empty($data['reference_type'])) {
			return $this->objGuestClient->remove_guest_by_reference_get($data, 'json');
		}
		return false;
	}

	public function joinSpeaker($data = array())
	{
		if (isset($data['user_id']) && !empty($data['user_id']) &&
			isset($data['event_id']) && !empty($data['event_id']) &&
			isset($data['reference_type']) && !empty($data['reference_type']) &&
			isset($data['reference_id']) && !empty($data['reference_id'])) {
			$attendee = $this->getAttendees(array(
					'user_id' => $data['user_id'],
					'event_id' => $data['event_id'],
					'reference_id' => $data['reference_id'],
					'reference_type' => $data['reference_type'],
					'status' => 'pending',
					'role_id' => 2
			));
			$data['eventAttendeeID'] = $attendee[0]['eventAttendeeID'];
			return $this->objGuestClient->guest_post($data, 'json');
		}
		return false;
	}

	public function cancelJoinSpeaker($data = array())
	{
		if (isset($data['user_id']) && !empty($data['user_id']) &&
			isset($data['event_id']) && !empty($data['event_id']) &&
			isset($data['reference_type']) && !empty($data['reference_type']) &&
			isset($data['reference_id']) && !empty($data['reference_id'])) {
			$attendee = $this->getAttendees(array(
				'user_id' => $data['user_id'],
				'event_id' => $data['event_id'],
				'reference_id' => $data['reference_id'],
				'reference_type' => $data['reference_type'],
				'status' => 'pending',
				'role_id' => 2
			));
			$data['eventAttendeeID'] = $attendee[0]['eventAttendeeID'];
			$event_attendee_id = $data['eventAttendeeID'];
			if ($event_attendee_id){
					$this->addSpeakerComment(array(
						'event_attendee_id' => $event_attendee_id,
						'comment' => $data['comment']
				));
			}
			$data['isSpeaker'] = "1";
			return $this->objGuestClient->guest_post($data, 'json');
		}
		return false;
	}

	public function getGuestStatus($data = array())
	{
		if(isset($data['user_id']) && !empty($data['user_id']) &&
			isset($data['reference_id']) && !empty($data['reference_id']) &&
			isset($data['reference_type']) && !empty($data['reference_type']) &&
			isset($data['event_id']) && !empty($data['event_id'])) {
			$result = $this->objGuestClient->guests_get($data, 'json');

			return isset($result['result']) && !empty($result['result'])
				? $result['result']
				: array();
		}
		return false;

	}

	public function updateStatus($data){
		if(empty($data))
			return false;

		return $this->objGuestClient->multi_update_status_post($data, 'json');
	}

	public function sendSpeakerInviteEmail($userID, $eventID, $referenceID, $referenceType, $activationKeyData, $itineraryID){
		$this->load->helper('url');
		$this->load->library('email');
		$activationKeyData['url'] = base_url();
		$user = $this->Users->getUser($userID);
		$event = $this->Events->getEvent($eventID);
		if( isset($event['start_date_time']) && !empty($event['start_date_time']) ) {
			$event['start_date_time'] = $this->email->formatDisplayDate($event['start_date_time']);
		}
		if( isset($event['end_date_time']) && !empty($event['end_date_time']) ) {
			$event['end_date_time'] = $this->email->formatDisplayDate($event['end_date_time']);
		}
		$this->email->setConfigurations('speaker_event_invite')
		->renderTemplate(array(
				'user' => $user,
				'event' => $event,
				'event_id' => $eventID,
				'reference_id' => $referenceID,
				'reference_type' => $referenceType,
				'activationKeyData' => $activationKeyData,
				'itinerary_id' => $itineraryID))
				->to($user['email'])
				->send();
	}

	public function getPendingItinerary($userId, $eventId, $referenceId, $referenceType, $itineraryId, $role = 2){
		if ('agenda' == $referenceType){
			$itineraries = make_new_key($this->Itinerary->getEventItineraries($eventId), 'itineraryID');
		}else if ('activity' == $referenceType){
			$itineraries = make_new_key($this->Breakout->getItineraryBreakouts($itineraryId), 'breakoutID');
		}

		$eventAttendees = make_new_key($this->getAttendees(array(
				'user_id' => $userId,
				'reference_type' => $referenceType,
				'status' => 'pending',
				'role_id' => $role
		)), 'reference_id');

		return (!empty($itineraries) && !empty($eventAttendees)) ? array_intersect_key($itineraries, $eventAttendees) : array();
	}
}