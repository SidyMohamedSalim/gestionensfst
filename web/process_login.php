<?PHP
define("_VALID_PHP", true);
include_once 'include/connexion_BD.php';
include_once 'include/fonctions.php';
sec_session_start(); // Our custom secure way of starting a php session. 
 
if(isset($_POST['login'], $_POST['p'])) { 

   $log = $_POST['login'];
   $password = $_POST['p']; // The hashed password.
    if(isset($_POST['admin']))
        $type="admin";
            else
                $type="normal";
    $l=login($log, $password, $bdd,$type);
   if($l === true) {
      // Login success
      echo 'Success: You have been logged in!';
	  if(isset($_POST['location'])) // a modifier: securiser la redirection!!!
	  header('Location: '.$_POST['location']);
	  else
	  header('Location: ./');
	 
	 
   } else {
      // Login failed
       if(isset($_POST['admin']))
      header('Location: ./login_admin.php?error='.$l);
       else
           header('Location: ./login.php?error='.$l);
		
   }
} else { 
   // The correct POST variables were not sent to this page.
   echo 'Invalid Request';

    if(isset($_POST['admin']))
        header('Location: ./login_admin.php?error=0');
    else
   header('Location: ./login.php?error=0');
}

?>
