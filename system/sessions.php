<?php
<<<<<<< HEAD
$Sessions = new Session();
=======
$Sessions = new Sessions();

/* 
Sample flash code perfect 
for copying into your layouts 

------SNIP------

<?php
if (array_key_exists("flash", $_SESSION)) {
  ?>
  <div class="flash <?php echo $_SESSION['flash']['type']; ?>">
    <?php echo $_SESSION['flash']['message']; ?>
  </div>
  <?php
  // clear after printing
  unset($_SESSION["flash"]);
}
?>

------SNIP------
*/
>>>>>>> d760b83e4fad6e955ac08a56c2381b8c3844dd16
class Sessions {
	
	function __construct() {
		session_start();			
	}
	
	function login() { }
	
	function logout() { }
}