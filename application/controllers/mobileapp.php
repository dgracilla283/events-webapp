<?php
require_once APPPATH . 'libraries/clients/api_user_client.php';

class Mobileapp extends CI_Controller {

	private $data = array();
	private $userData = array();

	public function __construct()
	{
		parent::__construct();
		//$this->output->cache(7200);
		$this->load->helper('url');
		if (! isset($this->Events)) {
			$this->load->model('Events', '', TRUE);
		}
		if (! isset($this->Itinerary)) {
			$this->load->model('Itinerary', '', TRUE);
		}
		if (! isset($this->Breakout)) {
			$this->load->model('Breakout', '', TRUE);
		}
		if (! isset($this->Users)){
			$this->load->model('Users', '', TRUE);
		}
		if (! isset($this->Role)){
			$this->load->model('Role', '', TRUE);
		}
		if (! isset($this->Guest)){
			$this->load->model('Guest', '', TRUE);
		}
		if (! isset($this->User_Photo)){
			$this->load->model('User_Photo', '', TRUE);
		}

		if (! isset($this->Activation_Key)){
			$this->load->model('Activation_Key', '', TRUE);
		}

		//load roles
		$roles = $this->Role->getRoles();
		if($roles){
			foreach($roles as $role){
				$this->roles[strtolower($role['title'])] = $role['roleID'];
			}
		}
	}
	
	public function testing()
	{
		$data = array();
		
		
		$users[101]['user_id'] = "101";
		$users[101]['email'] = "test@rcggs.com";
		$users[101]['first_name'] = "Test First Name";
		$users[101]['last_name'] = "Test Last Name";
		$users[101]['affiliation'] = "Test Affiliation";
		$users[101]['industry'] = "Test Industry";
		$users[101]['title'] = "Test Title";
		$users[101]['bio'] = "Test Bio";
		$users[101]['created_at'] = "2013-11-29";
		$users[101]['updated_at'] = "2013-11-29";
		$users[101]['is_primary'] = "1";
		$users[101]['active'] = "1";
		$users[101]['userPhotoID'] = "58";
		$users[101]['fk_i_uid'] = "35";
		$users[101]['s_fname'] = "09ec46ed4b7dc80695739b3c66aa82429794855c.jpg";
		$users[101]['s_origdata'] = "";
		$users[101]['b_is_primary'] = "1";
		$users[101]['b_is_deleted'] = "0";
		$userActivities = array();
 		$userActivities['30']['event_itineraries'] = array('140','142');
  			$eventBreakouts = array();
  			$eventBreakouts['105']['reference_id'] = "105";
  			$eventBreakouts['105']['role_id'] = "4";
  			$eventBreakouts['105']['team'] = "EVP Marketing";
  			$eventBreakouts['105']['status'] = "approved";
 		$userActivities['30']['event_breakouts'] = $eventBreakouts;
 			$eventPreferences = array();
 				$eventPreferenceDetail = array();
 				$eventPreferenceDetail['attendeeActivityPreferenceID'] = "66";
 				$eventPreferenceDetail['activityPreferenceID'] = "353";
 				$eventPreferenceDetail['activityPreferenceOptionID'] = "26";
 				$eventPreferenceDetail['userID'] = "35";
 				$eventPreferenceDetail['value'] = "75";
 				$eventPreferenceDetail['dateCreated'] = "2013-02-25 07:37:29";
 				$eventPreferenceDetail['dateUpdated'] = "2013-11-29 07:37:29";
 			$eventPreferences['353']['26'] = $eventPreferenceDetail;
 		$userActivities['30']['event_preferences'] = $eventPreferences;
 		
  		$users[101]['activities'] = $userActivities;
 		$data['records']['users'] = $users;
 		
		$companions = array();
		$companions[150]['companionID'] = 16;
		$companions[150]['primary_user_id'] = 16;
		$companions[150]['user_id'] = 150;
		$companions[150]['type'] = 'adult';
		
		//$data['records']['companions'] = $companions;
		
		//$events[]
		//$data['records']['events'] = $events;
		
		$this->data['result'] = $data;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
	
	public function login()
	{
		session_start();
		$_SESSION = array();

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$this->session->set_userdata('username', $username);
		$_SESSION['username'] = $username;
		$objEventClient = new api_user_client();
		$userInfo = array();
		$data = array();
		if (!empty($username) && !empty($password)) {
			$userData = $objEventClient->login_get(array('email'=>$username, 'password'=>$password));

			if (!empty($userData['result'])) {
				$_SESSION['_user'] = $userData['result'];
				$userInfo['result'] = $userData['result'];
				//redirect('/webapp/');
			} else if (!empty($userData['error'])) {
				//$data['loginErrorMsg'] = $userData['error'];
				$userInfo['result'] = $userData['error'];
			}
		}
		$this->data['username'] = $this->session->userdata('username');
		$this->data['result'] = $userInfo;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
	
	public function reset_password_mobile()
	{
		$errorMsg = $successMsg =  '';
		$email = $this->input->post('username');
		$userInfo = array();

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
				$jsonInfo['result'] = array('result' => $successMsg);
			} else {
				$errorMsg = 'Sorry, we couldn\'t find anyone with that email address';
				$jsonInfo['result'] = $errorMsg;
			}
		}
		
		$this->data['result'] = $jsonInfo;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}

	public function register() {
		$captha_len = 5;
		$word = '';
		$i = 0;
		$this->load->helper('captcha');
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		while ($i < $captha_len) {
			$word .= $chars{mt_rand(0,(strlen($chars)) - 1 )};
			$i++;
		}
		$vals = array(
        	'word'	 => $word,
            'img_path'   => './img/captcha/',
            'img_url'    => '/img/captcha/',
            'font' => BASEPATH.'fonts/texb.ttf',
            'img_width'  => 150,
            'img_height' => 30,
            'expiration' => 7200,
            "time" => time()
		);

		$this->data['cap'] = create_captcha($vals);

		$cap = array(
            'captcha_time'  => $this->data['cap']['time'],
            'ip_address'    => $this->input->ip_address(),
            'word'   => $this->data['cap']['word']
		);
		$this->data['page_id'] = $this->uri->segment(2);
		$this->load->view('mobileapp/register_mobile.php', $this->data);
	}

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
			//$imageParams = array('imagePath'=> $uploadPath . $dataPhoto['s_fname']);
		}
		if (file_exists($uploadPath . $dataPhoto['s_fname']) && !empty($dataPhoto['s_fname'])) {

			$imgPath = $this->config->item('base_url') . 'img/upload/user/' . $dataPhoto['s_fname'];
			//$imgPath = $uploadPath . $dataPhoto['s_fname'];
		} else {
			$imgPath = $this->config->item('base_url') . 'img/user_default.png';
		}

		$im = imagecreatefromstring(file_get_contents($imgPath));
		/* // Get original width and height
		 $width = imagesx($im);
		 $height = imagesy($im);

		 // New width and height
		 $new_width = 60;
		 $new_height=ceil((($new_width/$width))*$height);
		 $image_resized = imagecreatetruecolor($new_width, $new_height);
		 imagecopyresampled($image_resized, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		 header('Content-Type: image/jpg');
		 imagejpeg($image_resized);
		 imagedestroy($image_resized); */


		$thumb_width    = $width;
		$thumb_height   = $height;

		$source_image = $im;
		$thumb_width = $thumb_width;
		$thumb_height = $thumb_height;

		// Create the image, of the required size
		$thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
		if($thumbnail === false) {
			//creation failed -- probably not enough memory
			return null;
		}

		// Fill the image with a white color (this will be visible in the padding around the image,
		// if the aspect ratios of the image and the thumbnail do not match)
		// Replace this with any color you want, or comment it out for black.
		// I used grey for testing =)
		$fill = imagecolorallocate($thumbnail, 151, 164, 183);
		imagefill($thumbnail, 0, 0, $fill);

		// Compute resize ratio
		$hratio = $thumb_height / imagesy($source_image);
		$wratio = $thumb_width / imagesx($source_image);
		$ratio = min($hratio, $wratio);

		// If the source is smaller than the thumbnail size,
		// Don't resize -- add a margin instead
		// (that is, dont magnify images)
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

	public function user_account(){
		//self::checkAuth();
		//$sessionUserData = $_SESSION['_user'];

		$userID = $this->input->get('userId');

		$userInfo =  $this->Users->getUser($userID);

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
				$userInfo =  $this->Users->getUser($userID);
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
		if(isset($userID)) {
			$userInfo =  $this->Users->getUser($userID);
			if (!empty($userID)) {
				$userPhoto = $this->User_Photo->getPhoto($userID);
				$userPhoto = make_new_key($userPhoto, 'fk_i_uid');
				$userInfo['uploaded_photo'] = $userPhoto;
			}
			$this->data['userInfo'] = $userInfo;
		}

		$this->data['update_success'] = $update_success;
		$this->data['page_id'] = 'user-account';
		$this->load->view('mobileapp/user_account_mobile', $this->data);
	}


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
		$this->load->view('mobileapp/forgot_password_mobile.php', $this->data);
	}

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

		if (2<$diff->format('%h')) //check if expired - limiatio of 2 hours
		redirect('/webapp/password_reset_expired');

			
			
		$userInfo =  $this->Users->getUser($user_id);
		$this->data['userInfo'] = $userInfo;

		// password update handling
		$postdata = $this->input->post();
		$update_success = false;

		$errorMsg = '';

		if(!empty($postdata)){
			if (empty($postdata['new_password']) || empty($postdata['new_password2']))
			$errorMsg = 'password is empty';
			else if ($postdata['new_password'] != $postdata['new_password2'])
			$errorMsg = 'password mismatch/new password empty';

			$userData =  $this->Users->getUser($user_id);
			$userData['password'] = $postdata['new_password'];
			$this->Users->addUser($userData);

			//update status of activation key
			$activationKeyInfo['status'] = 1;
			$this->Activation_Key->updateActivationKey($activationKeyInfo);
			redirect('/webapp/login');
		}


		$this->data['errorMsg'] = $errorMsg;

		$this->data['page_id'] = 'reset-password-form';
		$this->load->view('webapp/reset_password.php', $this->data);
	}

	public function password_reset_expired(){
		$this->data['page_id'] = 'reset-password-expired';
		$this->load->view('webapp/password_reset_expired.php', $this->data);
	}

	/**
	 * Log out
	 */
	public function logout() {
			
		if(!empty($_SESSION['_user']))
		unset($_SESSION['_user']);
			
		redirect('/webapp/login');
	}

	private function checkAuth() {
		session_start();

		if(empty($_SESSION['_user']))
		redirect('/mobileapp/login');
		else
		$this->userData = $_SESSION['_user'];
	}

	public function mobile_location(){
		//redirect('../rcgeventplanner://');
		header('Location: www.google.com');
	}
	
	/**
	 * Join an Event
	 */
	public function join(){
		$userIds = $this->input->post('userIds');
		$eventId = $this->input->post('eventId');
		$refType = $this->input->post('reference');
		$refId = $this->input->post('refId');
		
		$userIds = explode("||", $userIds);
		$userIdsLength = count($userIds);
		
		try {
			
			if(!empty($eventId)&&!empty($refType)&&!empty($refId)){
				// send email notification if there are event owners assigned to event
				$eventOwners = $this->Event_Owner->getEventOwners(array(
						'event_id' => $eventId,
				));
				
				if($userIdsLength > 0) {
					$response = $this->Guest->getGuestStatus(array(
						'user_id' =>  $userIds[0],
						'event_id' => $eventId,
						'reference_type' 	=> $refType,
						'reference_id'		=> $refId
					));
					if(empty($response)){
						foreach ($userIds as $key=>$userId){
							$this->Guest->join(array(
								'user_id' =>  $userId,
								'event_id' => $eventId,
								'reference_type' 	=> $refType,
								'reference_id'		=> $refId,
								'role_id' => 3 //By default, role is attendee
							));
							// SEND OWNER EMAIL NOTIFICATION FROM PRIMARY GUEST
							$registrant = $this->Users->getUser($userId);
							$event = $this->Events->getEvent($eventId);
							foreach ($eventOwners as $event_owner) {
								$this->Users->sendJoinNotificationToOwners($event_owner, $registrant, $event);
							}
							// END EMAIL NOTIFICATION
						}
					} else {
						$event_attendee = current($response);
			
						$this->Guest->join($event_attendee);
						
						// SEND OWNER EMAIL NOTIFICATION FROM PRIMARY GUEST
							$registrant = $this->Users->getUser($userIds[0]);
							$event = $this->Events->getEvent($eventId);
							foreach ($eventOwners as $event_owner) {
								$this->Users->sendJoinNotificationToOwners($event_owner, $registrant, $event);
							}
						
						// companion
						//Join companions if there's selected
						$rejectedCompanion = $this->Guest->getGuestCompanions($userIds[0], $eventId, 'rejected');	
						
						for ($x = 1 ; $x < count($userIds); $x++) {
							
							if ($rejectedCompanion[$userIds[$x]]) {
								$params = array(
											'eventAttendeeIDs' => $rejectedCompanion[$userIds[$x]]['eventAttendeeID'],
											'status' => 'pending'
											);
								$this->Guest->updateStatus($params);		
							} else {
								$this->Guest->join(array(
									'user_id' =>  $userIds[$x],
									'event_id' => $eventId,
									'reference_type' 	=> $refType,
									'reference_id'		=> $refId,
									'role_id' => 3 //By default, role is attendee
								));
							}
							
							$registrant = $this->Users->getUser($userIds[$x]);
							foreach ($eventOwners as $event_owner) {
								$this->Users->sendJoinNotificationToOwners($event_owner, $registrant, $event);
							}
						}
					}
				}
				
				$this->data['result']="success";
			} else {
				$this->data['result']="failed";
			}
		} catch (Exception $e) {
			$this->data['result']="failed";
		}
		
		$this->load->view('mobileapp/json_view.php', $this->data);
		
	}
	
	/**
	 * Cancel Join Event
	 */
	public function cancel_join(){
		$userIds = $this->input->post('userIds');
		$eventId = $this->input->post('eventId');
		$reference = $this->input->post('reference');
		$refId = $this->input->post('refId');
		$data = array();
		try{
			$test = "";
			$userIds = explode("||", $userIds);
			foreach ($userIds as $key=>$userId){
				$this->Guest->cancelJoin(array(
						'user_id' => $userId,
						'event_id' => $eventId,
						'reference_type'=> $reference,
						'reference_id'	=> $refId 
				));

			}
			$this->data['result']="success";
		}catch(Exception $e){
			$this->data['result']="failed -".$e;
		}
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
	
	public function map()
	{
		$eventId = $this->input->get('id');
		//$this->output->cache(7200); 
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->data['eventId'] = $eventId;
		$this->data['page_id'] = 'page5';
		$this->load->view('mobileapp/map.php',$this->data);
	}
	
	/**
	 * Get Guest Users
	 */
	public function get_guest_users() {
		//get primary users
		$primaryUserData = $this->Users->getUsers(array('sort_field' => 'email', 'is_primary' => '1'));
		$primaryUserResultData = array();
		foreach($primaryUserData as $user){
			$primaryUserResultData[$user['userID']] = $user;
		}

		//get companion users
		$userData = $this->Users->getUsers(array('sort_field' => 'last_name,first_name', 'is_primary' => '0'));
		$userResultData = array();
		foreach($userData as $user){
			//retrieve primary user
			$eventCompanionAttendeeData = $this->Companion->getPrimaryUser($user['userID']);
			$user['primary_user_id'] = $eventCompanionAttendeeData[0]['primary_user_id'];
			$userResultData[$user['userID']] = $user;
		}

		$data['primaryUser'] = $primaryUserResultData;
		$data['users'] = $userResultData;

		$data['method'] = 'get_user';
		$this->data['result'] = $data;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
	
	/**
	 * Manage Companion 
	 */
	public function manage_companion() {
		$companions = $error = array();		
		//$sessionUserData = $_SESSION['_user'];
		//$userId = $sessionUserData['userID'];
		
		$save_success = false;
		
		$uid  = $this->input->get('uid');
		$userId = $uid;
		$postdata = $this->input->post();
		
		$cid  = $this->input->get('cid');
		
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
		
		if(isset($cid)) {
			$userInfo =  $this->Users->getUser($cid);
			
			if (!empty($cid)) {
				$userPhoto = $this->User_Photo->getPhoto($uid);
				$userPhoto = make_new_key($userPhoto, 'fk_i_uid');
				$userInfo['uploaded_photo'] = $userPhoto;
				
				if($this->Companion->getPrimaryUser($cid)!= null){
					$companionInfo = current($this->Companion->getPrimaryUser($cid));
				}else{
					$companionInfo = array();
					$companionInfo['type'] = "";
					$companionInfo['primary_user_id'] = "";
				}
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
			'userId'	 =>		$userId,
			'error'		=> $error,
		);
		
		$this->load->view('mobileapp/manage_companion.php', $this->data);
	}
	
	/**
	 * List of Event's Agenda
	 */
	public function schedule(){
		$eventId = $this->input->get('id');
		//self::checkAuth(__FUNCTION__, array('eventID' => $eventId));
		//$this->output->cache(7200);
		$this->data['event'] = $this->Events->getEvent($eventId);
		$this->data['itineraries'] = $this->Itinerary->getEventItinerariesGroupByDate($eventId);
		$this->data['eventId'] = $eventId;
		$this->data['page_id'] = 'page4';
		echo '<pre>'; print_r($this->data); echo '</pre>';
die();
		//$this->load->view('webapp/schedule.php',$this->data);
	}
	
	/**
	 * Agenda Information
	 */
	public function view_itinerary(){
		$this->data['userID'] = "52";
		
		$eventId = $this->input->get('eid');
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
		echo '<pre>'; print_r($this->data); echo '</pre>';
die();
		//$this->load->view('webapp/view_itinerary.php',$this->data);
	}
	
	
	public function user_update()
	{		
		$objEventClient = new api_user_client();		
		
		$jsonResult = array();
		
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
				$update_success = true;
				$jsonResult['result'] = 'success';
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
		}

		if ($error)
			$jsonResult['result'] = $error;
		$this->data['result'] = $jsonResult;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
	
	
	/**
	 * Process Companion 
	 */
	public function companion_update() {
		$error = array();				
		$save_success = false;
				
		$postdata = $this->input->post();
		$userId = $postdata['primary_user_id'];		
		
		if(!empty($postdata)){	
			$postdata['is_primary'] = 0;
						
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
		
		if (empty($error) && !empty($postdata)) {
			$user = $this->Users->addUser($postdata);
			$jsonResult['result'] = 'success';
		}		
	
		
		if ($error)
			$jsonResult['result'] = $error;
			
		$this->data['result'] = $jsonResult;
		$this->load->view('mobileapp/json_view.php', $this->data);
	}
}