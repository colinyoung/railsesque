<?php
$Sessions = new Sessions();
if (array_key_exists("flash", $_SESSION)) {
  print $_SESSION['flash'];
  $_SESSION['flash'] = "";
}
class Sessions {
	
	function __construct() {
		session_start();			
	}
	
	function login() { }
	
	function logout() { }
}