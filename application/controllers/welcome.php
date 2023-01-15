<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/RestControllerLibrary.php");


class Welcome extends CI_Controller
{
	public function index()
	{
		parent::__construct();
		$this->load->view('http://localhost/help_me/application/views/user_login.php');
	}
}
