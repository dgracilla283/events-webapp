<?php
require_once APPPATH . 'libraries/clients/api_user_client.php';

class Export extends CI_Controller { 

	public function __construct()
	{
		parent::__construct();		
	} 

	public function index(){
		echo 'index page'; exit;  
	}

	public function event_attendees(){
		$eventId = $this->input->get('eid'); 
		$export = $this->input->get('export') ? true : false; 
		$attendees = $this->Guest->getAttendees(array(
			'event_id' => $eventId, 
			'status' => 'approved'
		));

		$attendees =  make_new_key($attendees,'userID'); 
		
		$eventAttendees = array();
		
		foreach($attendees as $attendee) {
			$userID = $attendee['userID']; 	
			if($attendee['is_primary']){
				$eventAttendees[$userID] = 	$attendee; 
				$companions = $this->Companion->getCompanions($userID);
				if(!empty($companions)){
					foreach($companions as $comp) {
						if(array_key_exists($comp['user_id'], $attendees)){
							$comp = array_merge($comp, $attendees[$comp['user_id']]); 	
							$eventAttendees[$userID]['companions'][] =  $comp; 
						}
					} 	
				} 
				 	
			}
		
		}
		$this->data['event'] = $this->Events->getEvent($eventId); 
		
		$this->data['attendees'] = $eventAttendees; 
		$this->data['export'] = $export; 
		
		$html = $this->load->view('export_attendees.php', $this->data);

		if($export){
			$file= strtolower(str_replace(' ','_', $this->data['event']['title'])).'.xls';
			header("Content-type: application/vnd.ms-excel"); 
			header("Content-Disposition: attachment; filename=$file");
			echo $html; 
		}
		
		
	}



} 