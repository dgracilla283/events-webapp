<?php
$config ["mail"] = array (

		/**
		 * Custom headers per email type (e.g.
		 * event_invite etc) *
		 */
		"event_invite" => array (
				"subject" => "You are being invited to an Event",
		/*"from"		=> array(
			"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
		)*/
		"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"forgot_password" => array (
				"subject" => "Reset your password",
		/*"from"		=> array(
			"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
		)*/
		"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"activate_account" => array (
				"subject" => "Activate Account",
		/*"from"		=> array(
			"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
		)*/
		"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"speaker_event_invite" => array (
				"subject" => "You are being invited to be the Speaker to an Event",
			/*"from"		=> array(
			 "donotreply.rcgevents@gmail.com", "RCG Events Mailer"
			)*/
			"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"join_notification" => array (
				"subject" => "A User Has Joined Your Event",
				/*"from"		=> array(
					"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
				)*/
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"approve_notification" => array (
				"subject" => "Your Request Has Been Approved",
				/*"from"		=> array(
					"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
				)*/
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"reject_notification" => array (
				"subject" => "Your Request Has Been Rejected",
				/*"from"		=> array(
					"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
				)*/
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"accept_speaker_invite" => array (
				"subject" => "Speaker has accepted your invite",
				/*"from"		=> array(
					"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
				)*/
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"decline_speaker_invite" => array (
				"subject" => "Speaker has declined your invite",
				/*"from"		=> array(
					"donotreply.rcgevents@gmail.com", "RCG Events Mailer"
				)*/
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		"breakout_speaker_invite" => array (
				"subject" => "You are being invited to be the Speaker of an Event Activity",
				"from" => array (
						"support@rcgevents.com",
						"RCG Events Mailer"
				)
		),

		/**
		 * default value must always be existing *
		 */
		"default" => array (
				"mailtype" => "html",
				"protocol" => "mail",
				"newline" => "\r\n",
				/**
				 * TODO: Change this to RCG mail smtp settings since this won't work in local
				 * network since gmail smtp is blocked
				 *
				 * "smtp_host" => "ssl://smtp.googlemail.com",
				 * "smtp_port" => 465,
				 * "smtp_user" => "donotreply.rcgevents@gmail.com",
				 * "smtp_pass" => "tempP@ssword",
				 */

				/**
				 * RCG Events Mailer Config *
				 */
				"smtp_host" => "ssl://smtpout.asia.secureserver.net",
				"smtp_port" => 465,
				"smtp_user" => "support@rcgevents.com",
				"smtp_pass" => "Andr01d@pp",

				"charset" => "iso-8859-1",
				"wordwrap" => TRUE,
				"overrideToHeader" => FALSE
		)
);