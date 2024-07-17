<?php
define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if (login_check($bdd) == true) {

	if (isset($_SESSION['admin']) && $_SESSION['admin']) header('location:profil_admin.php');
	else
		header('location:profil.php');


	$active = 1;
	$script = array("editable", "scripts");
	include('include/header.php');

?>

	<!--Body content-->
	<!-- horizontal nav -->

	<div>
		<ul class="breadcrumb">
			<li>
				<a href="#">Home</a> <span class="divider">/</span>
			</li>
		</ul>
	</div>



<?php

	include('include/scripts.php');
	include('include/footer.php');
} else {
	header('location:login.php');
}
?>