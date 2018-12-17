<?php
/**
 * Consolidates all authorization requirements per each webapp controller action
 *
 */
final 
	class WebApp_Auth 
		extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}		
	
	/**
	 * 
	 * on dashboard view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function dashboard($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on event_details view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function event_details($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on schedule view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function schedule($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on view_itinerary view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function view_itinerary($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on attendees view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function attendees($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on map view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function map($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on speakers view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function speakers($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	/**
	 * 
	 * on view_user view, check if authorized to view the event
	 * @param array $options
	 * @param array	userData
	 * @return bool
	 */
	public function view_user($params = array(), $userData)
	{
		//check event authorization of user
		if(isset($params['eventID'])) {
			return $this->_checkEventAuth($params['eventID'], $userData['userID']);
		}
		return false;
	}
	
	
	/**
	 * 
	 * Checks if user is authorized to view an event and its subscreens
	 * @param int event id
	 * @param int user id
	 * @return bool
	 */
	private function _checkEventAuth($eventId, $userId)
	{
		$events = $this->Guest->getGuestAllEvents($userId);
		if(isset($events[$eventId])) {
			return true;
		}
		return false;
	}
}