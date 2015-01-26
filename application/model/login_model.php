<?php

class LoginModel
{

	public function login()
	{
		if (!isset($_POST['user_name']) OR empty($_POST['user_name'])) {
			$_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
			return false;
		}
		if (!isset($_POST['user_password']) OR empty($_POST['user_password'])) {
			$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
			return false;
		}

		$sth = $this->db->prepare("SELECT user_id,
										  user_name,
										  user_email,
										  user_password_hash,
										  user_active,
										  user_failed_logins,
										  user_last_failed_login
								   FROM users
								   WHERE (user_name = :user_name OR user_email = :user_name)");
		$sth->execute(array(':user_name' => $_POST['user_name']));
		$count = $sth->rowCount();

		if ($count != 1) {
			$_SESSION["feedback_negative"][] = FEEDBACK_LOGIN_FAILED;
			return false; 
		}

		$result = $sth->fetch();

		if (($result->user_failed_logins >= 3) AND ($result->user_last_failed_login > (time()-30))) {
			$_SESSION['feedback_negative'][] = FEEDBACK_PASSWORD_WRONG_3_TIMES;
			return false;
		}

		if (password_verify($_POST['user_password'], $result->user_password_hash)) {
			
		}

	}

	public function logout()
	{
		setcookie('rememberme', false, time() - (3600 * 24 * 3650), '/', COOKIE_DOMAIN);

		Session::destroy();
	}
}