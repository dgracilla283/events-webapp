<?php

require_once dirname( APPPATH ) . '/system/libraries/Email.php';
/**
 * RCG Event Planner mail class
 * To use this class, just declare $this->load->library('email') (if not declared yet).
 * Use $this->email->$methodName to access the methods
 *
 * @since			02/01/2013
 */
class My_Email 
	extends CI_Email
{

	const EXT = 'phtml';
	const FROM_THIS_DATE_FORMAT = 'Y-m-d H:i:s';
	const TO_THIS_DATE_FORMAT = 'F j, Y - g:i a';
	
	public $overrideToHeader = FALSE;
	
	/**
	 * CI Instance
	 */
	private $_ci;
	
	/**
	 * Current method type
	 */
	private $_emailType;

	/**
	 * Constructs a MY_Email Object
	 * @param string	Method Type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	
	/**
	 * Sets configuration method for given email type.
	 * Configurations for given email type can be added/changed in applications/config/config_mail.php
	 * @param string	Email Type
	 * @return $this
	 */
	public function setConfigurations( $emailType )
	{
		$this->_emailType = $emailType;

		//open config file
		$this->_ci =& get_instance();
		
		$this->_ci->config->load('config_mail', true, true);
		$mailConfigs = $this->_ci->config->item( 'config_mail' );
		
		$mailConfigs = $mailConfigs['mail'];
		$this->initialize( $mailConfigs['default'] );
		
		if( isset( $mailConfigs[$this->_emailType] ) && 
			!empty( $mailConfigs[$this->_emailType] ) ) {
			$this->_setConfigHeaders( $mailConfigs[$this->_emailType] );
		}
		return $this;
	}

	/**
	 * 
	 * Sets or overrides current email type
	 * @param string $emailType
	 * @return this
	 */
	public function setEmailType( $emailType )
	{
		$this->_emailType = $emailType;
		return $this;
	}
	
	/**
	 * Renders email template
	 * @param array	variables to be set inside mail template
	 * @return this
	 */
	public function renderTemplate( $_ = array() )
	{
		if( !file_exists( dirname(__FILE__) . '/MY_Email_templates/' . $this->_emailType . '.' . self::EXT )  ) {
			//TODO: Put Exception Handler here
			die('Email template does not exist');
		}
		
		ob_start();
		//The $variables param will hold dynamic data to be echoed inside the phtml template
		include( dirname(__FILE__) . '/MY_Email_templates/' . $this->_emailType . '.' . self::EXT );
		$fileContents = ob_get_clean();
		//put the email contents as email body
		$this->message($fileContents);
		return $this;
		
	}
	
	/**
	 * sets custom headers declared at mail_configs.php
	 * @param array	email headers
	 * @return this
	 */
	private function _setConfigHeaders( $emailHeaders = array() )
	{
		foreach($emailHeaders as $key => $value) {
			if (method_exists($this, $key)) {
				if(is_array($value)) {
					//For "from()" key, TODO: dynamic params
					$this->$key( $value[0], $value[1] );
				} else {
					$this->$key($value);
				}
			}
		}
		//exit;
		return $this;
	}

	/**
	 * Formats display date in email body
	 * @param string date
	 * @return string formatted date
	 */
	public function formatDisplayDate( $strDate )
	{
		$dateTime = DateTime::createFromFormat(self::FROM_THIS_DATE_FORMAT, $strDate);
		return $dateTime->format(self::TO_THIS_DATE_FORMAT);
	}
	
	/**
	 * Mutator for CI_Email to() method. This will allow to avoid email blast
	 * when testing in dev or prod environment
	 * 
	 * @param string email
	 * @return $this
	 */
	public function to($email)
	{
		if( $this->overrideToHeader ) {
			//In the meantime send it to my email - gesmilla
			parent::to('brian.rivadulla@rcggs.com');
			//parent::to('geraldine.esmilla@rcggs.com');
		} else {
			parent::to($email);
		}
		return $this;
	}
}
