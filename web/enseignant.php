<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=5;
    $AUvalid=!isAUvalid($bdd);
    $script = array("datatable","editable","select2","scripts", "script_enseignant");
include('include/header.php');

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');
    $CurYear_des=get_cur_year("des",$bdd);
?>
	
		<!--Body content-->
			<!-- horizontal nav -->
			
			<div>
					<ul class="breadcrumb">
						<li>
							<a href="#">Home</a> <span class="divider">/</span>
						</li>
                        <li class="active">
                            Enseignants
                        </li>
					</ul>
			</div>
			
			<!-- Table des enseignants -->
					
			<div class="well">
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Liste des enseignant</h3>
				<!--	<a style="margin-top:8px;"class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a> -->
				</div> <!-- Fin well-small-->
				
				<div class="well well-small" >
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
                          <a class="toggle-vis" data-column="5"><span class="label label-info">Email</span></a>
                          <a class="toggle-vis" data-column="6"><span class="label label-info">Status</span></a>
                            <?php  if($AUvalid):?>
                                <button id="add_prof" class="btn btn-success pull-right "><i class="icon-pencil icon-white"></i>Nouveau</button>
                            <?php  endif;?>
                      </span>

					</div>
				    <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
					
						<thead>
							<tr>
								<th data-hide="phone,tablet">#</th>
								<th>Nom</th>
								<th>Prenom</th>
								<th>Departement</th>
								<th>Grade</th>
								<th>Email</th>
								<th>Status</th>
							<!--	<th>Action</th> -->
							</tr>
						</thead>
						<tbody id="liste_prof">
						<?php
                        $CurYear=get_cur_year("id",$bdd);
						if(isset($_GET['filtre']) && ($_GET['filtre']=="inactif" || $_GET['filtre']=="actif"))
                        {
                            if($_GET['filtre']=="inactif")
						    $sql = 'SELECT E.*,departement.designation AS dept,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`="'.$CurYear.'") AS actif   FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 AND E.annee <='.$CurYear.' HAVING (actif=0 OR actif is null) ORDER BY E.enseignantID'; //E.status="'.$_GET['filtre'].'"
						    else
						    $sql = 'SELECT E.*,departement.designation AS dept,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`="'.$CurYear.'") AS actif  FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 AND E.annee <='.$CurYear.' HAVING actif=1 ORDER BY E.enseignantID';

                        }else
                            $sql = 'SELECT E.*,departement.designation AS dept,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`="'.$CurYear.'") AS actif  FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 AND E.annee <='.$CurYear.' ORDER BY E.enseignantID';
                       //   echo $sql;
                       // die();
						$res = $bdd->query($sql);
		
						if($res == TRUE)
                        {
                            while ($row = $res->fetch_assoc())
                            {
                                $id=$row['enseignantID'];
                                $actif_prof=($row['actif']==1)?"actif":"inactif";
                                $dept_id=$row['departementID'];

                                $row['Grade']="";
                                $row['grade_info']="vide";

                                $sql="SELECT * FROM `enseignant_actif` WHERE `enseignant`=".$id." AND `annee`=".$CurYear;
                                //
                                $res1=$bdd->query($sql);
                                if($res1== TRUE && $res1->num_rows>0)
                                {
                                    if($row1 = $res1->fetch_assoc()){
                                        if(!empty($row1['grade'])){
                                            $sql="SELECT * FROM `grade` WHERE `gradeID`=".$row1['grade'];
                                            //       echo $sql; die();
                                            $res1=$bdd->query($sql);
                                            if($res1== TRUE && $res1->num_rows>0 && $row1 = $res1->fetch_assoc()){
                                                $row['Grade']=$row1['code'];
                                                $row['grade_info']=$row1['designation'];
                                                $row['grade_id'] = $row1['gradeID'];
                                            }
                                        }


                                    }
                                }

                                echo '<tr>
							<td>'.$id.'</td>
							<td><span class="prof_nom" data-pk="'.$id.'" data-name="prof_nom">'.$row['nom'].'</span></td>
							<td><span class="prof_prenom" data-pk="'.$id.'" data-name="prof_prenom">'.$row['prenom'].'</span></td>
							<td><span class="prof_dept" data-pk="'.$id.'" data-value="'.$dept_id.'" data-name="prof_dept">'.$row['dept'].'</span></td>
							<td><span class="prof_grade"  data-pk="'.$id.'" data-name="prof_grade" data-value="'. $row['grade_id'].'">'.$row['Grade'].'</span><span style="margin-left:5px;" class="tip_top icon icon-info-sign"  data-container="body" data-toggle="tooltip" data-original-title="'.$row['grade_info'].'"></span></td>
							<td><span class="prof_email" data-type="email" data-pk="'.$id.'" data-name="prof_email">'.$row['email'].'</span></td>
							<td><span class="prof_status label label-'.label($actif_prof).'"  data-pk="'.$id.'" data-name="prof_status"  data-value="'.$actif_prof.'">'.$actif_prof.'</span></td></tr>';
                            }
                            $res->close();
                        }
						?>
						
						</tbody>
					</table>
				<!--	<button id="enable" class="btn tip_top" data-original-title="inline edition">enable / disable</button> -->
				</div> <!-- Fin well-small-->
			</div> <!-- Fin table-->
			
			
			
			
		<div class="well">
			<div class="well well-small" >
					<h3 style="display:inline-block;">Liste des vacataires</h3>
				<!--	<a style="margin-top:8px;"class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a> -->
				</div> <!-- Fin well-small-->
				
				<div class="well well-small" >
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
                          <a class="toggle-vis1" data-column="0"><span class="label label-info">#</span></a>
                          <a class="toggle-vis1" data-column="1"><span class="label label-info">Nom</span></a>
                          <a class="toggle-vis1" data-column="2"><span class="label label-info">Prenom</span></a>

                          <a class="toggle-vis1" data-column="3"><span class="label label-info">Grade</span></a>
                          <a class="toggle-vis1" data-column="4"><span class="label label-info">Email</span></a>
                          <a class="toggle-vis1" data-column="5"><span class="label label-info">Status</span></a>
                            <?php  if($AUvalid):?>
                                <button id="add_vacataire" class="btn btn-success pull-right "><i class="icon-pencil icon-white"></i>Nouveau</button>
                            <?php  endif;?>
                      </span>

					</div>
				    <table id="listeV" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
					
						<thead>
							<tr>
								<th data-hide="phone,tablet" >#</th>
								<th>Nom</th>
								<th>Prenom</th>
								<th>Grade</th>
								<th>Email</th>
								<th title="ActiF cette année?">Status</th>
							<!--	<th>Action</th> -->
							</tr>
						</thead>
						<tbody id="liste_vacataire">
						<?php
/*
						if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
						$sql = 'SELECT vacataire.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info  FROM `vacataire` LEFT JOIN `departement` ON vacataire.departementID = departement.departementID LEFT JOIN `grade` ON vacataire.grade = grade.gradeID WHERE vacataire.annee='.$CurYear.' AND vacataire.status="'.$_GET['filtre'].'" ORDER BY vacataire.enseignantID';
						else
*/
                        if(isset($_GET['filtre']) && ($_GET['filtre']=="inactif" || $_GET['filtre']=="actif"))
                        {
                            if($_GET['filtre']=="inactif")
                                $sql = 'SELECT vacataire.*,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`="'.$CurYear.'") AS actif  FROM `vacataire` LEFT JOIN `grade` ON vacataire.grade = grade.gradeID WHERE vacataire.annee <='.$CurYear.' HAVING (actif=0 OR actif is null) ORDER BY vacataire.enseignantID DESC';
                          //      $sql = 'SELECT vacataire.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `vacataire` LEFT JOIN `departement` ON vacataire.departementID = departement.departementID LEFT JOIN `grade` ON vacataire.grade = grade.gradeID  HAVING (actif=0 OR actif is null) ORDER BY vacataire.enseignantID DESC';
                          //      $sql = 'SELECT E.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$CurYear.') AS actif   FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 HAVING (actif=0 OR actif is null) ORDER BY E.enseignantID'; //E.status="'.$_GET['filtre'].'"
                            else
                  //              $sql = 'SELECT E.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 HAVING actif=1 ORDER BY E.enseignantID';
                  //              $sql = 'SELECT vacataire.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `vacataire` LEFT JOIN `departement` ON vacataire.departementID = departement.departementID LEFT JOIN `grade` ON vacataire.grade = grade.gradeID  HAVING actif=1 ORDER BY vacataire.enseignantID DESC';
                                $sql = 'SELECT vacataire.*,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`="'.$CurYear.'") AS actif  FROM `vacataire` LEFT JOIN `grade` ON vacataire.grade = grade.gradeID  HAVING actif=1 ORDER BY vacataire.enseignantID DESC';
                        }else
                      //    $sql = 'SELECT E.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `enseignant` AS E LEFT JOIN `departement` ON E.departementID = departement.departementID LEFT JOIN `grade` ON E.grade = grade.gradeID WHERE E.vacataire=0 ORDER BY E.enseignantID';
                      //    $sql = 'SELECT vacataire.*,departement.designation AS dept,departement.chef_enseignantID,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`='.$CurYear.') AS actif  FROM `vacataire` LEFT JOIN `departement` ON vacataire.departementID = departement.departementID LEFT JOIN `grade` ON vacataire.grade = grade.gradeID  ORDER BY vacataire.enseignantID DESC'; //WHERE vacataire.annee='.$CurYear.'
                          $sql = 'SELECT vacataire.*,grade.gradeID,grade.code as Grade, grade.designation as grade_info,(select `actif` from `enseignant_actif` where `enseignant`=vacataire.`enseignantID` AND `annee`="'.$CurYear.'") AS actif  FROM `vacataire` LEFT JOIN `grade` ON vacataire.grade = grade.gradeID WHERE vacataire.annee <='.$CurYear.' ORDER BY vacataire.enseignantID DESC'; //WHERE vacataire.annee='.$CurYear.'
                  //      echo $sql;
                        $res = $bdd->query($sql);
		
						if($res == TRUE)
                        {
                            while ($row = $res->fetch_assoc())
                            {
                                $id=$row['enseignantID'];
                                $actif_prof=($row['actif']==1)?"actif":"inactif";

                                $row['Grade']="";$row['grade_info']="vide";
                                $sql="SELECT * FROM `enseignant_actif` WHERE `enseignant`=".$id." AND `annee`=".$CurYear;
                                //
                                $res1=$bdd->query($sql);
                                if($res1== TRUE && $res1->num_rows>0)
                                {
                                    if($row1 = $res1->fetch_assoc()){
                                        if(!empty($row1['grade'])){
                                            $sql="SELECT * FROM `grade` WHERE `gradeID`=".$row1['grade'];
                                            //       echo $sql; die();
                                            $res1=$bdd->query($sql);
                                            if($res1== TRUE && $res1->num_rows>0 && $row1 = $res1->fetch_assoc()){
                                                $row['Grade']=$row1['code'];
                                                $row['grade_info']=$row1['designation'];
                                            }
                                        }


                                    }
                                }

                                echo '
                                <tr>
                                    <td>'.$id.'</td>
                                    <td><span class="prof_nom" data-pk="'.$id.'" data-name="prof_nom">'.$row['nom'].'</span></td>
							        <td><span class="prof_prenom" data-pk="'.$id.'" data-name="prof_prenom">'.$row['prenom'].'</span></td>
							        <td><span class="prof_grade"  data-pk="'.$id.'" data-name="prof_grade" data-value="'. $row['gradeID'].'">'.$row['Grade'].'</span><span style="margin-left:5px;" class="tip_top icon icon-info-sign"  data-container="body" data-toggle="tooltip" data-original-title="'.$row['grade_info'].'"></span></td>
							        <td><span class="prof_email" data-type="email" data-pk="'.$id.'" data-name="prof_email">'.$row['email'].'</span></td>
							        <td><span class="prof_status label label-'.label($actif_prof).'"  data-pk="'.$id.'" data-name="prof_status" data-value="'.$actif_prof.'">'.$actif_prof.'</span></td>
							    </tr>';
                            }                                                                                                                                     //  <td><span class="prof_dept" data-pk="'.$id.'" data-name="prof_dept">'.$row['dept'].'</span></td>
                            $res->close();
                        }
						?>
						
						</tbody>
					</table>
				<!--	<button id="enable" class="btn tip_top" data-original-title="inline edition">enable / disable</button> -->
				</div> <!-- Fin well-small-->
			</div> <!-- Fin table-->
			
			



    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.' (ENSEIGNANTS)"';?>;

        var message2=document.getElementById('title').innerHTML;
        message2+=<?php echo '"  '.$CurYear_des.' (VACATAIRES)"';?>;

    </script>
		
<?php

include('include/scripts.php');
    ?>

    <script>
        $(document).ready(function(){
            var table = $('#listeV').DataTable( {
                "iDisplayLength": window.length,
                "aaSorting": [[ 0, "desc" ]]

            } );
            $('#listeV').on( 'length.dt', function ( e, settings, len ) {

                http.open("GET", "process_gestion.php?ePP="+len, true);

                http.send(null);
            } );
            $('a.toggle-vis1').on( 'click', function (e) {
                e.preventDefault();

                // Get the column API object
                var column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
                $( this ).children().toggleClass("label-info");
            } );
        });
    </script>

<?php
include('include/footer.php');
} else {
   header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
// Note: $_SERVER['REQUEST_URI'] is your current page
}
?>