<?php
require_once APPPATH . 'libraries/clients/api_attendee_activity_preference_client.php';
class Attendee_Activity_Preference extends CI_Model{
	
	private  $objAttendeeActivityPreference; 
	
	public function __construct(){		
		$this->load->helper('cache');	
		$this->objAttendeeActivityPreference = new api_attendee_activity_preference_client();		
	}
	
	public function getAttendeeActivityPreference($data) {
		$params = array(
			'attendeeActivityPreferenceID' => $data['attendeeActivityPreferenceID'], 
			'activityPreferenceID' => $data['activityPreferenceID'], 
			'activityPreferenceOptionID' => $data['activityPreferenceOptionID'],  
			'userID' => $data['userID'] 
		);		
		$res = $this->objAttendeeActivityPreference->attendee_activity_preference_get($params);

		return $res['result'];
	}
	
	public function saveAttendeeActivityPreference($data) {
		$res = array();
		if(!empty($data['options'])) {
			foreach($data['options'] as $key => $val){
				$params = array(
					'attendeeActivityPreferenceID' => null, 
					'activityPreferenceID' => $data['activityID'], 
					'activityPreferenceOptionID' => $key,  
					'userID' => $data['user_id'] 
				);
				self::deleteAttendeeActivityPreference($params); 
				if(!empty($val)){
					foreach($val as $opt) {		
						$params['value'] = $opt; 					
						$res[] = $this->objAttendeeActivityPreference->attendee_activity_preference_post($params);								
					}
				}	
			}
		}		
		return $res; 
	}

	public function deleteAttendeeActivityPreference($data) {
		$params = array(
			'attendeeActivityPreferenceID' => $data['attendeeActivityPreferenceID'], 
			'activityPreferenceID' => $data['activityPreferenceID'], 
			'activityPreferenceOptionID' => $data['activityPreferenceOptionID'],  
			'userID' => $data['userID'] 
		);
		return $this->objAttendeeActivityPreference->attendee_activity_preference_remove_get($params);	
	}
	
}