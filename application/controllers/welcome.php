<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/RestControllerLibrary.php");


// Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
// Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
// Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

// use Libraries\RestControllerLibrary as RestController;

class Welcome extends CI_Controller
{

	public function index()
	{
		parent::__construct();
		$this->load->view('login');
	}
}
