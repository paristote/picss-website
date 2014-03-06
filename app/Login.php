<?php

/*
 * The home page controller
 * Show all Picss in a single page
 */
class Login extends AppBase {

	function loginAdmin($f3) {
		$f3->set('pageTitle', ":Picss: Login");
		$f3->set('content', 'admlogin.htm');
		$f3->set('pageCss', 'admlogin.css');
	}

	function authAdmin($f3) {
		// get username and password entered by the user
		$uid = $f3->get('POST.user_id');
		$pwd = $f3->get('POST.password');
		
		$crypt = \Bcrypt::instance();
		
		// verify the credentials against the values in session
		$ok = ($uid === $f3->get('user_id') &&
			   $crypt->verify($pwd, $f3->get('password')));
		
		if (!$ok) {
			$f3->error('401');
		} else {
			$f3->clear('SESSION');
			$f3->set('SESSION.auth', TRUE);
			$f3->reroute('/aaa');
		}
	}

	function logout($f3) {
		$f3->clear('SESSION');
		$f3->reroute('/');
	}

}

?>