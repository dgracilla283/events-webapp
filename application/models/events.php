<?php
require_once APPPATH . 'libraries/clients/api_event_client.php';
class Events extends CI_Model{

	private $objEventClient;

	public function __construct(){
		$this->objEventClient = new api_event_client();
		$this->load->helper('cache');
	}

	public function getEvents($data = array()){
		$res = $this->objEventClient->events_get($data, 'json');
		return $res['result'];
	}

	public function getEvent($id){
		if( empty($id) ) {
			return false;
		}
		$res = $this->objEventClient->event_get(array('id' => $id), 'json');
		return $res['result'];
	}

	public function deleteEvent($id){
		if( empty($id) ) {
			return false;
		}
		$res = $this->objEventClient->event_remove_get(array('eventID' => $id), 'json');
		delete_all_cache();
		return $res['result'];
	}

	public function duplicateEvent($id){
		if( empty($id) ) {
			return false;
		}
		$data = $this->getEvent($id);
		print_r($data);
		die();
		$res = $this->objEventClient->event_remove_get(array('eventID' => $id), 'json');
		delete_all_cache();
		return $res['result'];
	}

	public function addEvent($data = array()) {
		if( empty($data) ) {
			return false;
		}
		$data['status'] = isset($data['status']) ? 	1 : 0;
		// format date
		$data['start_time'] = isset($data['start_time']) ? $data['start_time'] : '00:00:00';
		$data['end_time'] = isset($data['end_time']) ? $data['end_time'] : '00:00:00';
		$data['start_date_time'] = date('Y-m-d H:i',strtotime( str_replace('-', '/',$data['start_date_time']).' '.$data['start_time']));
		$data['end_date_time'] = date('Y-m-d H:i', strtotime(str_replace('-', '/', $data['end_date_time']).' '.$data['end_time']));

		$res = $this->objEventClient->event_post($data, 'json');

		delete_all_cache();

		//by default set all users as attendees
		return $res['result'];
	}

}