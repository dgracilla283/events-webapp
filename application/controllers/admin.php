<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'libraries/Auth_Controller.php';

class Admin extends Auth_Controller {

	/**
	 * RCG Event Planner CMS
	 */
	private $data = array();
	private $roles = array();
	private $_mapLocation = '';

	public function __construct()
	{
		parent::__construct();
		$this->_mapLocation = $this->config->item('upload_path') . 'map/';

		$this->data['lastExpandID'] = 0;
		$this->data['method'] = $this->uri->segment(2);
		$roles = $this->Role->getRoles();
		if($roles){
			foreach($roles as $role){
				$this->roles[strtolower($role['title'])] = $role['roleID'];
			}
		}
	}
	/**
	 * Index Page
	 * Used to redirect users to login page or dashboard
	 */
	public function index()
	{
		if (empty($this->userData)) {
			redirect('login/index');
		} else {
			redirect('/admin/dashboard');
		}
	}

	/**
	 * Dashboard
	 * List of Events
	 */

	public function dashboard() {
		$this->data['method'] = 'dashboard';
		$this->load->view('admin/dashboard.php', $this->data);
	}

	public function get_events_published() {
		$events = $this->Events->getEvents(array('status' => TRUE, 'is_current_event' => TRUE));

		foreach($events as &$event){
			$event['pending_requests'] = $this->Guest->getAttendees(array(
				'event_id' => $event['eventID'],
				'status' => 'pending',
				'role_id' => $this->roles['attendees']
			));
		}
		$this->data['events'] = $events;
		$this->data['method'] = 'dashboard';
		
		$this->load->view('admin/_table_events.php', $this->data);
	}

	public function get_events_completed() {
		$events = $this->Events->getEvents(array('status' => TRUE, 'is_past_event' => TRUE));
	
		foreach($events as &$event){
			$event['pending_requests'] = $this->Guest->getAttendees(array(
					'event_id' => $event['eventID'],
					'status' => 'pending',
					'role_id' => $this->roles['attendees']
			));
		}
		$this->data['events'] = $events;
		$this->data['method'] = 'dashboard';
		$this->load->view('admin/_table_events.php', $this->data);
	}

	public function get_events_unpublished() {
		$events = $this->Events->getEvents(array('status' => '0'));

		foreach($events as &$event){
			$event['pending_requests'] = $this->Guest->getAttendees(array(
				'event_id' => $event['eventID'],
				'status' => 'pending',
				'role_id' => $this->roles['attendees']
			));
		}
		$this->data['events'] = $events;
		$this->data['method'] = 'dashboard';
		$this->load->view('admin/_table_events.php', $this->data);
	}

	/**
	 * Add Event
	 */
	public function add_event() {
		$postdata = $this->input->post();
		$event = array();
		if(!empty($postdata)){
			$event = $this->Events->addEvent($postdata);
			if(!empty($event)) {
				//add event owner
				$eventOwnerParams = array();
				$eventOwnerParams['event_id'] = $event['eventID'];
				$eventOwnerParams['user_id'] = $this->userData['userID'];
				$this->Event_Owner->addEventOwner($eventOwnerParams);
				redirect('/admin/view_event?id='.$event['eventID'].'&add=1');
			}
		}
		$this->data['method'] = 'add_event';
		$this->load->view('admin/edit_event.php',$this->data);
	}

	/**
	 * Edit Event
	 */

	public function edit_event() {
		$eventId = $this->input->get('id');
		$this->data['event'] = $this->Events->getEvent($eventId);
		$postData = $this->input->post();

		$this->_uploadMapPhoto($postData);

		if(!empty($postData)){
			$this->Events->addEvent($postData);
			$this->data['edited'] = true;
		}

		$this->data['mapPhotos'] = $this->Map_Photo->getPhoto($eventId);
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->data['method'] = 'edit_event';
		$this->load->view('admin/edit_event.php',$this->data);
	}

	/**
	 * _doUploadMapPhoto
	 * Upload map photo
	 * @param $postData array
	 * @param $eventId int/string
	 */
	private function _doUploadMapPhoto($postData, $eventId) {
		if(isset($_FILES['new_map_photo']) && !empty($_FILES['new_map_photo'])) {
			$tmpFiles =  $_FILES['new_map_photo'];
			$ctr = 0;

			for($ctr = 0; $ctr < count($tmpFiles['tmp_name']); $ctr++) {
				//-- Reassigning it back since Upload library is yielding an error in multiple uploads
				$_FILES['new_map_photo'] = array(
					'name' => $tmpFiles['name'][$ctr],
					'type' => $tmpFiles['type'][$ctr],
					'tmp_name' => $tmpFiles['tmp_name'][$ctr],
					'error' => $tmpFiles['error'][$ctr],
					'size'	=> $tmpFiles['size'][$ctr]
				);
				$conf = array(
					'upload_path' 	=> $this->Map_Photo->getUploadPath(),
					'allowed_types' => 'gif|jpg|jpeg|png',
					'max_size' 		=> 2048
				);

				$this->load->library('upload', $conf);
				$conf['file_name'] = $tmpFiles['name'][$ctr]; //str_replace(' ', '-', $postData['new_map_photo_title'][$ctr]) . '-' . time() . '.jpg';
				$this->upload->initialize($conf);

				if (!$this->upload->do_upload('new_map_photo')) {
					$error = array('error' => $this->upload->display_errors());
					$data['error'] = current($error);
				} else {
					$data = array('uploaded_data' => $this->upload->data());
					$paramsPhoto = array(
						's_fname'	=> $tmpFiles['name'][$ctr], //$conf['file_name'],
						'title'		=> $postData['new_map_photo_title'][$ctr],
						'event_id' 	=> (empty($eventId)) ? $postData['eventID'] : $eventId,
						's_origdata' => serialize(getimagesize($tmpFiles['tmp_name'][$ctr])),
					);

					$dataPhoto = $this->Map_Photo->addPhoto($paramsPhoto);
					$this->data['db_inserted_photo'] = $dataPhoto;
				}
			}
		}
	}

	/**
	 * Upload map photo
	 * @param $postData array
	 * @param $eventId int/string
	 */
	private function _uploadMapPhoto($postData, $eventId = '') {
		if(!empty($postData)){
			if (!empty($eventId)) {
				$this->_doDuplicateMapPhoto($postData, $eventId);
			} else {
				$this->_doDeleteMapPhotos($mapPhotosDelete);
			}
			$this->_doUploadMapPhoto($postData, $eventId);
		}
	}

	/**
	 * _doDuplicateMapPhoto
	 * Duplicate map photos
	 * @param $postData array
	 */
	private function _doDuplicateMapPhoto($postData, $eventId) {
		$mapPhotosDelete = (!isset($postData['map_photo_item_remove']) || empty($postData['map_photo_item_remove'])) ? array() : $postData['map_photo_item_remove'];
		$mapPhotos       = $this->Map_Photo->getPhoto($postData['currentEventID']);

		if (!empty($mapPhotos)) {
			foreach ($mapPhotos as $key => $value) {
				if (!in_array($value['mapPhotoID'], $mapPhotosDelete)) {
					$file    = $this->_mapLocation . $value['s_fname'];
					$newfile = $this->_mapLocation . $eventId . '-' .$value['s_fname'];

					if (!copy($file, $newfile)) {
						echo "Failed to duplicate event";
						die;
					} else {
						$paramsPhoto = array(
							's_fname'    => $eventId . '-' .$value['s_fname'],
							'title'      => $value['title'],
							'event_id'   => $eventId,
							's_origdata' => $value['s_origdata']
						);

						$dataPhoto = $this->Map_Photo->addPhoto($paramsPhoto);
					}
				}
			}
		}
	}

	/**
	 * _doDeleteMapPhotos
	 * Check if there are map photos to be deleted. If yes, delete.
	 * @param $postData array
	 */
	private function _doDeleteMapPhotos($postData) {
		$mapPhotosDelete = $postData['map_photo_item_remove'];

		// Check if a remove map photo is marked checked
		if (!empty($mapPhotosDelete)) {
			// Iterate and Remove photo
			foreach($mapPhotosDelete as $mapPhotoId) {
				$this->_executeDeleteMapPhoto($mapPhotoId);
			}
		}
	}

	/**
	 * View Event
	 */
	public function view_event() {
		$eventId = $this->input->get('id');

		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'reference_type' => 'event',
			'status' => 'approved'
			));

			$eventAttendees = make_new_key($eventAttendees, 'user_id');
			$childrenCount = 0;
			$childrenAttendee = $this->Companion->getCompanions(0, 'child');
			foreach($childrenAttendee as $childCompanion){
				if(array_key_exists($childCompanion['user_id'], $eventAttendees))
				$childrenCount++;
			}
			$this->data['event'] = $this->Events->getEvent($eventId);
			$this->data['eventId'] = $eventId;
			$this->data['users'] = $eventAttendees;
			$this->data['childrenCount'] = $childrenCount;
			$this->data['method'] = 'view_event';
			$this->load->view('admin/view_event.php',$this->data);
	}

	/**
	 * Delete Event
	 */
	public function delete_event() {
		$eventId = $this->input->get('id');
		$this->Events->deleteEvent($eventId);

		$mapPhotos = $this->Map_Photo->getPhoto($eventId);
		foreach ($mapPhotos as $value) {
			$mapPhotoId = $value['mapPhotoID'];
			$this->_executeDeleteMapPhoto($mapPhotoId);
		}

		redirect('/admin/dashboard');
	}

	/**
	 * _executeDeleteMapPhoto
	 * @param $mapPhotoId int/string
	 */
	private function _executeDeleteMapPhoto($mapPhotoId) {
		$mapPhotoInfo = make_new_key($this->Map_Photo->fetch(array('mapPhotoID' => $mapPhotoId)), 'mapPhotoID');
		$result = $this->Map_Photo->deletePhoto($mapPhotoId);
		if($result) {
			$filename = $this->_mapLocation . $mapPhotoInfo[$mapPhotoId]['s_fname'];
			if(file_exists($filename)) {
				unlink($filename);
			}
		}
	}

	public function event_title_check_duplicate($eventTitle) {
		$byEvents = $this->Events->getEvents(array('title' => $eventTitle));
		if (!empty($byEvents)) {
			$this->form_validation->set_message('event_title_check_duplicate', 'Event title already exists.');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Duplicate Event
	 */
	private function _duplicate_event() {
		$this->load->library('form_validation');

		$response = array();
		$postData = $this->input->post();

		$eventID  = $postData['eventID'];
		$byEvents = $this->Events->getEvents(array('title' => $postData['title']));
		$isExists = (!empty($byEvents)) ? TRUE : FALSE;

		$this->form_validation->set_rules('title', 'Event title', 'trim|required|callback_event_title_check_duplicate');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$this->form_validation->set_rules('location', 'Event location', 'trim|required');
		$this->form_validation->set_rules('additional_info', 'Additonal Info (dress code, logistics, requirements, etc.)');
		$this->form_validation->set_rules('attendees_limit', 'Attendees Limit', 'trim');
		$this->form_validation->set_rules('start_date_time', 'Event start date', 'trim|required');
		$this->form_validation->set_rules('start_time', 'Event start date', 'trim|required');
		$this->form_validation->set_rules('end_date_time', 'Event end date', 'trim|required');
		$this->form_validation->set_rules('end_time', 'Event end time', 'trim|required');

		$ctr = 1;
		foreach ($postData as $key => $value) {
			if (strpos($key,'itinerary_date_') !== false) {
				$this->form_validation->set_rules($key, 'Event Day ' . $ctr, 'trim|required');
				$ctr++;
			}
		}

		if ($this->form_validation->run() !== FALSE) {
			//duplicate event
			$postData['eventID'] = '';
			$event = $this->Events->addEvent($postData);
			$itineraries = $this->Itinerary->getEventItineraries($eventID);

			foreach ($itineraries as $itinerary) {
				$eventPreferences = $this->Activity_Preference->getActivityPreference(array(
					'activityPreferenceID' => null,
					'referenceID' => $itinerary['itineraryID'],
					'referenceType' => null,
					'eventID'  => $eventID,
				));

				$itinerary['itineraryID'] = '';
				$itinerary['event_id'] = $event['eventID'];
				$date_start_data = explode(" ", $itinerary['start_date_time']);
				$date_end_data = explode(" ", $itinerary['end_date_time']);

				// adjust new itinerary date
				$itinerary['start_date_time'] = $postData['itinerary_date_'.$date_start_data[0]];
				$itinerary['start_time'] = $date_start_data[1];
				$itinerary['end_date_time'] = $postData['itinerary_date_'.$date_start_data[0]];
				$itinerary['end_time'] = $date_end_data[1];
				$ret = $this->Itinerary->addItinerary($itinerary);

				// duplicate preferences and preferences options
				if (is_array($eventPreferences) && count($eventPreferences) > 0 ) {
					foreach ($eventPreferences as $eventPreference){
						$eventPreference['activityPreferenceID'] = '';
						$eventPreference['referenceID'] = $ret['itineraryID'];
						$eventPreference['eventID'] = $event['eventID'];
						$eventPreference['dateCreated'] = '';
						$eventPreference['dateUpdated'] = '';
						$return = $this->Activity_Preference->saveActivityPreference($eventPreference);
						$newActivityPreferenceID = $return[0]['activityPreferenceID'];
						if (is_array($eventPreference['options']) && count($eventPreference['options']) > 0 ) {
							$optionData = array();
							$optionData['activityPreferenceID']	= $newActivityPreferenceID;
							foreach ($eventPreference['options'] as $option){
								$optionData['options'][] = $option['title'];
							}
							$this->Activity_Preference_Option->saveActivityPreferenceOption($optionData);
						}
					}
				}
			}

			$newlyAddedEvent = $this->Events->getEvents(array('title' => $postData['title']));
			$this->_uploadMapPhoto($postData, $newlyAddedEvent[0]['eventID']);
		}

		//copy attendess
		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventID,
			'reference_type' => 'event',
			'status' => 'approved'
		));

		if (!$isExists) {
			/**
			 * @param int eventAttendeeID // optional pk
			 * @param int event_id // event id fk
			 * @param int user_id // guest userid
			 * @param string reference_type // enum ('itinerary', 'breakout', 'activity')
			 * @param int reference_id // remote id of which guest will be attending
			 * @param int role_id // guest role
			 * @param string team // guest team
			 */
			if ($postData['include_attendees']) {
				foreach ($eventAttendees as $attendee) {
					$attendeeData = array();
					$attendeeData['event_id'] = $event['eventID'];
					$attendeeData['user_id'] = $attendee['user_id'];
					$attendeeData['reference_type'] = 'event';
					$attendeeData['reference_id'] = $event['eventID'];
					$attendeeData['role_id'] = '3';
					$attendeeData['status'] = 'approved';
					$this->Guest->addEventGuest($attendeeData);
				}
			}
		}

		$response['success'] = (!$isExists) ? true : false;
		$response['message'] = (!$isExists) ? '' : 'Event title already exists.';
		$response['eventId'] = $eventID;
		$response['fieldClass'] = 'txtTitle';
		$response['fieldTitle'] = $postData['title'];

		return $response;
	}

	public function duplicate_event_form() {
		$isSaveSuccess = FALSE;

		$post = $this->input->post();
		if (!empty($post)) {
			$duplicateEventResponse = $this->_duplicate_event();

			$transaction        = 'success';
			$transactionMessage = 'Save Successful';
			if (!$duplicateEventResponse['success']) {
				$transaction        = 'error';
				$transactionMessage = $duplicateEventResponse['message'];

				$this->data['isSuccess']  = $transaction;
				$this->data['message']    = $transactionMessage;
			} else {
				$duplicatedEvent = $this->Events->getEvents(array('title' => $post['title']));
				$newEventId      = $duplicatedEvent[0]['eventID'];

				$this->session->set_flashdata('isSuccess', $transaction);
				$this->session->set_flashdata('message', $transactionMessage);

				redirect('/admin/duplicate_event_form/?id=' . $newEventId);
				die;
			}
		}

		$eventId     = $this->input->get('id');
		$event       = $this->Events->getEvent($eventId);
		$itineraries = $this->Itinerary->getEventItineraries($eventId);

		$this->data['event']       = $event;
		$this->data['itineraries'] = $itineraries;
		$this->data['mapPhotos']   = $this->Map_Photo->getPhoto($eventId);

		$this->load->view('admin/duplicate_event_form.php',$this->data);
	}

	/**
	 * Get Event Itineraries/Agenda
	 */

	public function get_event_itineraries(){
		$eventId = $this->input->get('id');
		$itineraries = $this->Itinerary->getEventItineraries($eventId);

		$eventPreferences = $this->Activity_Preference->getActivityPreference(array(
			'activityPreferenceID' => null,
			'referenceID' => null,
			'referenceType' => null,
			'eventID'  => $eventId
		));

		$agendaPreferences = $actvityPreferences = array();
		foreach($eventPreferences as $pref) {
			if('agenda' == $pref['referenceType'])
			$agendaPreferences[] = $pref['referenceID'];
			elseif('activity' == $pref['referenceType'])
			$actvityPreferences[] = $pref['referenceID'];
		}

		if(!empty($itineraries)){
			foreach($itineraries as &$itinerary){
				$itinerary['hasPreferences'] = in_array($itinerary['itineraryID'], $agendaPreferences);
				if($itinerary['breakout_status']) {
					$itinerary['breakouts'] =  $this->Breakout->getItineraryBreakouts($itinerary['itineraryID']);
					foreach ($itinerary['breakouts'] as &$breakout) {
						$breakout['hasPreferences'] = in_array($breakout['breakoutID'], $actvityPreferences);
					}
				}
			}
		}

		$this->data['itineraries'] = $itineraries;
		$this->data['method'] = 'get_event_itineraries';
		$this->load->view('admin/get_event_itineraries.php',$this->data);
	}

	/**
	 * Save Itinerary/Agenda
	 *
	 */
	public function save_itinerary(){
		$postData = $this->input->post();
		$return = array();
		if(!empty($postData)){
			if(!isset($postData['breakout_status']))
			$postData['breakout_status'] = 0;
			$return = $this->Itinerary->addItinerary($postData);

			//-- Add Map Reference to Photo
			$this->Map_Reference->add(array(
				'mapReferenceID' => isset($postData['mapReferenceID']) ? $postData['mapReferenceID'] : '',
				'map_photo_id'   => $postData['map_photo_id'],
				'reference_type' => 'agenda',
				'reference_id'	 => $return['itineraryID']
			));

			if (!empty($postData['user_id'][0])){
				if (empty($postData['itineraryID'])){
					$speakers = $postData['user_id'];
					foreach ($speakers as $userID){
						$activationKeyData = $this->Activation_Key->addActivationKey($userID);
						//$this->Guest->sendSpeakerInviteEmail($userID, $postData['event_id'], $return['itineraryID'], 'agenda', $activationKeyData['result'], $return['itineraryID']);
					}
				}
			}
		}
		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	public function send_speaker_invite(){
		$postData = $this->input->post();
		$itineraryID = '';
		if(!empty($postData)){
			$userID = $postData['uid'];
			$eventID = $postData['eid'];
			$referenceType = $postData['rtype'];
			$speakerRoleID = 2;
			if (isset($postData['bid'])){
				$referenceID = $postData['bid'];
				$itineraryID = $postData['id'];
				$itineraryInfo = $this->Itinerary->getItinerary($itineraryID);
				$breakoutInfo =  $this->Breakout->getBreakout($referenceID);

				$user = $this->Users->getUser($userID);

				$ihtml = array(
					'speakerName' 		=> $user['first_name'] . ' ' . $user['last_name'],
					'eventTitle' 		=> $itineraryInfo['title'],
					'activityTitle' 	=> $breakoutInfo['title'],
					'activityStart' 	=> date_format(new DateTime($postData['start_date_time']), 'F d, Y g:i a'),
					'activityEnd' 		=> date_format(new DateTime($postData['end_date_time']), 'F d, Y g:i a'),
					'activityLocation' 	=> $breakoutInfo['location'],
					'baseUrl'			=> base_url(),
					'eventId'			=> $itineraryInfo['event_id'],
					'itineraryId'		=> $itineraryInfo['itineraryID'],
					'referenceType'		=> 'activity',
					'userId'			=> $user['userID']
				);

				if (!empty($user['email'])) {
					//$emailSent = $this->Email_Services->genericEmailInvite($ihtml, 'breakout_speaker_invite', $user['email']);
				}

			}else{
				$referenceID = $postData['id'];
				$speaker = $this->Guest->getAttendees(array(
					'event_id' => $eventID,
					'reference_id' => $itineraryId,
					'role_id' => $speakerRoleID,
					'reference_type' => $referenceType,
					'user_id' => $userID
				));

				// check if recipient was already added to the db, if no add the recipient to the speaker list
				if (!$speaker) {
					$param = array(
							'eventAttendeeID' => null,
							'event_id' =>  $eventID,
							'user_id' => $userID,
							'reference_type' => $referenceType,
							'reference_id' => $referenceID,
							'role_id' => $speakerRoleID,
							'status' => 'accepted'
					);
					$result = $this->Guest->addEventGuest($param);
				}

				//$activationKeyData = $this->Activation_Key->addActivationKey($userID);
				//$this->Guest->sendSpeakerInviteEmail($userID, $eventID, $referenceID, $referenceType, $activationKeyData['result'], $itineraryID);
			}



		}
	}

	/**
	 * Edit Itinerary/Agenda
	 *
	 */
	public function edit_itinerary(){
		$eventId = $this->input->get('eid');
		$itineraryId = $this->input->get('id');
		$itinerary = $this->Itinerary->getItinerary($itineraryId);

		$breakoutInfo = array();
		$speakers = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'reference_id' => $itineraryId,
			'role_id' => $this->roles['speaker'],
			'reference_type' => 'agenda'
			));

			$breakoutInfo['guests'] = $this->Guest->getAttendees(array(
				'event_id' => $eventId,
				'reference_id' => $itineraryId
			));

			if(!empty($breakoutInfo['guests'])) {
				$breakoutInfo['guests'] = make_new_key($breakoutInfo['guests'], 'user_id');
			}

			$eventAttendees = $this->Guest->getAttendees(array(
				'event_id' => $eventId,
				'role_id' => $this->roles['attendees'],
				'reference_type' => 'agenda'
			));

			$attendees = $this->Guest->getAttendees(array(
				'event_id' => $eventId,
				'role_id' =>	$this->roles['attendees'],
				'reference_type'	=> 'event',
				'reference_id'		=> $eventId,
				'status' => 'approved'
			));


			$mapReferences = $this->Map_Reference->fetch(array(
			'reference_type' => 'agenda',
			'reference_id'	=> $itineraryId
			));
			$mapReference = !empty($mapReferences) ? current($mapReferences) : array();

			$mapPhotos = $this->Map_Photo->fetch(array('event_id' => $eventId));
			//dump($mapPhotos); exit;

			$this->data['mapReference'] = $mapReference;
			$this->data['mapPhotos']	= $mapPhotos;
			$this->data['breakoutInfo'] = $breakoutInfo;
			$this->data['speakers'] = $speakers;
			$this->data['itinerary'] = $itinerary;
			$this->data['attendees'] = $attendees;
			$this->data['method'] = 'edit_itinerary';
			$this->data['users'] = make_new_key($eventAttendees, 'user_id');

			$this->load->view('admin/edit_itinerary.php',$this->data);
	}

	/**
	 * Delete Itinerary/Agenda
	 *
	 */
	public function delete_itinerary(){
		$params = array(
			'itineraryID' => $this->input->get('id'),
			'event_id' => $this->input->get('eid')
		);
		$response = $this->Itinerary->deleteItinerary($params);

		if ($response['status']) {
			$args = array(
				'reference_id' => $this->input->get('id'),
				'event_id'     => $this->input->get('eid'),
				'reference_type' => 'agenda'
			);
			$this->Guest->deleteGuest($args);
		}

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($response));
	}

	/**
	 * Add Agenda Attendees
	 *
	 */
	public function add_agenda_attendees() {
		$getVar = $this->input->get();

		$postData = $this->input->post();
		if (!empty($postData)) {
			$response = true;
			if(!empty($postData['user_id'])) {
				$agendaAttendees = $this->Guest->getAttendees(array(
					'event_id' => $postData['event_id'],
					'role_id' => $this->roles['attendees'],
					'reference_type' => $postData['referenceType'],
					'team' => '',
					'status' => 'approved'
				));
				$agendaAttendees = make_new_key($agendaAttendees, 'user_id');
				$agendaAttendeesUserIds = array_keys($agendaAttendees);

				// delete agenda attendees
				$toBeDeleted = array_diff($agendaAttendeesUserIds, $postData['user_id']);
				if (!empty($toBeDeleted)) {
					foreach ($toBeDeleted as $uid) {
						$params = array(
							'user_id'        => $uid,
							'event_id'       => $postData['event_id'],
							//'role_id'        => $this->roles['attendees'],
							'reference_type' => $postData['referenceType'],
							'reference_id'   =>  $postData['agenda_id'],
							//'team'           => '',
							'status'         => 'approved'
						);
						$this->Guest->deleteGuest($params);
					}
				}

				// add agenda attendees
				foreach($postData['user_id'] as $uid) {
					if (!in_array($uid, $agendaAttendeesUserIds)) {
						$params = array(
							'user_id'        => $uid,
							'event_id'       => $postData['event_id'],
							'role_id'        => $this->roles['attendees'],
							'reference_type' => $postData['referenceType'],
							'reference_id'   =>  $postData['agenda_id'],
							'team'           => '',
							'status'         => 'approved'
						);
						$this->Guest->addEventGuest($params);
					}
				}
			} else {
				$params = array(
					'event_id' => $postData['event_id'],
					'reference_type' => $postData['referenceType'],
					'reference_id' =>  $postData['agenda_id'],
					'status' => 'approved'
					);
				$this->Guest->deleteGuest($params);
			}
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($response));
			exit;
		}

		if(!empty($getVar)) {
			$eventId = $getVar['eid'];
			$agendaId = $getVar['id'];
			$referenceType = $getVar['rtype'];
		}

		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_type' => 'event',
			'status' => 'approved'
			));

		$agendaAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_id' => $agendaId,
			'reference_type' => $referenceType,
			'status' => 'approved'
		));

		$agendaAttendees = make_new_key($agendaAttendees, 'user_id');

		$this->data['eventAttendees'] = $eventAttendees;
		$this->data['agendaAttendees'] = $agendaAttendees;
		$this->data['users'] = $this->Users->getUsers();
		$this->data['eventId'] = $eventId;
		$this->data['agendaId'] = $agendaId;

		$this->data['getVar'] = $this->input->get();

		$this->data['method'] = $this->uri->segment(2);
		$this->load->view('admin/add_agenda_attendees.php',$this->data);
	}

	/**
	 * Get Event Guests
	 */
	public function get_event_guests() {
		$eventId = $this->input->get('id');
		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_type' => 'event',
			'status' => 'approved'
			));

			$userCompanions = array();
			$arrayCompanions = $this->Companion->getCompanions();
			if(!empty($arrayCompanions)) {
				foreach($arrayCompanions as $comp) {
					$userCompanions[$comp['primary_user_id']][] = $comp;
				}
			}

			$this->data['userCompanions'] = $userCompanions;
			$this->data['attendees'] = make_new_key($eventAttendees,'user_id');
			$this->data['method'] = 'get_event_guests';
			$this->load->view('admin/get_event_guest.php',$this->data);
	}

	/**
	 * Get Event Owners
	 */
	public function get_event_owners() {
		$eventId = $this->input->get('id');

		$eventOwners = $this->Event_Owner->getEventOwners(array(
				'event_id' => $eventId,
		));


		$this->data['owners'] = make_new_key($eventOwners,'user_id');
		$this->data['method'] = 'get_event_owners';
		$this->load->view('admin/get_event_owner.php',$this->data);
	}

	/**
	 * Add Event Attendees
	 */
	public function add_event_attendees(){
		$getVar = $this->input->get();
		$postData = $this->input->post();

		$eventId = isset($getVar['eid']) ? $getVar['eid'] : $postData['event_id'];
		$eventAttendees = $this->Guest->getAttendees(array(
			   'event_id' => $eventId,
			   'role_id' =>	$this->roles['attendees'],
			   'reference_type' => 'event',
			   'status' => 'approved'
			   ));
			   $eventAttendees = make_new_key($eventAttendees, 'user_id');

			   $users =  $this->Users->getUsers();

			   $userCompanions = array();
			   $arrayCompanions = $this->Companion->getCompanions();
			   if(!empty($arrayCompanions)) {
				foreach($arrayCompanions as $comp) {
					$userCompanions[$comp['primary_user_id']][] = $comp;
				}
			   }
			   $this->data['userCompanions'] = $userCompanions;

			   $this->data['attendees'] = $eventAttendees;
			   $this->data['users'] = $users;
			   $this->data['eventId'] = $eventId;

			   if(!empty($postData['user_id'])){
				$response = array();
				$params = array(
				'event_id' => $postData['event_id'],
				'role_id' => $this->roles['attendees'],
				'reference_type' => 'event',
				'reference_id' =>  $postData['event_id'],
				'team' => '',
				'status' => 'approved'
				);
				//look for the remove attendees
				foreach($eventAttendees as $key => $val){
					if(!in_array($key,$postData['user_id']) && $val['is_primary']){
						$deleteParams = array(
						'user_id' => $key,
						'event_id' => $postData['event_id']
						);
						$this->Guest->deleteGuest($deleteParams);
					}
				}
				//save newly added
				$attendeeCompanions = array();
				foreach($postData['user_id'] as $uid) {
					if(!array_key_exists($uid, $eventAttendees)){
						$params['user_id'] = $uid;
						$this->Guest->addEventGuest($params);
					}
					if(!empty($userCompanions[$uid])){
						$attendeeCompanions[$uid] = $userCompanions[$uid];
					}
				}
				// look for companions
				if(!empty($attendeeCompanions)) {
					$this->data['companions'] = $attendeeCompanions;
					$response['selectCompanions'] = $this->load->view('admin/add_event_companion.php', $this->data, true);
				}
				$response['success'] = true;
				echo json_encode($response);
				exit;
			   }
			   $this->data['method'] = $this->uri->segment(2);
			   $this->load->view('admin/add_event_attendees.php',$this->data);
	}

	/**
	 * Add Event Owners
	 */
	public function add_event_owners(){
		$getVar = $this->input->get();
		$postData = $this->input->post();

		$eventId = isset($getVar['eid']) ? $getVar['eid'] : $postData['event_id'];
		$eventOwners = $this->Event_Owner->getEventOwners(array(
				'event_id' => $eventId,
		));
		$eventOwners = make_new_key($eventOwners, 'user_id');

		$users =  $this->Users->getUsers();



		$this->data['event_owners'] = $eventOwners;
		$this->data['users'] = $users;
		$this->data['eventId'] = $eventId;

		if(!empty($postData['user_id'])){
			$response = array();
			$params = array(
					'event_id' => $postData['event_id'],
			);
			//look for the remove event owners
			foreach($eventOwners as $key => $val){
				if(!in_array($key,$postData['user_id'])){
					$deleteParams = array(
							'user_id' => $key,
							'event_id' => $postData['event_id']
					);
					$this->Event_Owner->deleteEventOwner($key, $postData['event_id']);
				}
			}
			//save newly added
			//$attendeeCompanions = array();
			foreach($postData['user_id'] as $uid) {
				if(!array_key_exists($uid, $eventOwners)){
					$params['user_id'] = $uid;
					$this->Event_Owner->addEventOwner($params);
				}
				/* if(!empty($userCompanions[$uid])){
					$attendeeCompanions[$uid] = $userCompanions[$uid];
				} */
			}
			// look for companions
			if(!empty($attendeeCompanions)) {
				//$this->data['companions'] = $attendeeCompanions;
				//$response['selectCompanions'] = $this->load->view('admin/add_event_companion.php', $this->data, true);
			}
			$response['success'] = true;
			echo json_encode($response);
			exit;
		}
		$this->data['method'] = $this->uri->segment(2);
		$this->load->view('admin/add_event_owners.php',$this->data);
	}

	public function add_event_companion(){
		$postData = $this->input->post();

		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $postData['event_id'],
			'role_id' =>	$this->roles['attendees'],
			'reference_type' => 'event',
			'status' => 'approved'
			));
			$companions = array();
			foreach($eventAttendees as $eventAttendee) {
				if(!$eventAttendee['is_primary'])
				$companions[$eventAttendee['userID']] = $eventAttendee;
			}

			if(!empty($postData['user_id'])){
				$response = array();
				$params = array(
				'event_id' => $postData['event_id'],
				'role_id' => $this->roles['attendees'],
				'reference_type' => 'event',
				'reference_id' =>  $postData['event_id'],
				'team' => '',
				'status' => 'approved'
				);
				//look for the remove attendees
				foreach($companions as $key => $val){
					if(!in_array($key,$postData['user_id'])){
						$deleteParams = array(
						'user_id' => $key,
						'event_id' => $postData['event_id']
						);
						$this->Guest->deleteGuest($deleteParams);
					}
				}
				//save newly added
				foreach($postData['user_id'] as $uid) {
					if(!array_key_exists($uid, $companions)){
						$params['user_id'] = $uid;
						$this->Guest->addEventGuest($params);
					}
				}
				$response['success'] = true;
				$this->output->set_header('Content-type: application/json; charset=UTF-8');
				$this->output->set_output(json_encode($response));
				exit;
			}
	}

	/**
	 * Add Edit Guest
	 */
	public function add_edit_guest() {
		$uid = $this->input->get('uid');
		$postData = $this->input->post();
		$return = array();
		$userInfo = array();
		if($uid){
			$userInfo = $this->Users->getUser($uid);
		}
		if(!empty($postData)) {
			$postData['password'] = 'password';
			$user = $this->Users->addUser($postData);
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($user));
			exit;
		}
		$this->data['userInfo'] = $userInfo;
		$this->data['method'] = 'add_edit_guest';
		$this->load->view('admin/add_edit_guest.php',$this->data);
	}

	/**
	 * Delete Event Guests
	 */
	public function delete_event_guest(){
		$eventId = $this->input->get('eid');
		$userId = $this->input->get('uid');
		$attendeeCompanions = array();
		// delete companions
		$arrayCompanions = $this->Companion->getCompanions();
		if(!empty($arrayCompanions)) {
			foreach($arrayCompanions as $comp) {
				if($comp['primary_user_id'] == $userId){
					$deleteParams = array(
						'user_id' => $comp['user_id'],
						'event_id' => $eventId
					);
					$this->Guest->deleteGuest($deleteParams);
				}
			}
		}
		$return = $this->Guest->deleteEventGuest($userId,$eventId);
		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	/**
	 * Delete Event Owners
	 */
	public function delete_event_owner(){
		$eventId = $this->input->get('eid');
		$userId = $this->input->get('uid');

		$return = $this->Event_Owner->deleteEventOwner($userId,$eventId);
		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	/**
	 * Save Breakout (add/edit)
	 */
	public function add_edit_breakout(){
		$itineraryId = $this->input->get('iid');
		$eventId = $this->input->get('eid');
		$bid = $this->input->get('bid');
		$postData = $this->input->post();
		$return = $breakoutInfo = array();
		if(!empty($postData)){
			$itineraryInfo = $this->Itinerary->getItinerary($postData['itinerary_id']);
			$postData['start_date_time'] = 	$itineraryInfo['start_date_time'];
			$postData['end_date_time'] = 	$itineraryInfo['end_date_time'];
			$return = $this->Breakout->saveBreakout($postData);

			if (!empty($postData['user_id'])) {
				$user = array();
				foreach ($postData['user_id'] as $uid) {
					$speaker = $this->Guest->getAttendees(array(
						'event_id' => $postData['event_id'],
						'reference_id' => $postData['breakoutID'],
						'role_id' => '2',
						'reference_type' => $postData['reference_type'],
						'user_id' => $uid
					));

					// check if recipient was already added to the db, if no add the recipient to the speaker list
					if (!$speaker || empty($postData['breakoutID'])) {
						$user = $this->Users->getUser($uid);

						$ihtml = array(
							'speakerName' 		=> $user['first_name'] . ' ' . $user['last_name'],
							'eventTitle' 		=> $itineraryInfo['title'],
							'activityTitle' 	=> $postData['title'],
							'activityStart' 	=> date_format(new DateTime($postData['start_date_time']), 'F d, Y g:i a'),
							'activityEnd' 		=> date_format(new DateTime($postData['end_date_time']), 'F d, Y g:i a'),
							'activityLocation' 	=> $postData['location'],
							'baseUrl'			=> base_url(),
							'eventId'			=> $itineraryInfo['event_id'],
							'itineraryId'		=> $itineraryInfo['itineraryID'],
							'referenceType'		=> 'activity',
							'userId'			=> $user['userID']
						);

						if (!empty($user['email'])) {
							//$emailSent = $this->Email_Services->genericEmailInvite($ihtml, 'breakout_speaker_invite', $user['email']);
						}
					}
				}
			}

			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($return));
			exit;
		}
		$speakers = $breakoutSpeakers = array();
		if($bid) {
			// edit activity/breakout
			$breakoutInfo =  $this->Breakout->getBreakout($bid);
			$breakoutInfo['guests'] = $this->Guest->getAttendees(array(
				'event_id' => $eventId,
				'reference_id' => $bid
			));
			if(!empty($breakoutInfo['guests'])) {
				foreach ($breakoutInfo['guests'] as $breakoutInfoSpeakers) {
					if ($breakoutInfoSpeakers['role_id'] == '2') {
						$breakoutSpeakers[] = $breakoutInfoSpeakers;
					}
				}
				if (!empty($breakoutSpeakers)) {
					$breakoutSpeakers = make_new_key($breakoutSpeakers, 'user_id');
				}
			}
			$speakers = (!empty($breakoutSpeakers)) ? $breakoutSpeakers : $speakers;
		}
		$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' => $this->roles['attendees'],
			'reference_type' => 'event',
			'status' => 'approved'
			));

			$this->data['breakoutInfo'] = $breakoutInfo;
			$this->data['users'] = make_new_key($eventAttendees,'user_id');

			$this->data['itineraryId'] = $itineraryId;
			$this->data['eventId'] = $eventId;
			$this->data['speakers'] = $speakers;
			$this->data['method'] = 'add_edit_breakout';
			$this->load->view('admin/add_edit_breakout.php',$this->data);
	}

	/**
	 * Delete Breakout
	 */
	public function delete_breakout(){
		$breakoutId  = $this->input->get('bid');
		$return = $this->Breakout->deleteBreakout($breakoutId);

		if ($return['status']) {
			$args = array(
				'reference_id' => $this->input->get('bid'),
				'event_id'     => $this->input->get('eid'),
				'reference_type' => 'activity'
			);
			$this->Guest->deleteGuest($args);
		}

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	/**
	 * Add/Edit Breakout/Activity Attendees
	 */
	public function add_edit_breakout_attendees(){
		$eventId  = $this->input->get('eid');
		$breakoutId  = $this->input->get('bid');
		$itineraryId  = $this->input->get('iid');

		$eventAttendees =   $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_type' => 'event',
			'status' => 'approved'
			));

			$breakoutAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_type' => 'breakout',
			'reference_id'	=> $breakoutId,
			'status' => 'approved'
			));

			$postdata = $this->input->post();
			if(!empty($postdata)){
				$this->Guest->deleteGuest(array(
				'reference_id' => $postdata['bid'],
				'event_id' => $postdata['eid'],
				'role_id' => 3
				));
				$return = array();
				$data = array(
					'gid' => '',
					'event_id' =>$postdata['eid'],
					'reference_type' => 'breakout',
					'reference_id' => $postdata['bid'],
					'role_id' => 3,
					'team' => '',
					'status' => 'approved'
					);
					foreach($postdata['user_id'] as $id){
						$data['user_id'] =  $id;
						$return[] = $this->Guest->addEventGuest($data);
					}
					$this->output->set_header('Content-type: application/json; charset=UTF-8');
					$this->output->set_output(json_encode($return));
					exit;
			}

			$activityPreferences = $this->Activity_Preference->getActivityPreference(array(
			'activityPreferenceID' => null,
			'referenceID' => $breakoutId,
			'referenceType' => 'activity',
			'eventID' => $eventId
			));

			$this->data['eventAttendees'] = make_new_key($eventAttendees, 'user_id');
			$this->data['breakoutAttendees'] = make_new_key($breakoutAttendees, 'user_id');
			$this->data['eventId'] = $eventId;
			$this->data['breakoutId'] = $breakoutId;
			$this->data['itineraryId'] = $itineraryId;
			$this->data['activityPreferences'] = $activityPreferences;

			$this->data['method'] = 'add_edit_breakout';
			$this->load->view('admin/add_edit_breakout_attendees.php',$this->data);
	}

	/**
	 * Add User
	 */
	public function add_user() {
		$uid  = $this->input->get('uid');
		//process add user form
		$postdata = $this->input->post();
		$error = array();
		$edited = false;

		if(!empty($postdata)){
			//dump($_FILES); exit;
			/*begin uploading photo*/
			if (!empty($_FILES['user_photo']['tmp_name']) && $this->config->config['is_allow_user_photo']) {
				$uploadPath = $this->config->config['upload_path'];
				if (!is_dir($uploadPath)) {
					mkdir($uploadPath);
				} else {
					$uploadPath = $uploadPath . 'user';
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath);
					}
				}
				$conf = array(
							'upload_path' => $uploadPath,
							'allowed_types' => 'gif|jpg|png',
							'max_size' => 2048
				);

				$this->load->library('upload', $conf);
				$imgData = getimagesize($_FILES['user_photo']['tmp_name']);
				$raw_file_name = $_FILES['user_photo']['name'];
				$filename = sha1($raw_file_name . time()) . '.jpg';
				$conf['file_name'] = $filename;
				$this->upload->initialize($conf);
				if (!$this->upload->do_upload('user_photo')) {
					$error = array('error' => $this->upload->display_errors());
					$data['error'] = current($error);
				} else {
					$data = array('uploaded_data' => $this->upload->data());
					//$this->load->library('imageresizer', array('imagePath'=>$data['uploaded_data']['full_path']));
					//$raw_file_name = $data['uploaded_data']['raw_name'];
					//$orig_name = $data['uploaded_data']['orig_name'];
					//$filename = sha1($raw_file_name . time()) . '.jpg';
					// delete original photo
					//unlink($uploadPath . DIRECTORY_SEPARATOR . $orig_name);
					// create a resized copy and save
					//$this->imageresizer->resizeTo(600, 450)->saveToFile($uploadPath.DIRECTORY_SEPARATOR.$filename);

					$paramsPhoto = array(
								's_fname'=>$filename,
								'fk_i_uid' => $postdata['userID'],
								's_origdata' => serialize($imgData),
								'b_is_primary' => 1,
								'b_is_deleted' => 0
					);

					// delete current photo if this is update request
					if (!empty($postdata['userPhotoID']) && !empty($postdata['s_current_photo'])) {
						$currentPhoto = $uploadPath . DIRECTORY_SEPARATOR . $postdata['s_current_photo'];
						if (file_exists($currentPhoto)) {
							unlink($currentPhoto);
						}
						$paramsPhoto['userPhotoID'] = (int) $postdata['userPhotoID'];
					}

					$dataPhoto = $this->User_Photo->addPhoto($paramsPhoto);
					$this->data['db_inserted_photo'] = $dataPhoto;
				}

			} // upload photo

			// delete photo
			if (!empty($postdata['s_remove_photo']) && !empty($postdata['userPhotoID']) && !empty($postdata['s_current_photo'])) {

				$result = $this->User_Photo->deletePhoto($postdata['userPhotoID']);
				if ($result) {
					$uploadPath = $this->config->item('upload_path') . 'user/';
					$filename = $uploadPath . $postdata['s_current_photo'];
					if (file_exists($filename)) {
						unlink($filename);
					}
				}
			}

			if (empty($error)) {
				$user = $this->Users->addUser($postdata);
				$this->data['user_data'] = $user;
				$edited = true;
			}
		}

		$userInfo = $userPhoto = array();
		if (!$edited) {
			if(isset($uid)) {
				$userInfo =  $this->Users->getUser($uid);
				if (!empty($uid)) {
					$userPhoto = $this->User_Photo->getPhoto($uid);
					$userPhoto = make_new_key($userPhoto, 'fk_i_uid');
					$userInfo['uploaded_photo'] = $userPhoto;
				}
			}
		}

		$data['edited'] = $edited;
		$data['userInfo'] = $userInfo;
		$userData = $this->Users->getUsers();
		$data['users'] = $userData;
		$data['method'] = 'add_user';
		$this->load->view('admin/add_user.php',$data);
	}

	/**
	 * Get Users
	 */
	public function get_users() {

		$get = $this->input->get();

		$page = ($this->uri->segment(3))? $this->uri->segment(3) : 1;
		$perpage = 20;

		$userData = $this->Users->getUsers(array(
			'sort_field' => 'last_name,first_name',
			'is_primary' => '1',
			'page' => $page,
			'per_page' => $perpage
		));

		$totalRows = $this->Users->getNumUsers(array('is_primary' => '1'));
		$this->_paginationUsers($perpage, $totalRows, '/admin/get_users/');

		$this->data['users'] = $userData;
		$this->data['method'] = 'get_user';
		$this->data['pagination'] = $this->pagination->create_links();
		$this->load->view('admin/get_users.php',$this->data);
	}

	public function get_user() {
		$users   = array();
		$uri     = $this->uri->uri_to_assoc();
		$perpage = 20;
		$page    = (isset($uri['page']) && !empty($uri['page'])) ? $uri['page'] : 1;


		if (isset($uri['name'])) {
			$uri['name'] = (is_bool($uri['name'])) ? '' : $uri['name'];
			$args  = array(
				'name'       => $uri['name'],
				'sort_field' => 'last_name,first_name',
				'is_primary' => '1',
				'page'       => $page
			);
			$usersCount = count($this->Users->getUsers($args));

			$args['per_page'] = $perpage;
			$users = $this->Users->getUsers($args);

			$baseUrl = 'name/' . $uri['name'] . '/page/';
		}

		$baseUrl = '/admin/get_user/' . $baseUrl;
		$this->_paginationUsers($perpage, $usersCount, $baseUrl, 6);

		$this->data['users'] = $users;
		$this->data['method'] = 'get_user';
		$this->data['pagination'] = $this->pagination->create_links();
		$this->load->view('admin/get_users', $this->data);
	}

	/**
	 * _paginationUsers
	 * Set pagination for add_user module
	 * @param $perpage int/string
	 * @param $totalRows int/string
	 */
	private function _paginationUsers($perpage, $totalRows, $baseUrl, $uriSegment = '') {
		$this->load->library('pagination');
		$config['total_rows'] = $totalRows;
		$config['per_page'] = $perpage;
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '<li>';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['first_link'] = '<<';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '>>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['uri_segment'] = 3;
		$config['page_query_string'] = FALSE;
		$config['base_url'] = $baseUrl;
		$config['use_page_numbers'] = TRUE;

		if (!empty($uriSegment)) $config['uri_segment'] = $uriSegment;

		$this->pagination->initialize($config);
	}

	/**
	 * Delete Users
	 */
	public function delete_user(){
		$user_id = $this->input->get('uid');
		$arrayCompanions = $this->Companion->getCompanions($user_id);

		if(!empty($arrayCompanions)){
			foreach($arrayCompanions as $comp){
				$this->Users->deleteUser($comp['user_id']);
			}
		}
		return $this->Users->deleteUser($user_id);
	}

	/**
	 * Add Guest
	 */
	public function add_guest() {
		$uid  = $this->input->get('uid');
		//process add user form
		$postdata = $this->input->post();
		$error = array();
		$edited = false;
		$data_submitted = false;

		if(!empty($postdata)){

			/*begin uploading photo*/
			if (!empty($_FILES['user_photo']['tmp_name']) && $this->config->config['is_allow_user_photo']) {
				$uploadPath = $this->config->config['upload_path'];
				if (!is_dir($uploadPath)) {
					mkdir($uploadPath);
				} else {
					$uploadPath = $uploadPath . 'user';
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath);
					}
				}
				$conf = array(
							'upload_path' => $uploadPath,
							'allowed_types' => 'gif|jpg|png',
							'max_size' => 2048
				);

				$this->load->library('upload', $conf);
				$imgData = getimagesize($_FILES['user_photo']['tmp_name']);
				$raw_file_name = $_FILES['user_photo']['name'];
				$filename = sha1($raw_file_name . time()) . '.jpg';
				$conf['file_name'] = $filename;
				$this->upload->initialize($conf);
				if (!$this->upload->do_upload('user_photo')) {
					$error = array('error' => $this->upload->display_errors());
					$data['error'] = current($error);
				} else {

					if (empty($error)) {
						$user = $this->Users->addUser($postdata);
						$temp_uid =  $user['result']['userID'];
						$this->data['user_data'] = $user;
						$edited = true;
						$data_submitted = true;
					}

					$data = array('uploaded_data' => $this->upload->data());

					if (!$postdata['userID'])
					$postdata['userID'] = $temp_uid;
					$paramsPhoto = array(
								's_fname'=>$filename,
								'fk_i_uid' => $postdata['userID'],
								's_origdata' => serialize($imgData),
								'b_is_primary' => 1,
								'b_is_deleted' => 0
					);

					// delete current photo if this is update request
					if (!empty($postdata['userPhotoID']) && !empty($postdata['s_current_photo'])) {
						$currentPhoto = $uploadPath . DIRECTORY_SEPARATOR . $postdata['s_current_photo'];
						if (file_exists($currentPhoto)) {
							unlink($currentPhoto);
						}
						$paramsPhoto['userPhotoID'] = (int) $postdata['userPhotoID'];
					}

					$dataPhoto = $this->User_Photo->addPhoto($paramsPhoto);
					$this->data['db_inserted_photo'] = $dataPhoto;
				}

			} // upload photo

			// delete photo
			if (!empty($postdata['s_remove_photo']) && !empty($postdata['userPhotoID']) && !empty($postdata['s_current_photo'])) {

				$result = $this->User_Photo->deletePhoto($postdata['userPhotoID']);
				if ($result) {
					$uploadPath = $this->config->item('upload_path') . 'user/';
					$filename = $uploadPath . $postdata['s_current_photo'];
					if (file_exists($filename)) {
						unlink($filename);
					}
				}
			}
		}

		if (empty($error) &&  !$data_submitted && !empty($postdata)) {
			$user = $this->Users->addUser($postdata);
			$this->data['user_data'] = $user;
			$edited = true;
		}
		$userInfo = $userPhoto = $companionInfo = array();



		if(isset($uid)) {
			$userInfo =  $this->Users->getUser($uid);
			if (!empty($uid)) {
				$userPhoto = $this->User_Photo->getPhoto($uid);
				$userPhoto = make_new_key($userPhoto, 'fk_i_uid');
				$userInfo['uploaded_photo'] = $userPhoto;

				$companionInfo = current($this->Companion->getPrimaryUser($uid));
				$userInfo['type'] = $companionInfo['type'];
				$userInfo['primary_key_id'] = $companionInfo['primary_user_id'];
			}
		}

		if ($error){
			//retain previously entered data
			$userInfo['userID'] = $postdata['userID'];
			$userInfo['first_name'] = $postdata['first_name'];
			$userInfo['last_name'] = $postdata['last_name'];
			$userInfo['primary_key_id'] = $postdata['primary_user_id'];
			$userInfo['uploaded_photo'][$userInfo['userID']]['userPhotoID'] = $postdata['userPhotoID'];
			$userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'] = $postdata['s_current_photo'];
		}


		$data['edited'] = $edited;
		$data['userInfo'] = $userInfo;
		$userData = $this->Users->getUsers(array('is_primary' => true));
		$data['users'] = $userData;
		$data['method'] = 'add_guest';
		$this->load->view('admin/add_guest.php',$data);
	}

	/**
	 * Get Guest Users
	 */
	public function get_guest_users() {
		$page    = ($this->uri->segment(3))? $this->uri->segment(3) : 1;
		$perpage = 20;

		$primaryUserResultData = array();

		// get primary users
		$primaryUserData = $this->Users->getUsers(array(
			'sort_field' => 'email',
			'is_primary' => '1'
		));
		foreach($primaryUserData as $primaryUser){
			$primaryUserResultData[$primaryUser['userID']] = $primaryUser;
		}

		// get companions
		$userData = $this->Users->getUsers(array(
			'sort_field' => 'last_name,first_name',
			'is_primary' => '0',
			'page' => $page,
			'per_page' => $perpage
		));
		$totalRows = $this->Users->getNumUsers(array('is_primary' => '0'));
		$this->_paginationUsers($perpage, $totalRows, '/admin/get_guest_users/');

		$userResultData = array();
		foreach($userData as $key => $user){
			//retrieve primary user
			$eventCompanionAttendeeData = current($this->Companion->getPrimaryUser($key));
			$user['primary_user_id'] = $eventCompanionAttendeeData['primary_user_id'];
			$user['type'] = $eventCompanionAttendeeData['type'];
			$userResultData[$key] = $user;
		}

		$this->data['primaryUser'] = $primaryUserResultData;
		$this->data['users'] = $userResultData;
		$this->data['method'] = 'get_user';
		$this->data['pagination'] = $this->pagination->create_links();
		$this->load->view('admin/get_guest_users.php',$this->data);
	}

	public function get_guest_user() {
		$users   = array();
		$uri     = $this->uri->uri_to_assoc();
		$perpage = 20;
		$page    = (isset($uri['page']) && !empty($uri['page'])) ? $uri['page'] : 1;

		//get primary users
		$primaryUserResultData = array();
		$args = array(
			'sort_field' => 'email',
			'is_primary' => '1'
		);
		$primaryUserData = $this->Users->getUsers($args);
		foreach($primaryUserData as $primaryUser){
			$primaryUserResultData[$primaryUser['userID']] = $primaryUser;
		}

		if (isset($uri['name'])) {
			$uri['name'] = (is_bool($uri['name'])) ? '' : $uri['name'];
			$args  = array(
				'name'       => $uri['name'],
				'sort_field' => 'last_name,first_name',
				'is_primary' => '0',
				'page'       => $page
			);
			$usersCount = count($this->Users->getUsers($args));

			$args['per_page'] = $perpage;
			$users = $this->Users->getUsers($args);

			$baseUrl = 'name/' . $uri['name'] . '/page/';
		}

		$baseUrl = '/admin/get_guest_user/' . $baseUrl;
		$this->_paginationUsers($perpage, $usersCount, $baseUrl, 6);

		$userResultData = array();
		foreach($users as $key => $user){
			//retrieve primary user
			$eventCompanionAttendeeData = current($this->Companion->getPrimaryUser($key));
			$user['primary_user_id'] = $eventCompanionAttendeeData['primary_user_id'];
			$user['type'] = $eventCompanionAttendeeData['type'];
			$userResultData[$key] = $user;
		}

		$this->data['primaryUser'] = $primaryUserResultData;
		$this->data['users']       = $userResultData;
		$this->data['method']      = 'get_user';
		$this->data['pagination']  = $this->pagination->create_links();
		$this->load->view('admin/get_guest_users.php',$this->data);
	}

	/**
	 * Delete Guest User
	 */
	public function delete_guest_user(){
		$user_id = $this->input->get('uid');
		return $this->Users->deleteGuestUser($user_id);
	}

	/**
	 * Log out
	 */
	public function logout() {
		$this->_clearUserSession();
	}

	/**
	 * Email Validation
	 */
	public function validate_email(){
		$isValid = true;
		$email = $this->input->get('email');
		$users =  $this->Users->getUsers();
		foreach($users as $user){
			if($email == $user['email']) {
				$isValid = false;
				break;
			}
		}
		echo $isValid ? 'true' : 'false';
		exit;
	}

	/**
	 * Email Event Invite
	 */
	public function email_event_invite()
	{
		$eventId = $this->input->get('eid');
		$data['result'] = $this->Guest->emailGuestsEventInvite($eventId, $this->roles['attendees']);
		$this->load->view('admin/email_event_invite.php', $data);
	}

	public function view_activity_preferences(){
		$eventID = $this->input->get('eid');
		$referenceType = $this->input->get('rtype');
		$referenceID = $this->input->get('id');

		$postdata = $this->input->post();
		if (!empty($postdata)) {
			$return = $this->Activity_Preference->saveActivityPreference($postdata);

			// todo do not override saving of options
			if(!empty($postdata['options']) && !empty($return[0]['activityPreferenceID'])){
				$postdata['activityPreferenceID'] = $return[0]['activityPreferenceID'];
				$this->Activity_Preference_Option->saveActivityPreferenceOption($postdata);
			}
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($return));
			exit;
		}

		$activityPreferences = $this->Activity_Preference->getActivityPreference(array(
			'activityPreferenceID' => null,
			'referenceID' => $referenceID,
			'referenceType' => $referenceType,
			'eventID' => $eventID
		));

		$this->data['activityPreferences'] = $activityPreferences;
		$this->data['getVar'] = $this->input->get();
		$this->data['method'] = $this->uri->segment(2);
		$this->load->view('admin/view_activity_preferences.php',$this->data);
	}

	// Manage Activity Preferences
	public function manage_activity_preferences() {
		$getVar = 	 $this->input->get();
		$activityID = $getVar['id'];
		$eventID = $getVar['eid'];
		$referenceType = trim($getVar['rtype']);

		switch($referenceType){
			case 'agenda':
				$activityInfo =  $this->Itinerary->getItinerary($activityID);
				break;
			case 'activity':
				$activityInfo =  $this->Breakout->getBreakout($activityID);
				break;
		}

		$activityPreferences = $this->Activity_Preference->getActivityPreference(array(
			'activityPreferenceID' => null,
			'referenceID' => $activityID,
			'referenceType' => $referenceType,
			'eventID' => $eventID
		));
		$this->data['activityInfo'] = $activityInfo;
		$this->data['activityPreferences'] = $activityPreferences;
		$this->data['getVar'] = $this->input->get();

		$this->load->view('admin/manage_activity_preferences.php',$this->data);
	}

	/**
	 * Add/Edit Activity Preference
	 *
	 */

	public function add_edit_activity_preference(){
		$getVar = 	 $this->input->get();
		$activityPreferenceID = $getVar['apid'];
		$activityID = $getVar['id'];
		$eventID = $getVar['eid'];
		$referenceType = trim($getVar['rtype']);

		$postdata = $this->input->post();
		if (!empty($postdata)) {
			$return = $this->Activity_Preference->saveActivityPreference($postdata);
			if(!empty($postdata['options']) && !empty($return[0]['activityPreferenceID'])){
				$postdata['activityPreferenceID'] = $return[0]['activityPreferenceID'];
				$this->Activity_Preference_Option->saveActivityPreferenceOption($postdata);
			}
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($return));
			exit;
		}

		$preferenceInfo = array();

		if($activityPreferenceID) {
			$preferenceInfo = $this->Activity_Preference->getActivityPreference(array(
				'activityPreferenceID' => $activityPreferenceID,
				'referenceID' => $activityID,
				'referenceType' => $referenceType,
				'eventID' => $eventID
			));
		}

		$this->data['preferenceInfo'] = !empty($preferenceInfo[0]) ? $preferenceInfo[0] : null;
		$this->data['getVar'] = $getVar;

		$this->load->view('admin/add_edit_activity_preference.php',$this->data);
	}

	/**
	 * Delete Activity Preference
	 */
	public function delete_activity_preference(){
		$getVar = $this->input->get();
		$params = array(
			'activityPreferenceID' => $getVar['apid'],
			'referenceID' => $getVar['rid'],
			'referenceType' => $getVar['rtype'],
			'eventID'  => $getVar['eid']
		);

		$return = $this->Activity_Preference->deleteActivityPreference($params);

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
		exit;
	}

	/**
	 * Check if user added in activity has already a concurrent activity
	 */
	public function is_concurrent_activity()
	{
		$userId = $this->input->get('uid');
		$itineraryId = $this->input->get('iid');
		$breakoutId = $this->input->get('bid');
		$data['isConcurrentActivity'] = $this->Guest->isConcurrentActivity($userId, $itineraryId, $breakoutId);
		$this->load->view('admin/is_concurrent_activity', $data);
	}

	public function view_attendees() {
		$activityID = $this->input->get('id');
		$eventID = $this->input->get('eid');
		$referenceType = $this->input->get('rtype');

		$activityAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventID,
			'role_id' =>	$this->roles['attendees'],
			'reference_id' => $activityID,
			'reference_type' => $referenceType,
			'status' => 'approved'
			));

			$activityAttendees = make_new_key($activityAttendees, 'user_id');

			$this->data['activityAttendees'] = $activityAttendees;
			$this->data['getVar'] = $this->input->get();
			$this->data['method'] = $this->uri->segment(2);
			$this->load->view('admin/view_attendees.php',$this->data);
	}

	/**
	 * Manage Agenda/Activity Attendees
	 * Add/Delete/Edit Agenda/Activity Attendees
	 */
	public function manage_activity_attendees() {
		$getVar = 	 $this->input->get();
		$activityID = $getVar['id'];
		$eventID = $getVar['eid'];
		$referenceType = trim($getVar['rtype']);

		switch($referenceType){
			case 'agenda':
				$activityInfo =  $this->Itinerary->getItinerary($activityID);
				break;
			case 'activity':
				$activityInfo =  $this->Breakout->getBreakout($activityID);
				break;
		}
		$params = array(
			'event_id' => $eventID,
			'role_id' =>	$this->roles['attendees'],
			'reference_id' => $activityID,
			'reference_type' => $referenceType,
			'status' => 'approved'
			);
			$activityAttendees = $this->Guest->getAttendees($params);

			$this->data['activityAttendees'] = make_new_key($activityAttendees, 'user_id');
			$this->data['activityInfo'] = $activityInfo;
			$this->data['getVar'] = $getVar;
			$this->data['method'] = $this->uri->segment(2);
			$this->load->view('admin/manage_activity_attendees.php',$this->data);
	}

	/**
	 * Add Activity Attendees
	 */

	public function add_edit_activity_attendee() {
		$getVar  = $this->input->get();

		$postData = $this->input->post();
		if(!empty($postData)){
			$return = array();
			$params = array(
				'event_id' => $postData['eventID'],
				'role_id' => $this->roles['attendees'],
				'reference_type' => $postData['referenceType'],
				'reference_id' =>  $postData['activityID'],
				'user_id' =>  $postData['user_id'],
				'team' => '',
				'status' => 'approved'
				);
				$return = $this->Guest->addEventGuest($params);
				if(!empty($postData['options'])){
					$this->Attendee_Activity_Preference->saveAttendeeActivityPreference($postData);
				}
				$this->output->set_header('Content-type: application/json; charset=UTF-8');
				$this->output->set_output(json_encode($return));
				exit;
		}

		$userID =  $getVar['uid'];
		$activityID  = $getVar['id'];
		$eventID  = $getVar['eid'];
		$referenceType = $getVar['rtype'];

		$activityAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventID,
			'role_id' =>	$this->roles['attendees'],
			'reference_id' => $activityID,
			'reference_type' => $referenceType,
			'status' => 'approved'
			));
			$activityAttendees = make_new_key($activityAttendees, 'user_id');

			$eventAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventID,
			'role_id' => $this->roles['attendees'],
			'status' => 'approved'
			));

			$eventAttendees = make_new_key($eventAttendees, 'user_id');


			if('itinerary' == $referenceType) {
				$referenceType = 'agenda';
			}
			$activityPreferences = $this->Activity_Preference->getActivityPreference(array(
			'activityPreferenceID' => null,
			'referenceID' => $activityID,
			'referenceType' => $referenceType,
			'eventID' => $eventID
			));

			$attendeeActivityPreferences = array();
			if($userID){
				$res = $this->Attendee_Activity_Preference->getAttendeeActivityPreference(
				array (
					'attendeeActivityPreferenceID' => null,
					'activityPreferenceID' => $activityID,
					'activityPreferenceOptionID' => null,
					'userID' => $userID
				)
				);
				if(!empty($res)) {
					foreach($res as $opt){
						$attendeeActivityPreferences[$opt['activityPreferenceOptionID']][] = $opt['value'];
					}
				}
			}

			$this->data['activityPreferences'] = make_new_key($activityPreferences,'activityPreferenceID');
			$this->data['attendeeActivityPreferences'] = $attendeeActivityPreferences;
			$this->data['eventAttendees'] = $eventAttendees;
			$this->data['activityAttendees'] = $activityAttendees;
			$this->data['getVar'] = $getVar;
			$this->load->view('admin/add_activity_attendee.php',$this->data);
	}

	public function delete_activity_attendee(){
		$getVar  = $this->input->get();
		$userID = $getVar['uid'];
		$activityID  = $getVar['id'];
		$eventID  = $getVar['eid'];
		$referenceType = $getVar['rtype'];
		$params = array(
			'user_id' => $userID,
			'event_id' => $eventID,
			'role_id' => $this->roles['attendees'],
			'reference_type' => $referenceType,
			'reference_id' =>  $activityID,
			'team' => ''
			);
			$return = $this->Guest->deleteGuest($params);
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode(array('response' =>$return)));
			exit;
	}

	/**
	 * Add Itinerary
	 */

	public function add_itinerary()
	{
		$eventId = $this->input->get('eid');
		$attendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' =>	$this->roles['attendees'],
			'reference_type'	=> 'event',
			'reference_id'		=> $eventId,
			'status' => 'approved'
			));

			$mapPhotos = make_new_key($this->Map_Photo->fetch(array('event_id' => $eventId)), 'mapPhotoID');

			$this->data['eventId'] = $eventId;
			$this->data['attendees'] = $attendees;
			$this->data['mapPhotos'] = $mapPhotos;

			$this->load->view('admin/add_itinerary.php', $this->data);
	}

	/**
	 * Show Concurrent Activities
	 */
	public function show_concurrent_activity(){
		$getVar  = $this->input->get();
		$guestActivities = $this->Guest->getUserConcurrentActivities($getVar['user_id'],$getVar['start_date'],$getVar['end_date']);
		$this->data['guestActivities'] = $guestActivities;
		$this->load->view('admin/view_concurrent_activities.php',$this->data);
	}

	public function upload_map() {

		$postData = $this->input->post();
		//dump($_FILES);	exit;
		if(!empty($postData)){
			dump($_FILES);	exit;
		}
		$this->data = array(
			'eventId'		=> $this->input->get('eid'),
			'itineraryId'	=> $this->input->get('iid'),
			'method'		=> 'upload_map'
			);

			$this->load->view('admin/upload_map', $this->data);
	}

	/**
	 * _validateCompanions
	 * Validate if companions' primary user is selected
	 * @param $post array
	 * @return array
	 */
	private function _validateCompanions($post) {
		$approvedCompanions = $errorMessage = $primaryUsers = $companionUsers = array();

		$this->lang->load('static_message');
		$staticMessage = $this->lang->line('SYS_MESSAGE_COMPANION_PRIMARY_USER_REQ');

		foreach ($post['eventAttendeeIDs'] as $value) {
			$attendeeInfo = $this->Guest->getAttendees(array('eventAttendeeID' => $value));
			foreach ($attendeeInfo as $info) {
				$companion = $this->Companion->getCompanion($info['user_id']);
				$isPrimaryUser = (empty($companion)) ? TRUE : FALSE;

				if ($isPrimaryUser) {
					$primaryUsers[$info['user_id']] = $value;
				} else {
					$eventDetails = $this->Events->getEvent($info['event_id']);
					$companionUsers[] = array(
						'user_id'          => $info['user_id'],
						'primary_user_id'  => $companion[0]['primary_user_id'],
						'eventAttendeeIDs' => $value,
						'event_id'         => $info['event_id'],
						'eventTitle'       => $eventDetails['title']
					);
				}
			}
		}

		foreach ($companionUsers as $companion) {
			if (array_key_exists($companion['primary_user_id'], $primaryUsers)) {
				$approvedCompanions[] = $companion['eventAttendeeIDs'];
			} else {
				$args = array(
					'user_id' => $companion['primary_user_id'],
					'event_id' => $companion['event_id'],
				);
				$existingPrimaryUser = $this->Guest->getAttendees($args);

				switch ($existingPrimaryUser[0]['status']) {
					case 'approved':
						$approvedCompanions[] = $companion['eventAttendeeIDs'];
						break;
					default:
						$pUserInfo = $this->Guest->getAttendees(array('user_id' => $companion['primary_user_id']));
						$cUserInfo = $this->Guest->getAttendees(array('user_id' => $companion['user_id']));

						$primaryUserFullName   = $pUserInfo[0]['first_name'] . ' ' . $pUserInfo[0]['last_name'];
						$companionUserFullName = $cUserInfo[0]['first_name'] . ' ' . $cUserInfo[0]['last_name'];

						$errorMessage[] = sprintf($staticMessage, $primaryUserFullName, $companion['eventTitle'], $companionUserFullName);
						break;
				}
			}
		}
		if (!empty($errorMessage)) $this->session->set_userdata('primaryUserReqMessage', $errorMessage);

		$return = array(
			'selectedAttendees' => array_merge($primaryUsers, $approvedCompanions),
			'errorMessage'      => $errorMessage
		);

		return $return;
	}

	/**
	 * Manage Requests
	 */
	public function manage_requests(){
		$post = $this->input->post();
		$postStatus = $errorMessage = '';
		$selectedAttendees = array();
		$params = array();
		$events = $this->Events->getEvents(array('status' => 1));
		$eventByID = make_new_key($events, 'eventID');

		$getVar = $this->input->get();
		$eventID =  isset($getVar['eid']) ? $getVar['eid'] : '';
		$status = isset($getVar['status']) ? $getVar['status'] : 'pending';
		$page = !empty($getVar['per_page']) ? $getVar['per_page'] : 1;
		$perpage = 20;
		$params = array(
			'event_id' => $eventID,
			'status' => $status,
			'role_id' => $this->roles['attendees'],
			'order_by' => 'date_joined'
		);

		$total =  $this->Guest->getCount($params);
		$params['page'] = $page;
		$params['per_page'] = $perpage;
		$requests = $this->Guest->getAttendees($params);

		if(!empty($post)){

			$params = array(
				'eventAttendeeIDs' => implode(',',$post['eventAttendeeIDs']),
				'status' => $post['status']
			);

			$selectedAttendees = $post['eventAttendeeIDs'];
			$postStatus = $post['status'];

			if ($postStatus == 'approved') {
				$validateCompanion = $this->_validateCompanions($post);
				$selectedAttendees = $validateCompanion['selectedAttendees'];
				$errorMessage      = $validateCompanion['errorMessage'];

				$params['eventAttendeeIDs'] = implode(',',$selectedAttendees);
			}

			if (!empty($params['eventAttendeeIDs'])) {
				$this->Guest->updateStatus($params);

				//SEND NOTIFICATION
				if (!empty($postStatus)) {
					$registrant = "";
					$eventsAttendees = make_new_key($requests, 'eventAttendeeID');

					foreach ($selectedAttendees as $selectedAttendee) {
						$registrant = $eventsAttendees[$selectedAttendee];


						$eventTitle = $eventByID[$registrant['event_id']]['title'];
						if ( empty($eventTitle) ){ $eventTitle = "Unknown Event"; }
						if ( !empty($registrant) && 'approved' === $postStatus ){
							$this->Email_Services->notifyApproved($registrant, $eventTitle);
						} elseif ( !empty($registrant) && 'rejected' === $postStatus ) {

							$companions = $this->Guest->getGuestCompanions($registrant['user_id'], $registrant['event_id'], 'pending');
							if ($companions) {
								foreach ($companions as $companion){
									$params = array(
									'eventAttendeeIDs' => $companion['eventAttendeeID'],
									'status' => $postStatus
									);
									$this->Guest->updateStatus($params);
								}
							}
							$this->Email_Services->notifyRejected($registrant, $eventTitle);

							// check here if there are companions included, they will also be rejected
						}
					}
				}
				redirect('/admin/manage_requests/?eid=' . $eventID . '&status='. $status);
				die();
			}
		}

		$this->data['errorMessage'] = $this->session->userdata('primaryUserReqMessage');
		$this->session->set_userdata('primaryUserReqMessage', '');

		$this->load->library('pagination');
		$config['total_rows'] = $total;
		$config['per_page'] = $perpage;
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '<li>';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['first_link'] = '<<';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = '>>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['page_query_string'] = true;
		$config['base_url'] = '/admin/manage_requests/?status='.$status.'&eid='.$eventID;
		$config['use_page_numbers'] = TRUE;
		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		$eventAgenda = $this->Itinerary->getAllItineraries();
		$eventActivities = $this->Breakout->getAllBreakouts();

		$this->data['requests'] = $requests;
		$this->data['events'] = $eventByID;
		$this->data['eventAgenda'] =  make_new_key($eventAgenda, 'itineraryID');
		$this->data['eventActivities'] = make_new_key($eventActivities, 'breakoutID');
		$this->data['method'] = $this->uri->segment(2);
		$this->data['eventID'] = $eventID;
		$this->data['status'] = $status;
		$this->load->view('admin/manage_requests',$this->data);
	}

	/**
	 * Get Presentation Categories
	 */
	public function get_presentation_categories() {
		$eventId = $this->input->get('id');

		$presentationCategories = $this->Presentation_Category->getPresentationCategories(array(
				'event_id' => $eventId,
		));

		$this->data['presentation_categories'] = make_new_key($presentationCategories,'presentationCategoryID');
		$this->data['method'] = 'id';
		$this->load->view('admin/get_presentation_categories.php',$this->data);
	}

	/**
	 * Add Presentation Category
	 */

	public function add_presentation_category()
	{
		$eventId = $this->input->get('eid');

		$this->data['eventId'] = $eventId;

		$this->load->view('admin/add_presentation_category.php', $this->data);
	}

	/**
	 * Save Presentation Category
	 *
	 */
	public function save_presentation_category(){
		$postData = $this->input->post();
		$return = array();

		if(!empty($postData)){
			$return = $this->Presentation_Category->savePresentationCategory($postData);
		}

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	/**
	 * Edit Presentation Category
	 *
	 */
	public function edit_presentation_category(){
		$eventId = $this->input->get('eid');
		$presentationCategoryID = $this->input->get('id');
		$data['presentationCategoryID'] = $presentationCategoryID;
		$presentation_category = $this->Presentation_Category->getPresentationCategory($data);
		$this->data['presentation_category'] = $presentation_category[0];
		$this->data['method'] = 'edit_presentation_category';
		$this->load->view('admin/edit_presentation_category.php',$this->data);
	}


	/**
	 * Delete Presentation Category
	 *
	 */
	public function delete_presentation_category(){
		$params = array(
			'presentationCategoryID' => $this->input->get('id'),
			'event_id' => $this->input->get('eid')
		);
		$response = $this->Presentation_Category->deletePresentationCategory($params);
		$response = $this->Presentation->deletePresentation(array('presentation_category_id'=>$this->input->get('id')));
		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($response));
	}

	public function view_presentations(){
		$eventID = $this->input->get('eid');
		$presentationCategoryID = $this->input->get('id');

		/*
		 $postdata = $this->input->post();
		if (!empty($postdata)) {
			$return = $this->Presentation->saveActivityPreference($postdata);

			// todo do not override saving of options
			if(!empty($postdata['options']) && !empty($return[0]['activityPreferenceID'])){
				$postdata['activityPreferenceID'] = $return[0]['activityPreferenceID'];
				$this->Activity_Preference_Option->saveActivityPreferenceOption($postdata);
			}
			$this->output->set_header('Content-type: application/json; charset=UTF-8');
			$this->output->set_output(json_encode($return));
			exit;
		}
		*/

		$presentations = $this->Presentation->getPresentations(array(
			'presentation_category_id' => $presentationCategoryID
		));

		$this->data['presentations'] = $presentations;
		$this->data['getVar'] = $this->input->get();
		$this->data['method'] = $this->uri->segment(2);
		$this->load->view('admin/view_presentations.php',$this->data);
	}

	// Manage Presentations
	public function manage_presentations() {
		$presentation           = array();
		$getVar                 = $this->input->get();
		$presentationCategoryID = $getVar['id'];
		$presentationID         = $getVar['pid'];
		$eventID                = $getVar['eid'];

		$transaction        = $this->session->userdata('presentationTransaction');
		$transactionMessage = $this->session->userdata('presentationTransactionMessage');

		$postback = array(
			'title'        => $this->session->userdata('presentationTitle'),
			'url'          => $this->session->userdata('presentationUrl'),
			'display_type' => $this->session->userdata('presentationDisplay')
		);

		$this->session->unset_userdata('presentationTitle');
		$this->session->unset_userdata('presentationUrl');
		$this->session->unset_userdata('presentationDisplay');
		$this->session->unset_userdata('presentationTransaction');
		$this->session->unset_userdata('presentationTransactionMessage');

		$presentation_category = $this->Presentation_Category->getPresentationCategory(array('presentationCategoryID' => $presentationCategoryID));
		$presentations         = $this->Presentation->getPresentationsByCategory(array('presentation_category_id' => $presentationCategoryID));
		if (!empty($presentationID)) {
			$presentation = $this->Presentation->getPresentation(array('presentationID' => $presentationID));
		}

		$this->data['filePath']             = $this->Presentation->getUploadPath();
		$this->data['presentationCategory'] = $presentation_category[0];
		$this->data['presentations']        = $presentations;
		$this->data['presentation']         = $presentation;
		$this->data['getVar']               = $this->input->get();
		$this->data['transaction']          = (!empty($transaction)) ? $transaction : '';
		$this->data['transactionMessage']   = (!empty($transactionMessage)) ? $transactionMessage : '';
		$this->data['postback']             = $postback;

		$this->load->view('admin/manage_presentations', $this->data);
	}

	/**
	 * _uploadFilePresentation
	 * Handles the file upload on the presentation module.
	 * @param $files array
	 * @return array
	 */
	private function _uploadFilePresentation($files) {
		if(isset($files['filePresentation']) && !empty($files['filePresentation'])) {
			$conf = array(
				'upload_path'   => $this->Presentation->getUploadPath(),
				'allowed_types' => '*',
				'max_size'      => 2048
			);
			$this->load->library('upload', $conf);
			$this->upload->initialize($conf);

			if (!$this->upload->do_upload('filePresentation')) {
				$return = array(
					'success' => FALSE,
					'message' => $this->upload->display_errors('',''),
					'data'    => ''
				);
			} else {
				$return = array(
					'success' => TRUE,
					'message' => 'Upload Successful',
					'data'    => serialize($this->upload->data())
				);
			}
		}
		return $return;
	}

	/**
	 * Add/Edit Presentation
	 */
	public function add_edit_presentation() {
		$postdata = $this->input->post();
		$eventID  = $postdata['hfEventId'];

		$fileUpload = $preferenceInfo = array();

		if ($postdata['rbtnDisplayType'][0] == 'document') {
			$fileUpload = $this->_uploadFilePresentation($_FILES);
		}

		$pId = (!empty($postdata['presentationID'])) ? $postdata['presentationID'] : 0;
		$location = base_url()  . $this->router->class . '/manage_presentations?id=' . $postdata['presentation_category_id'] . '&pid=' . $pId . '&eid=' . $eventID;
		if (!empty($postdata)) {
			$postdata['display_type'] = $postdata['rbtnDisplayType'][0];
			
			$postdata['title'] = htmlentities($postdata['title']);
			
			if (!empty($fileUpload)) {
				if ($fileUpload['success']) {
					$postdata['url'] = '';
					$postdata['document_meta'] = $fileUpload['data'];
					$this->Presentation->savePresentation($postdata);
				}
				$this->session->set_userdata('presentationTransaction', $fileUpload['success']);
				$this->session->set_userdata('presentationTransactionMessage', $fileUpload['message']);
			} else {
				$transaction = $this->Presentation->savePresentation($postdata);
				$this->session->set_userdata('presentationTransaction', true);
				$this->session->set_userdata('presentationTransactionMessage', 'Save Successful');
			}
		}
		$this->session->set_userdata('presentationTitle', $postdata['title']);
		$this->session->set_userdata('presentationUrl', $postdata['url']);
		$this->session->set_userdata('presentationDisplay', $postdata['rbtnDisplayType'][0]);

		header('Location: ' . $location);
		die;
	}

	/**
	 * Update Presentation Category
	 *
	 */
	public function update_presentation_order(){
		$ids = $this->input->post('ids');
		$order_counter = 1;
		foreach ($ids as $id) {
			$presentation = $this->Presentation->getPresentation(array(
				'presentationID' => $id
			));
			$new_presentation = current($presentation);
			$new_presentation['order'] = $order_counter;

			$return = $this->Presentation->savePresentation($new_presentation);

			$order_counter++;
		}

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
	}

	/**
	 * Delete Presentation
	 */
	public function delete_presentation(){
		$getVar = $this->input->get();
		$params = array(
			'presentationID' => $getVar['pid'],
		);

		$return = $this->Presentation->deletePresentation($params);

		$this->output->set_header('Content-type: application/json; charset=UTF-8');
		$this->output->set_output(json_encode($return));
		exit;
	}

}