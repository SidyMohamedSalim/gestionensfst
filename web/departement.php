<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=7;
    $script = array("datatable","editable","scripts");
include('include/header.php');
    $AUvalid=!isAUvalid($bdd);


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
                            Départements
                        </li>
					</ul>
			</div>
			
			<!-- -->
					
			<div class="well">
			
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Liste des departements</h3>
                    <?php  if($AUvalid):?>
                    <a style="margin-top:8px;" class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a>
                    <?php  endif;?>
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
					</div>
					
				    <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >
					
						<thead>
							<tr>
								<th>#</th>
								<th>Designation</th>
								<th>Chef</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php
                        $CurYear=get_cur_year("id",$bdd);
						if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
                        {
                            if($_GET['filtre']=="inactif")
                                $sql = 'SELECT DP.*,(SELECT `enseignant` FROM `departement_chef` WHERE `departement`=DP.`departementID` AND `annee`='.$CurYear.' ) AS chef, (select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$CurYear.') AS actif FROM `departement` AS DP WHERE DP.annee<='.$CurYear.' HAVING (actif=0 OR actif is null) ORDER BY DP.departementID';
                            else
                                $sql='SELECT DP.*,(SELECT `enseignant` FROM `departement_chef` WHERE `departement`=DP.`departementID` AND `annee`='.$CurYear.' ) AS chef,(select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$CurYear.') AS actif FROM `departement` AS DP WHERE DP.annee<='.$CurYear.' HAVING actif=1 ORDER BY DP.departementID';;
                        }
					    else
						    $sql = 'SELECT DP.*, (SELECT `enseignant` FROM `departement_chef` WHERE `departement`=DP.`departementID` AND `annee`='.$CurYear.' ) AS chef, (select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$CurYear.') AS actif FROM `departement` AS DP WHERE DP.annee<='.$CurYear.' ORDER BY DP.departementID';

                     //   echo $sql;
                     //   die();
						$res = $bdd->query($sql);

						if($res == TRUE)
						{
							while ($row = $res->fetch_assoc()) 
							{
                                $chef_id='';
								$id=$row['departementID'];
                                $row['actif']=($row['actif']==1)?"actif":"inactif";
                                if($row['chef'] != NULL){
                                    $sql="SELECT CONCAT(`nom`, ' ',`prenom`) AS nom_pre FROM `enseignant` WHERE `enseignantID`=".$row['chef'];
                                    $res1 = $bdd->query($sql);
                                    $chef_id=$row['chef'];
                                    if($res1 == TRUE && $res1->num_rows ==1)
                                    {
                                        $prof=$res1->fetch_assoc();
                                        $row['chef']=$prof['nom_pre'];

                                    }
                                    $res1->close();
                                }

								echo '<tr >
								    <td>'.$id.'</td>
								    <td><span class="designation" data-pk="'.$id.'" data-name="dept_designation">'.$row['designation'].'</span></td>
								    <td><span class="chef_dept" data-pk="'.$id.'" data-chef="'.$chef_id.'" data-value="'.$chef_id.'" data-name="dept_chef">'.$row['chef'].'</span></td>
								    <td><span class="dept_status label label-'.label($row['actif']).'"  data-pk="'.$id.'" data-name="dept_status" data-value="'.$row['actif'].'">'.$row['actif'].'</span></td>
								    </tr>';
							}
							$res->close();

						}
						?>

						</tbody>
					</table>
				</div> <!-- Fin well-small-->
					
			</div>
			
			<!-- -->


    <?php  if($AUvalid):?>
			<!-- Modal messages : Ajouter-->
			
			    <div id="ajout" class="modal hide fade">
                    <form id="form-ajout" method="POST" action="process_gestion.php">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Ajouter</h3>
					</div>
					<div class="modal-body">

							<fieldset>
							<legend>Nouveau Département</legend>
							<input type="hidden" name="type" value="departement">
							<input type="hidden" name="op" value="ajouter">
							<label for="designation_A">Designation</label>
							<input type="text" name="designation" value="" id="designation_A" required>

							</fieldset>

					</div>
					<div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value="Ajouter" \>
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>

					</div>
                    </form>
				</div>
			
			<!-- END of Modal messages : Ajouter -->

    <?php  endif;?>

    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.' "';?>;

    </script>
		
<?php

include('include/scripts.php');
    ?>
    <?php  if($AUvalid):?>
    <script>
        $(document).ready(function(){
            $('.chef_dept').editable({
                type: 'select',
                url: 'process_gestion.php',
                source: 'load.php?type=enseignants',
                inputclass : 'input-large',
                params: function (params) {
                    params.chef = params.chef;
                    return params;
                },
                title: 'Chef du departement?',
                ajaxOptions: {
                    dataType: 'json' //assuming json response
                },
                success: function(response, newValue) {
                    if(!response.succes) return response.mssg;
                },
                error: function(response, newValue) {
                    if(response.status === 500)
                        return 'Service unavailable. Please try later.';
                }
            });
            $('.designation').editable({
                type: 'text',
                url: 'process_gestion.php',
                title: 'Entrer la designation',
                validate: function(value) {
                    if($.trim(value) == '') {
                        return 'Il faut remplir ce champs!';
                    }
                },
                ajaxOptions: {
                    dataType: 'json' //assuming json response
                },
                success: function(response, newValue) {
                    if(!response.succes) return response.mssg;
                },
                error: function(response, newValue) {
                    if(response.status === 500)
                        return 'Service unavailable. Please try later.';
                }
            });
            $('.dept_status').editable({
                type: 'select',
                url: 'process_gestion.php',
                source: 'load.php?type=status',
                inputclass : 'input-small',
                title: 'Status?',
                ajaxOptions: {
                    dataType: 'json' //assuming json response
                },
                success: function(response, newValue) {
                    if(!response.succes) return response.mssg;
                    else location.reload();
                }
                ,
                error: function(response, newValue) {
                    //    alert(JSON.stringify(response, null, 4));
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return response.mssg;
                    }
                }
            });
        });
    </script>
    <?php  endif;?>
<?php
include('include/footer.php');
} else {
   header('location:login.php');
}
?>