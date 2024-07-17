<?php

    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active='M2';
    $script = array("datatable","editable", "scripts");
include('include/header.php');

    if($access != _ADMIN) header('location:index.php');
    $CurYear_des=get_cur_year("des",$bdd);
?>

		<!--Body content-->
			<!-- horizontal nav -->
			
			<div>
					<ul class="breadcrumb">
						<li>
							<a href="#">Home</a> <span class="divider">/</span>
						</li>
						<li class="active"> Config Users</li>
					</ul>
			</div>
				
			<div class="well">
			
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Gestion des utilisateurs:</h3>
				</div>
				<?PHP /*
				// The hashed password from the form
				$password = $_POST['p']; 
				// Create a random salt
				$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				// Create salted password (Careful not to over season)
				$password = hash('sha512', $password.$random_salt);
 
				// Add your insert to database script here. 
				// Make sure you use prepared statements!
				if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {    
				$insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt); 
				// Execute the prepared query.
				$insert_stmt->execute();
				$_POST['value']*/
				
				if(isset($_POST['u']))
				{
					if(isset($_POST['new']))
					{
						$error=0;
                        $_POST['u']=safe_input($_POST['u'],$bdd);
						$sql="SELECT * FROM `user` WHERE `enseignantID`=".$_POST['u'];
						$res1 = $bdd->query($sql);
						if($res1 == true && $res1->num_rows ==0)
						{
						
						
						
						$sql="SHOW TABLE STATUS LIKE 'user'";
						$res = $bdd->query($sql);
						if($res== TRUE)
						{
							$stat = $res->fetch_assoc();
							$newID=$stat['Auto_increment'];
							
							$email="";
							$sql='SELECT `email`, concat(`nom`," ", `prenom`) AS name FROM `enseignant` WHERE `enseignantID`='.$_POST['u'];
							$res = $bdd->query($sql);
							if($res== TRUE && $res->num_rows >0)
							{
								$row = $res->fetch_assoc();
								$email=$row['email'];
                                $name=$row['name'];
							}
							else{$error=4; goto apres;}
							
							include_once('include/pwgen.class.php');
							$pwgen = new PWGen();
							$pass = $pwgen->generate();
							
							$login="user".$newID;
							$password = hash('sha512', $pass);
							$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
							// Create salted password (Careful not to over season)
							$password = hash('sha512', $password.$random_salt);
							
							if ($insert_stmt = $bdd->prepare("INSERT INTO user (id, login, email, pass, salt, enseignantID) VALUES (?, ?, ?, ?, ?, ?)")) {    
							$insert_stmt->bind_param('ssssss', $newID, $login, $email, $password, $random_salt, $_POST['u']); 
							// Execute the prepared query.
							$insert_stmt->execute();
							
							$sql="INSERT INTO `configuration`(`user`, `annee_courrante`) VALUES (".$newID.",".get_cur_year("id",$bdd).")";
							$res = $bdd->query($sql);
							if($res == false || $bdd->affected_rows ==0) $error=4;

                                $message['adresse']=sanitize($email);
                                $message['nom']=sanitize($name);
                        //        $message['sujet']="Votre compte a été créé avec succès (GS_FSTF)";
                        //        $message['corp']="<ul><li>login: ".$login." </li><li>Mot de passe: ".$pass."</li></ul>";



                                $row = getRowById($bdd, "email_template", 1);

                                if($row!=false)
                                {
                                    $temp=$row->fetch_assoc();
                                    $message['sujet'] = sanitize((string) $temp['sujet']);
                                    $message['corp']= ($temp['corp']); //strip_tags

                                    $message['corp'] = str_replace(array('[login]', '[pass]'),
                                    array($login, $pass), $message['corp']);
                                }



                                echo '<div class="alert alert-block alert-success">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<h4>SUCCES!</h4>
								Le compte à été crée avec succès';

                                if(envoi_mail($message,$bdd)) echo "<p>Email de notification envoyé</p>";
                                else echo '<p class="alert alert-block alert-error" >Probleme lors de l\'envoi de l\'email!!</p>';

                                echo '</div>';

					/*			<div class="container">
								<table cellpadding="0" cellspacing="0" border="0" class="span4 table table-bordered" >
								<tr><td>Login</td><td><strong>'.$login.'</strong></td></tr>
								<tr><td>Mot de passe</td><td><strong>'.$pass.'</strong></td></tr>
								</table></div>
												</div>'; */
							}else {$error=3; }
						//	$sql='NSERT INTO `user`(`id`, `login`, `pass`, `email`, `salt`, `enseignantID`, `access`) VALUES ('.$newID.',"'..'","'..'","'..'","'..'",'.$_POST['u'].'])';
						}else{$error=2; }
						}else {$error=1; }
						
						if($error!=0) 
						{echo '<div class="alert alert-error">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													<strong>ERREUR!</strong>';
						if($error==1) echo "Un compte <strong>existe</strong> pour cet enseignant...";
							echo '</div>';
						}
					}
				apres:
					if(isset($_POST['newPass']))
					{
						
						$error=0;
                        $_POST['u']=safe_input($_POST['u'],$bdd);
						$sql="SELECT * FROM `user` WHERE `enseignantID`=".$_POST['u'];
						$res1 = $bdd->query($sql);
						if($res1 == true && $res1->num_rows >0)
						{
							$row = $res1->fetch_assoc();
							$login=$row['login'];
							include('include/pwgen.class.php');
							$pwgen = new PWGen();
							$pass = $pwgen->generate();
							
							$password = hash('sha512', $pass);
							
							$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
							// Create salted password (Careful not to over season)
							$password = hash('sha512', $password.$random_salt);

							if ($insert_stmt = $bdd->prepare("UPDATE user SET  pass=?, salt=? WHERE enseignantID = ? ")) {    
							$insert_stmt->bind_param('sss', $password, $random_salt, $_POST['u']); 
							// Execute the prepared query.
							$insert_stmt->execute();

                                $message['adresse']=sanitize($row['email']);
                                $message['nom']=sanitize($row['email']);
                       //         $message['sujet']="Nouveau mot de passe (GS_FSTF)";
                       //         $message['corp']="<p>un nouveau mot de passe a été générer pour vous par l'administrateur..</p><ul><li>login: ".$login." </li><li>Mot de passe: ".$pass."</li></ul>";

                                $row = getRowById($bdd, "email_template", 2);

                                if($row!=false)
                                {
                                    $temp=$row->fetch_assoc();
                                    $message['sujet'] = sanitize((string) $temp['sujet']);
                                    $message['corp']= ($temp['corp']); //strip_tags

                                    $message['corp'] = str_replace(array('[login]', '[pass]'),
                                        array($login, $pass), $message['corp']);
                                }

                                echo '<div class="alert alert-block alert-success">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<h4>SUCCES!</h4>
								Nouveau mot de passe généré!';

                                if(envoi_mail($message,$bdd)) echo "<p>Email de notification envoyé</p>";
                                else echo '<p class="alert alert-block alert-error" >Probleme lors de l\'envoi de l\'email!!</p>';

                                echo '</div>';
                         /*
							echo '<div class="alert alert-block alert-success">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<h4>SUCCES!</h4>
								Nouveau mot de passe générer:
								<div class="container">
								<table cellpadding="0" cellspacing="0" border="0" class="span4 table table-bordered" >
								<tr><td>Login</td><td><strong>'.$login.'</strong></td></tr>
								<tr><td>Mot de passe</td><td><strong>'.$pass.'</strong></td></tr>
								</table></div>
												</div>';*/
							}else {$erreur=3; }
						}else $error=1;
						if($error>0) 
						{echo '<div class="alert alert-error">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													<strong>ERREUR!</strong>';
						if($error==1) echo "Compte inexistant..";
							echo '</div>';
						}
						
					}
					
					if(isset($_POST['drop']))
					{
						$error="";
                        $_POST['u']=safe_input($_POST['u'],$bdd);
						$sql="SELECT `id`,`access`,`email` FROM `user` WHERE `enseignantID`=".$_POST['u'];
						$res1 = $bdd->query($sql);
						if($res1 == true && $res1->num_rows >0)
						{
							$row = $res1->fetch_assoc();

                            if($row['access']==_ADMIN){
                                $error="Impossible de supprimer le compte d'un chef de département!<br/>Il faut d'abord choisir un remplacent...";
                                goto err;
                            }

							$sql="DELETE FROM `configuration` WHERE `user`=".$row['id'];
							$res1 = $bdd->query($sql);
							if($res1 == false && $bdd->affected_rows ==0) $error="Problème lors de la suppression";
							$sql="DELETE FROM `user` WHERE `enseignantID`=".$_POST['u'];
						//	echo $sql;
							$res1 = $bdd->query($sql);
							if($res1 == true && $bdd->affected_rows >0)
							{
                                $message['adresse']=sanitize($row['email']);
                                $message['nom']=sanitize($row['email']);
                         //       $message['sujet']="Suppression du compte (GS_FSTF)";
                         //       $message['corp']="<p>votre compte a été supprimé par l'administrateur..</p>";

                                $row = getRowById($bdd, "email_template", 3);

                                if($row!=false)
                                {
                                    $temp=$row->fetch_assoc();
                                    $message['sujet'] = sanitize((string) $temp['sujet']);
                                    $message['corp']= ($temp['corp']); //strip_tags

                            /*        $message['corp'] = str_replace(array('[login]', '[pass]'),
                                        array($login, $pass), $message['corp']);
                         */     }

                                echo '<div class="alert alert-block alert-success">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<h4>SUCCES!</h4>
								Le compte à été Supprimé..';

                                if(envoi_mail($message,$bdd)) echo "<p>Email de notification envoyé</p>";
                                else echo '<p class="alert alert-block alert-error" >Probleme lors de l\'envoi de l\'email!!</p>';

                                echo '</div>';


							}
							else $error="Problème lors de la suppression du compte";
						}else $error="Compte inexistant..";

                        err:
						if(!empty($error))
						{echo '<div class="alert alert-error">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													<strong>ERREUR!</strong> ';
				//		if($error==1) echo "Compte inexistant..";
						echo $error;
							echo '</div>';
						}
					}
				
				}
				?>
				<div class="well" >
					<h4>Liste des professeurs:</h4>
					<div class="well well-small">
						<form style="display:inline;" METHOD="GET" action="">
							<label for="mod_view" style="display:inline;" >Afficher</label>
							<select id="mod_view" style="margin-bottom:0px;width:auto;" name="filtre">
								<option value="tous" <?PHP if(isset($_GET['filtre']) && $_GET['filtre'] =="tous") echo 'selected';?>>Tous</option>
								<option value="actif" <?PHP if(isset($_GET['filtre']) && $_GET['filtre']=="actif")echo 'selected';?>>Actifs</option>
								<option value="inactif" <?PHP if(isset($_GET['filtre']) && $_GET['filtre']=="inactif")echo 'selected';?>>Inactifs</option>
							</select>
							<input type="submit" value="Recharger">
						</form>
						<span class="pull-right">
                          <a class="toggle-vis" data-column="0"><span class="label label-info">#</span></a>
                          <a class="toggle-vis" data-column="1"><span class="label label-info">Nom</span></a>
                          <a class="toggle-vis" data-column="2"><span class="label label-info">Prenom</span></a>
                          <a class="toggle-vis" data-column="3"><span class="label label-info">Departement</span></a>
                          <a class="toggle-vis" data-column="4"><span class="label label-info">Grade</span></a>
                          <a class="toggle-vis" data-column="5"><span class="label label-info">Status</span></a>
                          <a class="toggle-vis" data-column="6"><span class="label label-info">Accès</span></a>
                          <a class="toggle-vis" data-column="7"><span class="label label-info">Actions</span></a>
        </span>
					</div>
					<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
					
						<thead>
							<tr>
								<th>#</th>
								<th>Nom</th>
								<th>Prenom</th>
								<th>Departement</th>
								<th>Grade</th>
								<th>Status</th>
								<th>Accès</th>
								<th>Actions</th>
							<!--	<th>Action</th> -->
							</tr>
						</thead>
						<tbody id="liste_prof">
						<?php
                        $CurYear=get_cur_year("id",$bdd);

						if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
                        {
                            if($_GET['filtre']=="inactif")
                                $sql = 'SELECT enseignant.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info, (select `actif` from `enseignant_actif` where `enseignant`=`enseignant`.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `enseignant` LEFT JOIN `departement` ON enseignant.departementID = departement.departementID LEFT JOIN `grade` ON enseignant.grade = grade.gradeID WHERE enseignant.vacataire=0  HAVING (actif=0 OR actif is null) ORDER BY enseignant.enseignantID';
                            else
                                $sql = 'SELECT enseignant.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info, (select `actif` from `enseignant_actif` where `enseignant`=`enseignant`.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `enseignant` LEFT JOIN `departement` ON enseignant.departementID = departement.departementID LEFT JOIN `grade` ON enseignant.grade = grade.gradeID WHERE enseignant.vacataire=0 HAVING actif=1 ORDER BY enseignant.enseignantID';

                        }
						else
						$sql = 'SELECT enseignant.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info, (select `actif` from `enseignant_actif` where `enseignant`=`enseignant`.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `enseignant` LEFT JOIN `departement` ON enseignant.departementID = departement.departementID LEFT JOIN `grade` ON enseignant.grade = grade.gradeID WHERE enseignant.vacataire=0 ORDER BY enseignant.enseignantID';
						
						$res = $bdd->query($sql);
		
						if($res == TRUE)
                        {
                            while ($row = $res->fetch_assoc())
                            {
                                $id=$row['enseignantID'];
                                $actif_prof=($row['actif']==1)?"actif":"inactif";
                                echo '<tr>
                                <td>'.$id.'</td>
                                <td><span class="prof_nom" data-pk="'.$id.'" data-name="prof_nom">'.$row['nom'].'</span></td>
                                <td><span class="prof_prenom" data-pk="'.$id.'" data-name="prof_prenom">'.$row['prenom'].'</span></td>
                                <td><span class="prof_dept" data-pk="'.$id.'" data-name="prof_dept">'.$row['dept'].'</span></td>
                                <td  ><span class="prof_grade"  data-pk="'.$id.'" data-name="prof_grade">'.$row['Grade'].'</span><span style="margin-left:5px;" class="tip_top icon icon-info-sign"  data-container="body" data-toggle="tooltip" data-original-title="'.$row['grade_info'].'"></span></td>
                                <td><span class="prof_status label label-'.label($actif_prof).'"  data-pk="'.$id.'" data-name="prof_status">'.$actif_prof.'</span></td>';

                                $sql="SELECT * FROM `user` WHERE `enseignantID`=".$id;
                                $res1 = $bdd->query($sql);
                                echo '<form method="POST" action="">';
                                echo '<input type="hidden" name="u" value="'.$id.'"/>';
                                if($res1 == TRUE && $res1->num_rows >0)
                                {
                                    $row1 = $res1->fetch_assoc();
                                    if($row1['access']==_ADMIN)$access=1;
                                    elseif($row1['access']==_COLLEGE)$access=2;
                                    elseif($row1['access']==_PROF)$access=3;
                                    echo '<td><span class="user_access" data-pk="'.$row1['id'].'" data-value="'.$access.'" data-name="user_access">'.$row1['access'].'</span></td>';
                                    echo '<td><input class="btn" type="submit" name="newPass" value="Générer Pass"/><input class="btn" type="submit" name="drop" value="supprimer"/></td>';// <a href="configUsers.php?u='.$id.'&a=newPass" class="btn">Generer Pass</a><a href="configUsers.php?u='.$id.'&a=drop" class="btn">supprimer</a>
                                }
                                else{
                                    echo '<td><span>Pas de compte</span></td>';
                                    echo '<td><input class="btn" type="submit" name="new" value="créer compte!"/></td>'; //<a href="configUsers.php?u='.$id.'&a=new" class="btn"> </a>
                                }
                                echo '</form>';
                                echo '</tr>';
                            }
                            $res->close();
                        }
						?>
						
						</tbody>
					</table>
				</div>
				
				
				
			</div>	
				
				
				
				
				

		
	
		
		
<?php include('include/scripts.php');?>
<script>
$(function(){

    $('.user_access').editable({
        type: 'select',
        url: 'process_gestion.php?edit=user_access',
        source: [
            {value: 1, text: '<?php echo _ADMIN;?>'},
            {value: 2, text: '<?php echo _COLLEGE;?>'},
            {value: 3, text: '<?php echo _PROF;?>'}
        ],
        title: 'Accès?',
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
    });
});


	
</script>
    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.'"';?>;

        $('.tip_top').tooltip();
    </script>
<?php
    include('include/footer.php');
} else {
    header('location:login.php');
}
?>