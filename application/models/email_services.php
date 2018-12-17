<?php
class Email_Services extends CI_Model {

	public function __construct(){
		$this->load->library('email');
		$this->load->helper(array('form', 'url'));
	}

	public function notifyApproved($registrant, $eventTitle) {
		$appUrl = base_url();
		$sent = false;

		try {
			if ( !empty($registrant['email']) ) {
				$this->email
				->setConfigurations( 'approve_notification' )
				->renderTemplate(
					array(
						'event_title' => $eventTitle,
						'registrant' => $registrant,
						'app_url' => $appUrl
					)
				)
				->to( $registrant['email'] )
				->send();
				$sent = true;
			}
		}catch(Exception $e){
			$this->data['result']="failed -".$e;
		}
		return $sent;
	}

	public function notifyRejected($registrant, $eventTitle) {
		$appUrl = base_url();
		$sent = false;

		try {
			if ( !empty($registrant['email']) ) {
				$this->email
				->setConfigurations( 'reject_notification' )
				->renderTemplate(
					array(
						'event_title' => $eventTitle,
						'registrant' => $registrant,
						'app_url' => $appUrl
					)
				)
				->to( $registrant['email'] )
				->send();
				$sent = true;
			}
		}catch(Exception $e){
			$this->data['result']="failed -".$e;
		}
		return $sent;
	}

	/**
	 * genericEmailInvite
	 * Email execution
	 */
	public function genericEmailInvite($ihtml, $emailTemplate, $recipient) {
		$appUrl = base_url();
		$sent = false;

		try {
			if (!empty($recipient)) {
				$this->email
					 ->setConfigurations($emailTemplate)
					 ->renderTemplate($ihtml)
					 ->to($recipient)
					 ->send();
				$sent = true;
			}
		}catch(Exception $e){
			$this->data['result']="failed -".$e;
		}
		return $sent;
	}
}