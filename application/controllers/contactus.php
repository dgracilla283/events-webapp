<?php
class Contactus extends CI_Controller {

	private $data = array();
	private $userData = array();	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');  		    			       
    }

    public function index(){    
    	$this->data['page_id'] = $this->uri->segment(2);
		$this->load->view('webapp/contactus.php', $this->data);
    }
    
}