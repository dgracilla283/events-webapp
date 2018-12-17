<?php
require_once APPPATH . 'libraries/clients/api_activity_preference_option_client.php';
class Activity_Preference_Option extends CI_Model{
	
	private  $objActivityPreferenceOption; 
	
	public function __construct(){
		$this->load->helper('cache');	
		$this->objActivityPreferenceOption = new api_activity_preference_option_client();
	}
	
	public function getActivityPreferenceOptions($data) {
		$params = array(
			'activityPreferenceOptionID' => $data['activityPreferenceOptionID'], 
			'activityPreferenceID' => $data['activityPreferenceID']
		); 
		$res = $this->objActivityPreferenceOption->activity_preference_option_get($params);		  		
		return $res['result'];
	}
	
	public function saveActivityPreferenceOption($data) {
		$response = array();

		$params = array(
			'activityPreferenceOptionID' => null,  
			'activityPreferenceID' => $data['activityPreferenceID']
		);
		$previousOptions = self::getActivityPreferenceOptions($params);

		if(!empty($previousOptions)) {
			$previousOptions = make_new_key($previousOptions,'activityPreferenceOptionID');  
		} 
			
		if(!empty($data['options'])) {
			//delete options   	
			foreach($previousOptions as $key => $val) {
				if(!array_key_exists($key, $data['options'])){
					$params['activityPreferenceOptionID'] = $key; 
					self::deleteActivityPreferenceOption($params); 	
				}
			} 				
			// update/add options			 		
			foreach($data['options'] as $key => $val) {
				$val = trim($val); 
				if(!empty($val)) {  
					$params['activityPreferenceOptionID'] = null; 
					$params['title'] = $val; 
					$params['description'] = '';				
					if(array_key_exists($key, $previousOptions)){
						$params['activityPreferenceOptionID'] = $key; 
					}				 
					$response[] = $this->objActivityPreferenceOption->activity_preference_option_post($params); 
				}else {
					if(array_key_exists($key, $previousOptions)){
						$params['activityPreferenceOptionID'] = $key;
						self::deleteActivityPreferenceOption($params);
					} 
				}
			}
		}	 		 
		return $response;  
	}

	public function deleteActivityPreferenceOption($data) {
		$params = array(
			'activityPreferenceOptionID' => $data['activityPreferenceOptionID'], 
			'activityPreferenceID' => $data['activityPreferenceID']
		); 
		return $this->objActivityPreferenceOption->activity_preference_option_remove_get($params); 
	}
	
}