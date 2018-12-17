<?php
require_once APPPATH . 'libraries/clients/api_activity_preference_client.php';
class Activity_Preference extends CI_Model{
	
	private  $objActivityPreference; 
	
	public function __construct(){		
		$this->load->helper('cache');	
		$this->objActivityPreference = new api_activity_preference_client();
		if (! isset($this->Activity_Preference_Option)){
			$this->load->model('Activity_Preference_Option', '', TRUE);
		}
	}
	
	public function getActivityPreference($data) {
		$params = array(
			'activityPreferenceID' => $data['activityPreferenceID'], 
			'referenceID' => $data['referenceID'],
			'referenceType' => $data['referenceType'], 
			'eventID'  => $data['eventID']
		); 
		$res = $this->objActivityPreference->activity_preference_get($params); 	

		if(!empty($res['result'])) {
			foreach($res['result'] as &$pref){					
				$pref['options'] = $this->Activity_Preference_Option->getActivityPreferenceOptions(array(
						'activityPreferenceOptionID' => null,  
						'activityPreferenceID' => $pref['activityPreferenceID'])); 
			}
		}
		return $res['result'];
	}
	
	public function saveActivityPreference($data) {
		$params = array(
			'activityPreferenceID' => $data['activityPreferenceID'], 
			'referenceID' => $data['referenceID'],
			'referenceType' => $data['referenceType'], 
			'eventID'  => $data['eventID'], 
			'title' => $data['title'], 
			'description' => $data['description'], 
			'optionDisplayType' => $data['optionDisplayType'], 
			'isRequired' => $data['isRequired']
		);	
			 
		return $this->objActivityPreference->activity_preference_post($params); 
	}

	public function deleteActivityPreference($data) {
		$params = array(
			'activityPreferenceID' => $data['activityPreferenceID'], 
			'referenceID' => $data['referenceID'],
			'referenceType' => $data['referenceType'], 
			'eventID'  => $data['eventID']
		); 
		return $this->objActivityPreference->activity_preference_remove_get($params); 
	}
	
}