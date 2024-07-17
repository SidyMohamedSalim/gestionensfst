<?php
define("_VALID_PHP", true);
$install=1;
include('../include/connexion_BD.php');

$conn=mysqli_connect(HOST, USER, PASSWORD);
$db=NULL;
if($conn)
{
    $db = mysqli_select_db($conn, DATABASE);
}
function sanitize($string, $trim = false,  $end_char = '&#8230;', $int = false, $str = false)
{
    $string = filter_var($string, FILTER_SANITIZE_STRING);
    $string = trim($string);
    $string = stripslashes($string);
    $string = strip_tags($string);
    $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);

    if ($trim) {
        if (strlen($string) < $trim)
           return $string;

        $string = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string));

        if (strlen($string) <= $trim)
            return $string;

        $out = "";
        foreach (explode(' ', trim($string)) as $val)
        {
            $out .= $val.' ';

            if (strlen($out) >= $trim)
            {
                $out = trim($out);
                return (strlen($out) == strlen($string)) ? $out : $out.$end_char;
            }
        }
        //$string = substr($string, 0, $trim);
    }
    if ($int)
        $string = preg_replace("/[^0-9\s]/", "", $string);
    if ($str)
        $string = preg_replace("/[^a-zA-Z\s]/", "", $string);

    return $string;
}

if(isset($_POST['host']) && isset($_POST['user']) && isset($_POST['password']) && isset($_POST['database']))
{
    $php=file_get_contents('../include/connexion_BD.php');
    define('DB_HOST', sanitize($_POST['host'])); // database location
    define('DB_NAME', sanitize($_POST['database'])); // database name
    define('DB_USER', sanitize($_POST['user'])); // user name
    define('DB_PASS', sanitize($_POST['password'])); // password

    $php = str_replace('define("HOST", "'.HOST,'define("HOST", "'.DB_HOST,$php);
    $php = str_replace('define("USER", "'.USER,'define("USER", "'.DB_USER,$php);
    $php = str_replace('define("PASSWORD", "'.PASSWORD,'define("PASSWORD", "'.DB_PASS,$php);
    $php = str_replace('define("DATABASE", "'.DATABASE,'define("DATABASE", "'.DB_NAME,$php);

    $fh = fopen('../include/connexion_BD.php', 'w') or die("problème lors de l'ouverture du fichier .PHP");

    fwrite($fh, $php)or die('Problème lors de l ecriture du fichier .php');

    header('location:index.php');
}

?>
<!DOCTYPE html>
<html>
<head>
	
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Installation</title>
		<!-- Bootstrap -->
		<link href="../styles/bootstrap-classic.min.css" rel="stylesheet" media="screen">

		<style>
			fieldset.border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}

    legend.border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
		margin-bottom: 0px;
        border-bottom:none;
    }
		</style>
		
</head>
<body>
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">


            <a class="brand"  href="index.php"> <span>Installation</span></a>

            <ul class="nav">

                <li class="<?php if(!isset($_GET['action']) || ($_GET['action']!="import" && $_GET['action']!="export" ) ) echo 'active'; ?>">
                    <a href="index.php?action=connection">connexion BD</a>
                </li>
                <li class="<?php if(isset($_GET['action']) && $_GET['action']=="import" ) echo 'active'; ?>"><a href="index.php?action=import">Import BD</a></li>
                <li class="<?php if(isset($_GET['action']) && $_GET['action']=="export" ) echo 'active'; ?>"><a href="index.php?action=export">export BD</a></li>

            </ul>

            <ul class="nav pull-right">
                <li class="">
                    <a class="nav"  href="../index.php"> <span>Gestion des services</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>


    <div class="row-fluid">
				<div class="well  center login-box">
                <?php if(!isset($_GET['action']) || ($_GET['action']!="import" && $_GET['action']!="export" )): ?>
					<form class="" action="" method="post">
						<fieldset class="border">
						<legend class="border">connexion à la base de données</legend>
                            <label><div class="input-prepend control-group <?php if($conn)echo "success";?>" id="host-ctrl" title="Host" data-rel="tooltip">
								<span class="add-on">Hôte</span>
								<input autofocus class="input-large span10" name="host" id="host" type="text" value="<?php echo HOST;?>" />
							</div></label>
							<div class="clearfix"></div>
							
							<label>
							<div class="input-prepend control-group <?php if($conn)echo "success";?>" id="user-ctrl" title="User" data-rel="tooltip">
								<span class="add-on">Utilisateur</span>
								<input class="input-large span10" name="user" id="user" type="text" value="<?php echo USER;?>" />
							</div></label>
							<div class="clearfix"></div>

                            <label><div class="input-prepend control-group <?php if($conn)echo "success";?>" id="pass-ctrl" title="Password" data-rel="tooltip">
								<span class="add-on">Mot de passe</span>
								<input class="input-large span10" name="password" id="password" type="password" value="<?php echo PASSWORD?>" />
							</div></label>
							<div class="clearfix"></div>

                            <label><div class="input-prepend control-group <?php if($conn && $db)echo "success";?>" id="db-ctrl" title="Database" data-rel="tooltip">
								<span class="add-on">Base de données</span>
								<input class="input-large span10" name="database" id="database" type="text" value="<?php echo DATABASE;?>" />
							</div></label>
							<label>Nouvel base de données? 
							<input id="newDB" type="checkbox" name="newDB"/>
							</label>
							<div class="clearfix"></div>
							


							<hr>
							
							<input id="submit-btn" type="submit" class="btn  btn-primary" disabled name="connection" value="Enregister"/>
							
							<a id="test-btn" class="btn  btn-info" >Tester</a>
                            <?php
                            if($conn && $db)
                                echo '<span id="msg1" class="label label-success"><i class="icon-ok icon-white"></i></span>';
                            else
                                echo '<span id="msg2" class="label label-important"><i class="icon-remove icon-white"></i></span>';
                            ?>


						</fieldset>
					</form>
                <?php elseif($_GET['action']=="import"): ?>
                        <?php
                            if(!$conn || !$db)
                                echo '
                                <div class="alert alert-error">Il faut d\'abord configurer la connexion à la base de données (<a href="index.php">connexion BD</a>)</div>
                                ';
                            else
                            {
                                if( isset($_GET['db']) && ($_GET['db']=='1' || $_GET['db']=='2' || $_GET['db']=='3' ) )
                                {
                                    if($_GET['db']=='1')
                                        $db_file='include/DB_FULL.sql';
                                    elseif($_GET['db']=='2')
                                        $db_file='include/DB_LIGHT.sql';
                                    else
                                        $db_file='include/DB_test.sql';

                                    $database = @file_get_contents($db_file);

                                    if(!$database) echo '<div class="alert alert-error">Fichier sql manquant!</div>';
                                    else
                                    {
                                        $bdd->multi_query($database);//substr($database, 3)
                                        if($bdd->errno) echo '<div class="alert alert-error">Erreurs lors de l\'import!</br>'.$conn->error.'</div>';
                                        else
                                            echo '<div class="alert alert-success">Base de données importée avec <strong>succès</strong>!</br>Il faut maintenant supprimer le dossier "install" pour des raisons de sécurité</div>';
                                    }

                                }

                            }




                        ?>
                        <h3>Importer la base de données:</h3>
                        <a  class="btn  btn-info" href="index.php?action=import&db=1" >Base de données remplit (Modules Info 2014)</a>
                        <a  class="btn  btn-info" href="index.php?action=import&db=2" >Base de données legère </a>
                        <a  class="btn  btn-info" href="index.php?action=import&db=3" >mise à jours </a>
                        <br>

                <?php else: ?>
                        <?php
                        if($_GET['action']=="export" && isset($_GET['do'])){
                            echo '<div class="alert alert-success"> ';

                            include_once('backup.php');

                            echo '</div>';
                        }
                        ?>
                        <h3>Exportation la base de données:</h3>
                        <a  class="btn  btn-info" href="index.php?action=export&do" >Exporter</a>
                <?php endif; ?>
				</div><!--/span-->
			</div><!--/row-->
			
			
	<script src="../scripts/jquery-1.11.1.min.js"></script>		
	<script>
	$(document).ready(function() {
	
	
		$("#test-btn").click(function(){
	//	alert();
		var isChecked = $('#newDB:checked').val()?1:0;
			$.post( "connection.php", { host: $("#host").val(), user: $("#user").val(), password: $("#password").val(), database: $("#database").val(), newDB: isChecked })
			.done(function( data ) {
				$("#submit-btn").prop("disabled", true);
				
				if(data==0) alert("Probleme interne...");
				if(data==1) {
					$("#user-ctrl").removeClass("success");
					$("#user-ctrl").addClass("error");
					$("#host-ctrl").removeClass("success");
					$("#host-ctrl").addClass("error");
					$("#pass-ctrl").removeClass("success");
					$("#pass-ctrl").addClass("error");
					$("#db-ctrl").removeClass("success");
					$("#db-ctrl").addClass("error");
				}
				if(data==2) {
					$("#db-ctrl").removeClass("success");
					$("#db-ctrl").addClass("error");
					
					$("#host-ctrl").removeClass("error");
					$("#host-ctrl").addClass("success");
					$("#user-ctrl").removeClass("error");
					$("#user-ctrl").addClass("success");
					$("#pass-ctrl").removeClass("error");
					$("#pass-ctrl").addClass("success");
				}
				if(data==5){
					$("#host-ctrl").removeClass("error");
					$("#host-ctrl").addClass("success");
					$("#user-ctrl").removeClass("error");
					$("#user-ctrl").addClass("success");
					$("#pass-ctrl").removeClass("error");
					$("#pass-ctrl").addClass("success");
					$("#db-ctrl").removeClass("error");
					$("#db-ctrl").addClass("success");
					
					$("#submit-btn").prop("disabled", false);
				}
				
			//	alert( "Data Loaded: " + data );
			
			
			});
		});
		

});
	</script>
</body>

</html>