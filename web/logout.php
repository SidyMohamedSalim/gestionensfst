<?php
    define("_VALID_PHP", true);
include 'include/fonctions.php';
sec_session_start();
// Unset all session values
    if(isset($_SESSION['admin']) && $_SESSION['admin'])$admin=1;
    else $admin=0;
$_SESSION = array();
// get session parameters 
$params = session_get_cookie_params();
// Delete the actual cookie.
setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
// Destroy session
session_destroy();
    if($admin)
header('Location: ./login_admin.php');
    else
header('Location: ./');
//header('login.php');
?>