<?php
require_once APPPATH . 'libraries/clients/api_user_client.php';

class Webapp extends CI_Controller {
	private $data = array();
	private $userData = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');

		//load roles
		$roles = $this->Role->getRoles();
		if($roles){
			foreach($roles as $role){
				$this->roles[strtolower($role['title'])] = $role['roleID'];
			}
		}
    }

    /**
     * Web Application Login
     */

	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$hfBackUrl = $this->input->post('backUrl');

		$this->session->set_userdata('username', $username);
		$backUrl = (isset($_GET['back_url']) && !empty($_GET['back_url'])) ? urldecode($_GET['back_url']) : '';

		$objEventClient = new api_user_client();
		$data = array();
		if (!empty($username) && !empty($password)) {
			$userData = $objEventClient->login_get(array('email'=>$username, 'password'=>$password));
			if (!empty($userData['result']) && $userData['result']['active'] != '0') {
				$this->session->set_userdata('_user', $userData['result']);
				if (!empty($hfBackUrl)) {
					redirect($hfBackUrl);
					die;
				}
				redirect('/webapp/');
				die;
			} else {
				if ($userData['result']['active'] == '0' && isset($userData['result']['active'])) {
					$userData['error'] = 'Account is Inactive. Please check your email to enable account.';
				}
				$this->data['loginErrorMsg'] = $userData['error'];
				$this->session->set_userdata('sessionMessage', $userData['error']);
				$this->session->set_userdata('sessionUsername', $username);
				redirect($hfBackUrl);
			}
		}
		$sessionUsername = $this->session->userdata('username');
		if (empty($sessionUsername)) {
			$sessionUsername = $this->session->userdata('sessionUsername');
		}

		$this->data['loginErrorMsg'] = $this->session->userdata('sessionMessage');
		$this->data['username']      = $sessionUsername;

		$this->session->set_userdata('sessionMessage', '');
		$this->session->set_userdata('sessionUsername', '');

		$this->data['page_id'] = $this->uri->segment(2);
		$this->data['backUrl'] = $backUrl;
		$this->load->view('webapp/login.php', $this->data);
	}

	/**
	 * Event's List
	 */

	public function index()
	{
		self::checkAuth();
		//$userId = $_SESSION['_user']['userID'];
		$userId = $this->userData['userID'];
		$userInfo = $this->Users->getUser($userId);
		$userEvents = $this->Guest->getGuestEvents($userId);
		$pastEvents = $this->Guest->getGuestPastEvents($userId);

		$myPendingEvents = $this->Guest->getGuestPendingEvents($userId);
		$otherEvents = $this->Guest->getOtherEvents($userId);

		if(!empty($userEvents)) {
			$myEventsKeys = array_keys($userEvents);
			foreach ($myEventsKeys as $myEventsKey) {
				if(array_key_exists($myEventsKey, $otherEvents)) {
					unset($otherEvents[$myEventsKey]);
				}
				if(array_key_exists($myEventsKey, $myPendingEvents)) {
					unset($myPendingEvents[$myEventsKey]);
				}
			}
		}

		if(!empty($myPendingEvents)) {
			$myPendingEventsKeys = array_keys($myPendingEvents);
			foreach ($myPendingEventsKeys as $myPendingEventsKey) {
				if(array_key_exists($myPendingEventsKey, $otherEvents)) {
					unset($otherEvents[$myPendingEventsKey]);
				}
			}
		}

		$this->data['events'] = array(
			'myEvents' => $userEvents,
			'myPendingEvents'	=> $myPendingEvents,
			'myPastEvents'	=> $this->Guest->getGuestPastEvents($userId),
			'otherEvents'		=> $otherEvents
		);

		$this->data['userID'] = $userId;
		$this->data['userInfo'] = $userInfo;
		$this->data['page_id'] = 'page1';
		$this->load->view('webapp/home/home.php', $this->data);
	}

	/**
	 * Event DashBoard
	 */

	public function dashboard(){
		$eventId = $this->input->get('id');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		if (empty($eventId) && $this->config->item('segmented_url')) {
			$getParams = $this->uri->uri_to_assoc(2);
			$eventId = $getParams['id'];
		}
		$this->data['event'] = $this->Events->getEvent($eventId);
		$speakers = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' => $this->roles['speaker'],
			'status' => 'approved'
		));

		$this->data['eventId'] = $eventId;
		$this->data['method'] = 'view_event';
		$this->data['speakers'] = $speakers;
		$this->data['page_id'] = 'page2';
		$this->load->view('webapp/dashboard.php',$this->data);
	}

	/**
	 * Event Details Page
	 */

	public function event_details($isCancel = FALSE){
		$eventId = $this->input->get('id');

		self::checkAuth();

		$this->data['eventId'] = $eventId;
		$this->data['event'] = $this->Events->getEvent($eventId);

		//$this->data['primaryUser'] = $_SESSION['_user'];
		$this->data['primaryUser'] = $this->userData;

		//$companions = make_new_key($this->Companion->getCompanions($_SESSION['_user']['userID']), 'user_id');
		$companions = make_new_key($this->Companion->getCompanions($this->userData['userID']), 'user_id');
		//dump($companions);
		//$users = $this->Users->getUsers();
		//$companions = array_intersect_key($users, $companions);

		$guestStatus = make_new_key($this->Guest->getGuestStatus(array(
			//'user_id' => $_SESSION['_user']['userID'],
			'user_id' => $this->userData['userID'],
			'reference_type' => 'event',
			'reference_id'	=> $eventId,
			'event_id' => $eventId
		)), 'reference_id');

		$attendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'reference_type' => 'event',
			'reference_id' => $eventId
		));
		$attendees = !empty($attendees) ? make_new_key($attendees, 'user_id') : array();

		/*$debug = $this->input->get('debug');
		if($debug){
			echo count($attendees);
			yo::log($attendees);
			yo::log($this->data['event']); exit;
		}*/
		$this->data['statusData'] = array(
			'companions' => (!empty($attendees) && !empty($companions)) ? array_intersect_key($attendees, $companions) : array(),
			'eventId'	=> $eventId
		);
		$this->data['urlCancelEventRequest'] = base_url() . $this->router->class . $this->router->controller . '/cancel_event_request?id=' . $eventId;
		$this->data['attendees'] = $attendees;
		$this->data['guestStatus'] = $guestStatus;
		$this->data['page_id'] = 'page3';

		$view = ($isCancel) ? 'webapp/cancel_event_request' : 'webapp/event_details';
		$this->load->view($view, $this->data);
	}

	/**
	 * cancel_event_request
	 * Cancel event request
	 */
	public function cancel_event_request() {
		$this->event_details(TRUE);
	}

	/**
	 * List of Event's Agenda
	 */
	public function schedule(){
		$eventId = $this->input->get('id');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		$this->output->cache(7200);
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->data['itineraries'] = $this->Itinerary->getEventItinerariesGroupByDate($eventId);
		$this->data['eventId'] = $eventId;
		$this->data['page_id'] = 'page4';
		$this->load->view('webapp/schedule.php',$this->data);
	}

	/**
	 * Agenda Information
	 */
	public function view_itinerary(){
		$eventId = $this->input->get('eid');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		$itineraryId = $this->input->get('iid');
		$breakoutId = $this->input->get('bid');
		$itinerary =  $this->Itinerary->getItinerary($itineraryId);
		$itineraryAttendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId,
			'role_id' => $this->roles['attendees'],
			'reference_type' => 'agenda',
			'reference_id' => $itineraryId
		));
		$itineraryAttendees = make_new_key($itineraryAttendees, 'user_id');
		$breakoutInfo = array();
		$breakouts = $this->Breakout->getItineraryBreakouts($itineraryId);
		$id = array_keys($breakouts);

		if($itinerary['breakout_status']) { // breakout
			if(1 == count($breakouts)) { // single breakout
				$breakoutInfo = $this->Breakout->getBreakout($id[0]);
				$attendees = $this->Guest->getAttendees(array(
					'event_id' 	=> $eventId,
					'reference_id' => $id[0],
					'reference_type' => 'activity'
				));
				$teams = $speakers = $activityAttendees = $pendingRequests = array();
				foreach($attendees as $key){
					if('pending' == $key['status']){
						$pendingRequests[$key['userID']] =  $key;
					}elseif('approved' == $key['status']) {
						if($key['role_id'] == $this->roles['attendees']) {
							$activityAttendees[$key['userID']] = $key;
						} else if($key['role_id'] == $this->roles['speaker']) {
							$speakers[$key['userID']] = $key;
						} else if($key['role_id'] == $this->roles['team']) {
							$teams[] = $key;
						}
					}
				}
				$breakoutInfo['pendingRequests'] = 	$pendingRequests;
				$breakoutInfo['speakers'] = $speakers;
				$breakoutInfo['teams'] = $teams;
				//only return attendees, if already part of speakers, don't show anymore
				$breakoutInfo['activityAttendees'] = array_diff_key($activityAttendees, $speakers);


			}else { // multiple breakout
				foreach($breakouts as &$breakout) {
					$attendees = $this->Guest->getAttendees(array(
						'event_id' 	=> $eventId,
						'reference_id' => $breakout['breakoutID'],
						'reference_type' => 'activity'
					));
					$teams = $speakers = $activityAttendees = $pendingRequests = array();
					foreach($attendees as $key){
						if('pending' == $key['status']){
							$pendingRequests[$key['userID']] =  $key;
						}elseif('approved' == $key['status']) {
							if($key['role_id'] == $this->roles['attendees'])
								$activityAttendees[$key['userID']] = $key;
							if($key['role_id'] == $this->roles['speaker'])
								$speakers[$key['userID']] = $key;
							if($key['role_id'] == $this->roles['team'])
								$teams[] = $key;
						}
					}
					$breakout['pendingRequests'] = 	$pendingRequests;
					$breakout['speakers'] = $speakers;
					$breakout['teams'] = $teams;
					$breakout['activityAttendees'] = $activityAttendees;
				}
			}

			$this->data['breakouts'] = $breakouts;
		}else {	// itinerary

			$param = array(
				'event_id' 		=> $eventId,
				'reference_id' 	=> $itineraryId,
				'reference_type' => 'agenda'
			);

			$param['status'] = 'pending';
			$pendingRequests = $this->Guest->getAttendees($param);

			$param['status'] = 'approved';
			$param['role_id'] = $this->roles['speaker'];
			$speakers = $this->Guest->getAttendees($param);

			$param['role_id'] = $this->roles['team'];
			$arrItineraryTeams = $this->Guest->getAttendees($param);
			if(!empty($arrItineraryTeams)){
				foreach ($arrItineraryTeams as $key => $row) {
				    $team[$key]  = $row['team'];
				    $last_name[$key] = $row['last_name'];
				}
				array_multisort($team, SORT_ASC, $last_name, SORT_ASC, $arrItineraryTeams);
			}

			$param['role_id'] = $this->roles['attendees'];
			$activityAttendees = $this->Guest->getAttendees($param);

			$itinerary['pendingRequests'] = make_new_key($pendingRequests, 'user_id');
			$itinerary['speakers'] = $speakers;
			$itinerary['teams'] = $arrItineraryTeams;
			$itinerary['activityAttendees'] = make_new_key($activityAttendees, 'user_id');
		}
		$eventCompanions =  $this->Guest->getGuestCompanions($this->data['userID'], $eventId);

		$mapReferences = $this->Map_Reference->fetch(array(
			'reference_type' => 'itinerary',
			'reference_id'   => $itineraryId,
		));

		$this->data['mapReference'] = !empty($mapReferences) ? current($mapReferences) : array();
		$this->data['breakout'] = $breakoutInfo;
		$this->data['selectedBreakoutId'] = $breakoutId;
		$this->data['itinerary'] = $itinerary;
		$this->data['users'] = $itineraryAttendees;
		$this->data['mapAttendees'] = $this->Users->getUsers();
		$this->data['eventId'] = $eventId;
		$this->data['eventCompanions'] = $eventCompanions;

		$this->data['page_id'] = 'page5';
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->load->view('webapp/view_itinerary.php',$this->data);
	}

	/**
	 * View Activities
	 */

	public function view_breakout(){
		self::checkAuth();
		$bid = $this->input->get('bid');
		$breakout = $this->Breakout->getBreakout($bid);
		$breakout['guests'] = $this->Guest->getGuestByReferenceID($bid);
		$users = $this->Users->getUsers();

		$speaker = $this->Guest->getEventSpeaker($bid);
		$speakerName = '';
		if(!empty($speaker)){
			$speakerId = $speaker['user_id'];
			if($speakerId) {
				$speakerName = $users[$speakerId]['first_name'].' '.$users[$speakerId]['last_name'];
			}
		}

		$this->data['speakerName'] = $speakerName;
		$this->data['breakout'] = $breakout;
		$this->data['users'] = $users;
		$this->data['eventId'] = $this->input->get('eid');
		$this->data['page_id'] = 'page6';
		$this->load->view('webapp/view_breakout.php',$this->data);
	}

	/**
	 * View  Attendees
	 */

	public function attendees(){
		$eventId = $this->input->get('id');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		$this->output->cache(7200);
		$speakers = $this->Guest->getEventSpeakers(0, $eventId);
		$speakers = make_new_key($speakers, 'user_id');
		$this->data['speakers'] = $speakers;
		$attendees = $this->Guest->getAttendees(
			array( 'event_id' => $eventId,
				   'role_id' => 3, //Attendee
				   'reference_type' => 'event',
				   'status'	=> 'approved'
		));

		//get primary users
		$primaryUserData = $this->Users->getUsers(array('sort_field' => 'email', 'is_primary' => '1'));
		$primaryUserResultData = array();
		foreach($primaryUserData as $user){
			$primaryUserResultData[$user['userID']] = $user;
		}
		$updatedAttendees = array();
		foreach ($attendees as $attendee){
			$eventCompanionAttendeeData = $this->Companion->getPrimaryUser($attendee['userID']);
			$attendee['primary_user_id'] = $eventCompanionAttendeeData[0]['primary_user_id'];
			$updatedAttendees[] = $attendee;
		};
		$this->data['users'] = 	$updatedAttendees;
		$this->data['primaryUsers'] = $primaryUserResultData;
		$this->data['eventId'] = $eventId;
		$this->data['page_id'] = 'page7';

		$this->load->view('webapp/attendees.php',$this->data);
	}

	/**
	 * View Event Maps
	 */
	public function maps() {
		$eventId = $this->input->get('id');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		$this->output->cache(7200);

		$maps = $this->Map_Photo->fetch(array('event_id' => $eventId));
		$this->data = array(
			'page_id' => 5,
			'eventId' => $eventId,
			'maps' => $maps,
			'isGoogleMap' => 1,
			'event'		=> $this->Events->getEvent($eventId)
		);

		$this->load->view('webapp/maps.php',$this->data);
	}

	/**
	 * Event Speakers
	 */

	public function speakers(){
		$eventId = $this->input->get('id');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		//$this->output->cache(7200);
		$speakers = $this->Guest->getEventSpeakers(0, $eventId);
		$this->data['speakers'] = $speakers;
		$this->data['eventId'] = $eventId;
		$this->data['page_id'] = 'page6';
		$this->load->view('webapp/speakers.php',$this->data);
	}

	/**
	 * User Information
	 */

	public function view_user() {
		$eventId = $this->input->get('eid');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		//$this->output->cache(7200);
		$userId = $this->input->get('uid');
		$fromSpeaker = $this->input->get('spr');
		$breakoutId = $this->input->get('bid');
		$itineraryId = $this->input->get('iid');

		$programs = $this->Guest->getGuestProgramsList($userId, $eventId);

		$startDate = array();
		if(!empty($programs[0])) {
			foreach($programs as &$program) {
				if(isset($program['breakoutID'])) {
					$reference_id = $program['breakoutID'];
					$reference_type = 'activity';
				}else {
					$reference_id = $program['itineraryID'];
					$reference_type = 'agenda';
				}
				if(!empty($reference_id)){
				$program['preferences'] = $this->Activity_Preference->getActivityPreference(array(
					'activityPreferenceID' => null,
					'referenceID' => $reference_id,
					'eventID' => $eventId,
					'referenceType' => $reference_type
				));
				}
				$startDate[] = $program['start_date_time'];
			}
			array_multisort($startDate,SORT_ASC, SORT_STRING ,$programs);
		}

		$pendingRequests = $this->Guest->getGuestProgramsList($userId, $eventId, 'pending');

		foreach ($pendingRequests as $key => $value) {
			if ($value == FALSE) {
				unset($pendingRequests[$key]);
			}
		}

		$userPreferences  = $this->Attendee_Activity_Preference->getAttendeeActivityPreference(array(
			'attendeeActivityPreferenceID' => null,
			'activityPreferenceID' => null,
			'activityPreferenceOptionID' => null,
			'userID' => $userId
		));

		$mapUserPreferences = array();
		if(!empty($userPreferences)) {
			foreach ($userPreferences as $pref) {
				$mapUserPreferences[$pref['activityPreferenceOptionID']][] = $pref['value'];
			}
		}


		$this->data = array(
			'userInfo' 		=> $this->Users->getFullUserInfo($userId),
			'programs' 		=> $programs,
			'companions'	=> $this->Guest->getGuestCompanions($userId, $eventId),
			'userPreferences' => $mapUserPreferences,
			'eventId'		=> $eventId,
			'breakoutId'	=> $breakoutId,
			'itineraryId'	=> $itineraryId,
			'fromSpeaker'	=> $fromSpeaker,
			'pendingRequests' => $pendingRequests,
			'page_id'		=> 'page7'
		);
		$this->load->view('webapp/view_user.php', $this->data);
	}

	/**
	 * User Photo
	 * This will resize the user avatar
	 */
	public function user_photo() {
		$userid = $this->input->get('uid', 0);
		$width = $this->input->get('width');
		$height = $this->input->get('height');
		$width = !empty($width) ? (int) $width : 48 ;
		$height = !empty($height) ? (int) $height : 48;
		$imageParams = array();
		$imageBasePath = APPPATH . '../img/';
		$uploadPath = $imageBasePath . 'upload/user/';
		$imageParams = array('imagePath'=> "{$imageBasePath}user_default.png");

		$dataPhoto = $this->User_Photo->fetch(array('fk_i_uid'=>$userid));
		if (!empty($dataPhoto)) {
			$dataPhoto = current($dataPhoto);
		}
		if (file_exists($uploadPath . $dataPhoto['s_fname']) && !empty($dataPhoto['s_fname'])) {

			$imgPath = $this->config->item('base_url') . 'img/upload/user/' . $dataPhoto['s_fname'];
		} else {
			$imgPath = $this->config->item('base_url') . 'img/user_default.png';
		}

		$im = imagecreatefromstring(file_get_contents($imgPath));



		$thumb_width    = $width;
		$thumb_height   = $height;

		$source_image = $im;
		$thumb_width = $thumb_width;
		$thumb_height = $thumb_height;

		$thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
		if($thumbnail === false) {
			return null;
		}

		$fill = imagecolorallocate($thumbnail, 151, 164, 183);
		imagefill($thumbnail, 0, 0, $fill);

		$hratio = $thumb_height / imagesy($source_image);
		$wratio = $thumb_width / imagesx($source_image);
		$ratio = min($hratio, $wratio);
		if ($ratio > 1.0)
		$ratio = 1.0;

		// Compute sizes
		$sy = floor(imagesy($source_image) * $ratio);
		$sx = floor(imagesx($source_image) * $ratio);

		// Compute margins
		// Using these margins centers the image in the thumbnail.
		// If you always want the image to the top left, set both of these to 0
		$m_y = floor(($thumb_height - $sy) / 2);
		$m_x = floor(($thumb_width - $sx) / 2);

		// Copy the image data, and resample
		// If you want a fast and ugly thumbnail, replace imagecopyresampled with imagecopyresized
		if (!imagecopyresampled($thumbnail, $source_image,
		$m_x, $m_y, //dest x, y (margins)
		0, 0, //src x, y (0,0 means top left)
		$sx, $sy,//dest w, h (resample to this size (computed above)
		imagesx($source_image), imagesy($source_image)) //src w, h (the full size of the original)
		) {
			//copy failed
			imagedestroy($thumbnail);
			return null;
		}

		/* Set the new file name */
		//$thumbnail_file_name = $file;
		//header('HTTP/1.0 404 not found');

		header('Content-Type: image/jpg');
		imagejpeg($thumbnail);
		imagedestroy($thumbnail);
		exit;
	}

	/**
	 * User Account
	 * Update User Information
	 */
	public function user_account(){
		self::checkAuth();
		//$sessionUserData = $_SESSION['_user'];
		$sessionUserData = $this->userData;

		$userInfo =  $this->Users->getUser($sessionUserData['userID']);
		$this->data['userData'] = $userInfo;

		// user update handling
		$postdata = $this->input->post();
		$update_success = false;

		if(!empty($postdata)){
			//change password handling
			$error = '';
			if (isset($postdata['old_password'])) {
				$objEventClient = new api_user_client();
				$userData = $objEventClient->login_get(array('email'=>$postdata['email'], 'password'=>$postdata['old_password']));
				if (!empty($userData['result'])) {
					if (empty($postdata['new_password']))
						$error = 'password is empty';
					else if ($postdata['new_password'] != $postdata['new_password2'])
						$error = 'password mismatch/new password empty';
					else
						$postdata['password'] =  $postdata['new_password'];
				} else {
					$error = $userData['error'];
				}
			}
			if (empty($error)) {
				$user = $this->Users->addUser($postdata);
				//update session
				$userInfo =  $this->Users->getUser($sessionUserData['userID']);
				$this->session->set_userdata('_user', $userInfo);
				$update_success = true;
				$this->data['userData'] = $this->session->userdata('_user');
			}

			/*begin uploading photo*/
			if (!empty($_FILES['user_photo']['tmp_name']) && $this->config->config['is_allow_user_photo']  && empty($error)) {
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
			if (!empty($postdata['s_remove_photo']) && !empty($postdata['userPhotoID']) && !empty($postdata['s_current_photo']) && empty($error)) {

				$result = $this->User_Photo->deletePhoto($postdata['userPhotoID']);
				if ($result) {
					$uploadPath = $this->config->item('upload_path') . 'user/';
					$filename = $uploadPath . $postdata['s_current_photo'];
					if (file_exists($filename)) {
						unlink($filename);
					}
				}
			}

			$this->data['error'] = $error;
		}

		$userInfo = $userPhoto = array();
		if(isset($sessionUserData['userID'])) {
			$userInfo =  $this->Users->getUser($sessionUserData['userID']);
			if (!empty($sessionUserData['userID'])) {
				$userPhoto = $this->User_Photo->getPhoto($sessionUserData['userID']);
				$userPhoto = make_new_key($userPhoto, 'fk_i_uid');
				$userInfo['uploaded_photo'] = $userPhoto;
			}
			$this->data['userInfo'] = $userInfo;
		}

		$this->data['update_success'] = $update_success;
		$this->data['page_id'] = 'user-account';
		$this->load->view('webapp/user_account', $this->data);
	}

	/**
	 * Forgot Password
	 * Facility to send password reset link
	 */

	public function forgot_password(){
		$errorMsg = $successMsg =  '';
		$email = $this->input->post('email');

		if (!empty($email)) {
			$bUserExist = false;
			$arrUsers = $this->Users->getUsers();
			foreach ($arrUsers as $user) {
				if ($email == $user['email']) {
					$bUserExist = true;
					$userID = $user['userID'];
				}
			}
			if ($bUserExist) {
				$successMsg = 'Instructions for signing in have been emailed to you';
				$activationKeyData = $this->Activation_Key->addActivationKey($userID);
				$this->Users->sendForgetPasswordEmail($userID, $activationKeyData['result']);
			} else {
				$errorMsg = 'Sorry, we couldn\'t find anyone with that email address';
			}
		}

		$this->data['page_id'] = 'reset-password';
		$this->data['errorMsg'] = $errorMsg;
		$this->data['successMsg'] = $successMsg;
		$this->load->view('webapp/forgot_password.php', $this->data);
	}

	/**
	 * Reset Password
	 * Email Landing Page for reset password
	 */
	public function reset_password(){
		$user_id = $this->input->get('user_id');
		$key = $this->input->get('key');

		$res = $this->Activation_Key->getActivationKey(array('user_id' => $user_id, 'key' => $key));
		$activationKeyInfo = $res['result'];
		$this->data['activationKeyInfo'] = $activationKeyInfo;

		//activation key checker
		$this->load->helper('url');

		if (empty($activationKeyInfo)) // check if existing
			redirect('/webapp/password_reset_expired');

		if (1 == $activationKeyInfo['status']) //check if used
			redirect('/webapp/password_reset_expired');

		$currentTime = strtotime(date("Y-m-d H:i:s"));


		$key_created_date    = new DateTime($activationKeyInfo['created_at']);
		$current_date = new DateTime(date("Y-m-d H:i:s"));
		$diff = $key_created_date->diff($current_date);

		if (24<$diff->format('%h')) //check if expired - limitation of 24 hours
			redirect('/webapp/password_reset_expired');



		$userInfo =  $this->Users->getUser($user_id);
		$this->data['userInfo'] = $userInfo;

		// password update handling
		$postdata = $this->input->post();
		$update_success = false;

		$errorMsg = '';

		if(!empty($postdata)){
			if (empty($postdata['new_password'])) {
						$errorMsg = 'password is empty';
			} else if ($postdata['new_password'] != $postdata['new_password2']) {
				$errorMsg = 'password mismatch/new password empty';
			} else if (strlen($postdata['new_password']) <6 ) {
				$errorMsg = 'password must be at least  6 characters';
			}
			else {
				session_start();

				$userData =  $this->Users->getUser($user_id);
				$userData['password'] = $postdata['new_password'];
				$this->Users->addUser($userData);

				//update status of activation key
				$activationKeyInfo['status'] = 1;
				$this->Activation_Key->updateActivationKey($activationKeyInfo);

				//$_SESSION['username'] = $userData['email'];
				//$_SESSION['_user'] =$userData;
				$this->session->set_userdata('email',$userData['email']);
				$this->session->set_userdata('_user',$userData);

				redirect('/webapp/');
			}
		}


		$this->data['errorMsg'] = $errorMsg;

		$this->data['page_id'] = 'reset-password-form';
		$this->load->view('webapp/reset_password.php', $this->data);
	}

	/**
	 * Activate User Registration
	 */
	public function activate_account(){
		$user_id = $this->input->get('user_id');
		$key = $this->input->get('key');

		$res = $this->Activation_Key->getActivationKey(array('user_id' => $user_id, 'key' => $key));
		$activationKeyInfo = $res['result'];
		$this->data['activationKeyInfo'] = $activationKeyInfo;

		//activation key checker
		$this->load->helper('url');

		if (empty($activationKeyInfo)) { // check if existing
			redirect('/webapp/password_reset_expired');
		} else {
			//activate account
			session_start();
			$userData =  $this->Users->getUser($user_id);
			$userData['active'] = '1';

			//needed these codes in order to bypass the password update
			unset($userData['password']);
			unset($userData['salt']);

			$this->Users->addUser($userData);

			//update status of activation key
			$activationKeyInfo['status'] = 1;
			$this->Activation_Key->updateActivationKey($activationKeyInfo);

			//$_SESSION['username'] = $userData['email'];
			//$_SESSION['_user'] =$userData;

			$this->session->set_userdata('email',$userData['email']);
			$this->session->set_userdata('_user',$userData);

			redirect('/webapp/');
		}
	}

	/**
	 * Page Notification the Password Reset key is expired
	 */
	public function password_reset_expired(){
		$this->data['page_id'] = 'reset-password-expired';
		$this->load->view('webapp/password_reset_expired.php', $this->data);
	}

	/**
	 * Account Activition Denied
	 */
	public function activation_account_denied(){
		$this->data['page_id'] = 'activation-account-denied';
		$this->load->view('webapp/activation_account_denied', $this->data);
	}

	/**
	 * Join Event
	 */
	public function join() {
		self::checkAuth();


		$userId = $this->userData['userID'];
		$eventId = $this->input->post('eid');
		$refType = $this->input->post('rtype');
		$refId = $this->input->post('rid');

		// get event owners
		$eventOwners = $this->Event_Owner->getEventOwners(array(
				'event_id' => $eventId,
		));

		$response = $this->Guest->getGuestStatus(array(
			'user_id' =>  $userId,
			'event_id' => $eventId,
			'reference_type' 	=> $refType,
			'reference_id'		=> $refId
		));

		if(empty($response)){
			$this->Guest->join(array(
				'user_id' =>  $userId,
				'event_id' => $eventId,
				'reference_type' 	=> $refType,
				'reference_id'		=> $refId,
				'role_id' => 3 //By default, role is attendee
			));

			// SEND OWNER EMAIL NOTIFICATION FROM PRIMARY GUEST
			if (count($eventOwners) > 0) {
				$registrant = $this->Users->getUser($userId);
				$event = $this->Events->getEvent($eventId);
				foreach ($eventOwners as $event_owner) {
					$this->Users->sendJoinNotificationToOwners($event_owner, $registrant, $event);
				}
			}
			// END EMAIL NOTIFICATION

			//Join companions if there's selected
			foreach($this->input->post('companions') as $companionId) {
				$this->Guest->join(array(
					'user_id' =>  $companionId,
					'event_id' => $eventId,
					'reference_type' 	=> $refType,
					'reference_id'		=> $refId,
					'role_id' => 3 //By default, role is attendee
				));
			}
		} else {
			$event_attendee = current($response);

			$this->Guest->join($event_attendee);

			// SEND OWNER EMAIL NOTIFICATION FROM PRIMARY GUEST
			if (count($eventOwners) > 0) {
				$registrant = $this->Users->getUser($userId);
				$event = $this->Events->getEvent($eventId);
				foreach ($eventOwners as $event_owner) {
					$this->Users->sendJoinNotificationToOwners($event_owner, $registrant, $event);
				}
			}
			// END EMAIL NOTIFICATION

			//Join companions if there's selected
			foreach($this->input->post('companions') as $companionId) {
				$rejectedCompanion = $this->Guest->getGuestCompanions($userId, $eventId, 'rejected');

				if ($rejectedCompanion[$companionId]) {
					$params = array(
								'eventAttendeeIDs' => $rejectedCompanion[$companionId]['eventAttendeeID'],
								'status' => 'pending'
								);
					$this->Guest->updateStatus($params);
				} else {
					$this->Guest->join(array(
						'user_id' =>  $companionId,
						'event_id' => $eventId,
						'reference_type' 	=> $refType,
						'reference_id'		=> $refId,
						'role_id' => 3 //By default, role is attendee
					));
				}
			}
		}


		//refresh is already done on client-side
		return;
	}

	/**
	 * Cancel Join Event
	 */
	public function cancel_join() {
		self::checkAuth();
		$postData = $this->input->post();

		//-- Cancel primary user if checked
		if(isset($postData['primary_user']) && !empty($postData['primary_user'])) {
			$result = $this->Guest->cancelJoin(array(
				'user_id' 	=> $postData['primary_user'],
				'event_id' 	=> $postData['eid'],
				'reference_type'	=> $postData['rtype'],
				'reference_id'		=> $postData['rid'],
			));

		}
		//-- Cancel also user's companions request
		if(isset($postData['companions']) && !empty($postData['companions'])) {
			$companions = $postData['companions'];
			foreach($companions as $companionId) {
				$this->Guest->cancelJoin(array(
					'user_id' 	=> $companionId,
					'event_id' 	=> $postData['eid'],
					'reference_type' 	=> $postData['rtype'],
					'reference_id'		=> $postData['rid'],
				));
			}
		}


		//refresh is already done on client-side
		return;
	}

	/**
	 * User Registration
	 */
	public function registration(){
		$error = array();
		$successMsg =  '';

		$postdata = $this->input->post();

		if(!empty($postdata)){
			$postdata['active'] = '0'; //set status to 0 - inactive

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

					$postdata['userID'] = $temp_uid;

					$paramsPhoto = array(
								's_fname'=>$filename,
								'fk_i_uid' => $temp_uid,
								's_origdata' => serialize($imgData),
								'b_is_primary' => 1,
								'b_is_deleted' => 0
					);

					$dataPhoto = $this->User_Photo->addPhoto($paramsPhoto);
				}

			} else {
				$user = $this->Users->addUser($postdata);
			}

			if (!$error) {
				$successMsg = 'Account created. Please check your email to verify your account.';

				//create activation key and send email
				$activationKeyData = $this->Activation_Key->addActivationKey($user['result']['userID']);
				$this->Users->sendActivateAccountEmail($user['result']['userID'], $activationKeyData['result']);
			}
		}
		$this->data['page_id'] = 'user-registration';
		$this->data['errorMsg'] = $error;
		$this->data['successMsg'] = $successMsg;
		$this->load->view('webapp/registration.php', $this->data);
	}

	/**
	 * Manage Companion
	 */
	public function manage_companion() {
		self::checkAuth();
		$companions = $error = array();
		//$sessionUserData = $_SESSION['_user'];
		$sessionUserData = $this->userData;
		$userId = $sessionUserData['userID'];
		$save_success = false;

		$uid  = $this->input->get('uid');
		$postdata = $this->input->post();

		if(!empty($postdata)){
			$postdata['primary_user_id']  = $userId;

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
						$save_success = true;
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

		if (empty($error) &&  !$save_success && !empty($postdata)) {
			$user = $this->Users->addUser($postdata);
			$this->data['user_data'] = $user;
			$save_success = true;
		}

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

		$companions =  make_new_key($this->Companion->getCompanions($userId), 'user_id');
		$users = $this->Users->getUsers();

		//dump($companions);	exit;
		$this->data = array(
			'companions'	=> array_intersect_key($users, $companions),
			'userInfo'		=> $userInfo ,
			'page_id'		=> 'manage-companion',
			'save_success'		=> $save_success,
			'error'		=> $error,
		);
		$this->load->view('webapp/manage_companion.php', $this->data);
	}

	/**
	 * Log out
	 */
	public function logout() {

		//if(!empty($_SESSION['_user']))
		//	unset($_SESSION['_user']);

		$userSession = $this->session->userdata('_user');

		if(!empty($userSession)) {
			$this->session->unset_userdata('_user');
		}

		redirect('/webapp/login');
	}

	/**
	 * Email Exists Validation
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
	 * Notify Cancel Join
	 */
	public function notify_cancel_join() {
		$this->data['eventId'] = $this->input->get('eid');
		$this->load->view('webapp/event_details/notify_cancel_join.php', $this->data);
	}

	/**
	 * Map Details
	 */
	public function map_details()
	{
		$eventId = $this->input->get('eid');
		$mapId = $this->input->get('mid');
		$map = array();

		if($this->input->get('isGoogleMap')) {
			//Do google map coordinates here
			$map[] = array('title' => 'Google Map');
		} else {
			$map = $this->Map_Photo->fetch(array('mapPhotoID' => $mapId));
		}


		$this->data = array(
			'eventId'	=> $eventId,
			'mapId'		=> $mapId,
			'page_id'	=> '', //This should be deprecated
			'map'	=> !empty($map) ? current($map) : array(),
		);

		$this->load->view('webapp/map_details', $this->data);

	}


	/**
	 * Checks authorization if user is authorized to view a page
	 *
	 * @param string	action/method name
	 * @param array	options
	 */
	private function checkAuth($action = '', $params = array()) {
		session_start();
		$userSession = $this->session->userdata('_user');

		if(empty($userSession)) {
			redirect('/webapp/login');
			exit;
		} else {
			$this->userData = $userSession;
			$this->data['userID'] = $this->userData['userID'];
		}
		//For auth options
		if(!empty($action) && method_exists($this->WebApp_Auth, $action)) {
			if( !$this->WebApp_Auth->$action($params, $this->userData) ) {
				redirect('/webapp/');
				exit;
			}
		}

	}

	/**
	 * Activity/Agenda Join
	 */
	public function request_join(){
		$activityID = $this->input->get('id');
		$userID = $this->input->get('uid');
		$eventID = $this->input->get('eid');
		$referenceType = $this->input->get('rtype');
		$primaryUserID = $this->input->get('puid');

		$params = array(
			'eventAttendeeID' => '',
			'event_id' => $eventID,
			'user_id' => $userID,
			'reference_type' => $referenceType,
			'reference_id' => $activityID,
			'role_id' => $this->roles['attendees'],
			'team' => '',
			'status' => 'approved'
		);
		$this->data['primaryUserID'] = $primaryUserID;
		$response = $this->Guest->addEventGuest($params);
		/*if(!empty($response)){
			$response = $response[0];
			$response = array_merge($response, $this->Users->getUser($response['user_id']));
		}*/

		$this->data['response'] = $response;
		//$this->load->view('webapp/request_join.php', $this->data);
		echo json_encode($response);
		exit;
	}

	/**
	 * Cancel Activity / Agenda Join
	 */

	public function cancel_request_join(){
		$eventAttendeeID = $this->input->get('id');
		$params = array(
			'eventAttendeeID' => $eventAttendeeID
		);

		$record =$this->Guest->getAttendees($params);
		$this->Guest->deleteGuest($params);

		$this->data['primaryUserID'] =  $this->input->get('puid');
		$this->data['response'] = $record[0];
		$this->load->view('webapp/cancel_request_join.php', $this->data);
	}

	/**
	 * Attendee accepts invite to be a speaker
	 */
	public function accept_speaker_invite(){
		self::checkAuth();
		$userId = $this->input->get('uid');
		$eventId = $this->input->get('eid');
		$referenceId = $this->input->get('id');
		$referenceType = $this->input->get('rtype');
		$roleId = $this->roles['speaker'];

		$this->Guest->joinSpeaker($data = array(
				'user_id' => $userId ,
				'event_id' => $eventId,
				'reference_id' => $referenceId,
				'role_id' => $roleId,
				'reference_type' => $referenceType,
				'status' => 'approved'));
		$this->_email_notify_owner_invite($userId, $eventId, $referenceId, 'approved', '', $referenceType);

		return;
	}

	/**
	 * Attendee cancels invite to be a speaker
	 */
	public function cancel_speaker_invite(){
		self::checkAuth();
		$userId = $this->input->get('uid');
		$event_id = $this->input->get('eid');
		$reference_id = $this->input->get('id');
		$comment = $this->input->get('comment');
		$referenceType = $this->input->get('rtype');
		$role_id = $this->roles['speaker'];

		$this->_email_notify_owner_invite($userId, $event_id, $reference_id, 'declined', $comment);
		$this->Guest->cancelJoinSpeaker($data = array(
				'user_id' => $userId ,
				'event_id' => $event_id,
				'reference_id' => $reference_id,
				'role_id' => $role_id,
				'comment' => $comment,
				'reference_type' => $referenceType,
				'status' => 'rejected'));

		return;
	}

	/**
	 * Displays speaker confirmation page
	 */
	public function speaker_confirmation(){
		$backUrl = '';
		$eventId = $this->input->get('id');

		if (empty($_GET)) {
			self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		} else {
			$backUrl = urlencode(trim(base_url(), '/') . $_SERVER['REQUEST_URI']);
		}


		$userSession = $this->session->userdata('_user');
		if(empty($userSession)) {
			if (empty($backUrl)) {
				$this->logout();
			} else {
				redirect('/webapp/login?back_url=' . $backUrl);
			}
		} else {
			$userId = $userSession['userID'];
		}

		$userInfo = $this->Users->getUser($userId);
		$getUserId = $this->input->get('uid');

 		if ($getUserId != $userId){
 			$this->logout();
 		}else{
			$referenceId = $this->input->get('rid');
			$referenceType = $this->input->get('rtype');
			$itineraryId = $this->input->get('iid');
			$userInfo =  $this->Users->getUser($userId);
			$this->data['eventId'] = $eventId;
			$this->data['referenceType'] = $referenceType;
			$this->data['referenceId'] = $referenceId;
			$this->data['itineraryId'] = $itineraryId;
			$this->data['event'] = $this->Events->getEvent($eventId);
			$this->data['user'] = $userInfo;
			$results = $this->Guest->getPendingItinerary($userId, $eventId, $referenceId, $referenceType, $itineraryId);

			$itineraries = array();
			if (!empty($results)) {
				foreach ($results as $key => $value) {
					$dateStart = date('Y/m/d', strtotime(str_replace('-', '/', $value['start_date_time'])));
					$itineraries[$dateStart][] = $value;
				}
			}
			$this->data['itineraries'] = $itineraries;
			$this->data['page_id'] = 'page4';
			$this->load->view('webapp/speaker_confirmation.php', $this->data);
 		}
	}

	/**
	 * My Activities
	 */
	public function my_activities(){
		$eventId = $this->input->get('eid');
		self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		$userID =  $this->data['userID'];

		$userEvents = $this->Guest->getGuestEvents($userID);

		foreach($userEvents as &$userEvent) {
			$programs = $this->Guest->getGuestProgramsList($userID, $userEvent['eventID']);

			$startDate = array();
			if(!empty($programs[0])) {
				foreach($programs as &$program) {
					if(isset($program['breakoutID'])) {
					$reference_id = $program['breakoutID'];
					$reference_type = 'activity';
					}else {
						$reference_id = $program['itineraryID'];
						$reference_type = 'agenda';
					}
					if(!empty($reference_id)){
					$program['preferences'] = $this->Activity_Preference->getActivityPreference(array(
						'activityPreferenceID' => null,
						'referenceID' => $reference_id,
						'eventID' => $eventId,
						'referenceType' => $reference_type
					));
					}
					$startDate[] = $program['start_date_time'];
				}
				array_multisort($startDate,SORT_ASC, SORT_STRING ,$programs);
			}

			$pendingRequests = $this->Guest->getGuestProgramsList($userID, $userEvent['eventID'], 'pending');

			foreach ($pendingRequests as $key => $pendingRequest) {
				// get reference type on itinerary table
				if (is_array($pendingRequest)) {
					if (array_key_exists('breakoutID', $pendingRequest)) {
						$breakoutDetails = $this->Breakout->getBreakout($pendingRequest['breakoutID']);

						$pendingRequests[$key]['itinerary_id']   = $breakoutDetails['itinerary_id'];
						$pendingRequests[$key]['reference_type'] = 'activity';
						$pendingRequests[$key]['event_id']       = $userEvent['eventID'];
					} else {
						$itineraryId = $pendingRequest['itineraryID'];
						$itinerary = $this->Guest->getGuestByReferenceID($itineraryId);

						if (!empty($itinerary)) {
							$pendingRequests[$key]['reference_type'] = $itinerary[$userID]['reference_type'];
						}
					}
					$pendingRequests[$key]['user_id'] = $userID;
				}
			}
			$userEvent['programs']        = $programs;
			$userEvent['pendingRequests'] = $pendingRequests;
			$userEvent['companions']      = $this->Guest->getGuestCompanions($userID, $userEvent['eventID']);
		}

		$userPreferences  = $this->Attendee_Activity_Preference->getAttendeeActivityPreference(array(
			'userID' => $userID,
			'attendeeActivityPreferenceID' => null,
			'activityPreferenceID' => null,
			'activityPreferenceOptionID' => null
		));

		$mapUserPreferences = array();
		if(!empty($userPreferences)) {
			foreach ($userPreferences as $pref) {
				$mapUserPreferences[$pref['activityPreferenceOptionID']][] = $pref['value'];
			}
		}

		foreach ($userEvents as $key => $value) {
			if (!empty($value['pendingRequests'])) {
				foreach ($value['pendingRequests'] as $k => $v) {
					if ($v == FALSE) {
						unset($userEvents[$key]['pendingRequests'][$k]);
					}
				}
			}
			if (!empty($value['programs'])) {
				foreach ($value['programs'] as $k => $v) {
					if ($v == FALSE) {
						unset($userEvents[$key]['programs'][$k]);
					}
				}
			}
		}

		$this->data = array(
			'userInfo'        => $this->Users->getFullUserInfo($userID),
			'userEvents'      => $userEvents,
			'userPreferences' => $mapUserPreferences,
			'page_id'         => 'page7'
		);

		$this->load->view('webapp/my_activities.php', $this->data);

	}

	public function join_companion()
	{
		$eventId = $this->input->get('id');
		self::checkAuth();

		$this->data['eventId'] = $eventId;
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->data['primaryUser'] = $this->userData;

		$companions = make_new_key($this->Companion->getCompanions($this->userData['userID']), 'user_id');
		$users = $this->Users->getUsers();
		$this->data['companions'] = array_intersect_key($users, $companions);

		$this->data['page_id'] = 'page3';
		$this->load->view('webapp/home/join_companion.php', $this->data);
	}

	public function add_companion()
	{
		self::checkAuth();
		$postData = $this->input->post();
		$postData['primary_user_id'] = $this->userData['userID'];
		//-- force this to always to false
		$postData['is_primary'] = 0;
		$user =  $this->Users->addUser($postData);
		exit;
	}

	public function presentations()
	{
		$presentations = array();
		$this->load->model('presentation');
		$this->load->model('presentation_category');

		$eventId = $this->input->get('id');
		self::checkAuth();

		$presentationCategories = $this->presentation_category->getPresentationCategories(array('event_id' => $eventId));

		foreach ($presentationCategories as $key => $value) {
			$presentations[] = $this->presentation->getPresentations(array('presentation_category_id' => $value['presentationCategoryID']));
		}
		$this->data['eventId'] 	= $eventId;

		$this->data['presentationCategories'] = $presentationCategories;
		$this->data['presentations'] 		  = $presentations;

		$this->load->view('webapp/presentations', $this->data);
	}

	public function presentation_category()
	{
		$presentations = array();
		$this->load->model('presentation');
		$this->load->model('presentation_category');

		$presentationCategoryID = $this->input->get('id');
		$eventId = $this->input->get('eventId');
		self::checkAuth();

		$presentations = $this->presentation->getPresentations(array('presentation_category_id' => $presentationCategoryID));
		$presentationCategories = $this->presentation_category->getPresentationCategories(array('presentationCategoryID' => $presentationCategoryID));

		$updated_presentations = array();

		foreach ($presentations as $presentation) {
			$temp = $presentation;

			$upload_path = base_url(). str_replace('application', '', $this->presentation->getUploadPath());

			if ($temp['display_type'] != 'url') {
				$document_meta = unserialize($temp['document_meta']);
				$temp['url'] = $upload_path .  '/' . $document_meta['file_name'];
			}
			$updated_presentations[] = $temp;
		}

		$this->data['eventId'] 	= $eventId;
		$this->data['presentations'] = $updated_presentations;
		$this->data['presentationCategories'] = $presentationCategories;

		$this->load->view('webapp/presentation_category', $this->data);
	}

	/**
	 * _email_notify_owner_invite
	 * Notify event owner if speaker accepted or declined speaker invitation via email
	 */
	private function _email_notify_owner_invite($userId, $eventId, $referenceId, $status, $comment = '', $referenceType = 'agenda') {
		$userInfo =  $this->Users->getUser($userId);
		$guestSpeakerName = $userInfo['first_name'] . ' ' . $userInfo['last_name'];

		$eventInfo = $this->Events->getEvent($eventId);
		$eventOwner = $this->Event_Owner->getEventOwners(array('event_id' => $eventId));

		switch ($referenceType) {
			case 'agenda':
				$itineraryInfo = $this->Itinerary->getItinerary($referenceId);
				break;
			default:
				$itineraryInfo = $this->Breakout->getBreakout($referenceId);
				break;
		}


		foreach ($eventOwner as $value) {
			$eventOwnerName = $value['first_name'] . ' ' . $value['last_name'];

			$ihtml = array(
				'eventOwner' 		=> $eventOwnerName,
				'eventTitle' 		=> $eventInfo['title'],
				'speaker' 			=> $guestSpeakerName,
				'comment'			=> $comment,
				'itineraryTitle' 	=> $itineraryInfo['title'],
				'itineraryStart' 	=> date_format(new DateTime($itineraryInfo['start_date_time']), 'F d, Y g:i a'),
				'itineraryEnd' 		=> date_format(new DateTime($itineraryInfo['end_date_time']), 'F d, Y g:i a'),
				'itineraryLocation' => $itineraryInfo['location']
			);
			$emailTemplate = ($status == 'approved') ? 'accept_speaker_invite' : 'decline_speaker_invite';
			$recipient = $value['email'];

			$this->Email_Services->genericEmailInvite($ihtml, $emailTemplate, $recipient);
		}
	}
}