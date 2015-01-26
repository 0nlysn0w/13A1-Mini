<?php

class Login extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->view->render('login/index');
	}

	public function login()
	{
		$login_model = $this->loadModel('Login');
		$login_succesfull = $login_model->login();

		if ($login_succesfull) {
			header('location: ' . URL . 'dashboard/index');
		} else {
			header('location: ' . URL . 'login/index');
		}
	}
}