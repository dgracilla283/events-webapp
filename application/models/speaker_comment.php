<?php
require_once APPPATH . 'libraries/clients/api_speaker_comment_client.php';
class Speaker_Comment extends CI_Model{
	
	private  $objActivityPreference; 
	
	public function __construct(){
		$this->load->helper('cache');	
		$this->objSpeakerComment = new api_speaker_comment_client();		
	}
	
	public function getSpeakerComment($eid) {
		$res = $this->objSpeakerCommentClient->speaker_comment_get(array('event_attendee_id' => $eid), 'json');		
        return $res['result'];
	}
    
	public function addSpeakerComment($data) {
		$params = array(			
			'event_attendee_id'  => $data['event_attendee_id'], 
			'comment' => $data['comment']
		);	
			 
		return $this->objSpeakerComment->speaker_comment_post($params); 
	}

	public function deleteSpeakerComment($data) {
		$params = array(
			'speakerCommentID' => $data['speakerCommentID'],
			'event_attendee_id'  => $data['event_attendee_id']
		);	
		return $this->objSpeakerComment->speaker_comment_remove_get($params); 
	}	
}