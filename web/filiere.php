<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=8;
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
                            Filères
                        </li>
					</ul>
			</div>
			
			<!-- -->
					
			<div class="well">
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Liste des filières</h3>
                    <?php  if($AUvalid):?>
                    <a style="margin-top:8px;" class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a>
                    <?php  endif;?>
                </div>
				
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
                          <a class="toggle-vis" data-column="1"><span class="label label-info">Designation</span></a>
                          <a class="toggle-vis" data-column="2"><span class="label label-info">Cycle</span></a>
                          <a class="toggle-vis" data-column="3"><span class="label label-info">Departement</span></a>
                          <a class="toggle-vis" data-column="4"><span class="label label-info">Status</span></a>


                      </span>
					</div>

                    <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >
                        <thead>
                        <tr>
								<th data-hide="phone,tablet" >#</th>
								<th>Designation</th>
								<th>Cycle</th>
								<th>Departement</th>
								<th>Status</th>
						</tr>
						</thead>
						<tbody>
						<?php
                        $CurYear=get_cur_year("id",$bdd);
                   //     $sql = 'SELECT filiere.*,departement.designation as dept,cycle.designation as cycle FROM `filiere` LEFT JOIN `departement` ON filiere.departementID = departement.departementID LEFT JOIN `cycle` ON filiere.cycleID = cycle.cycleID WHERE filiere.status="'.$_GET['filtre'].'" ORDER BY filiere.filiereID';
                        $sql = 'SELECT filiere.*,departement.designation as dept,cycle.designation as cycle,  (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`='.$CurYear.') AS actif FROM `filiere` LEFT JOIN `departement` ON filiere.departementID = departement.departementID LEFT JOIN `cycle` ON filiere.cycleID = cycle.cycleID WHERE filiere.annee<='.$CurYear;

                        if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
                        {
                            if($_GET['filtre']=="inactif")
                                $sql =$sql.' HAVING (actif=0 OR actif is null)';
                            else
                                $sql =$sql.' HAVING actif=1';
                        }



                        $res = $bdd->query($sql);
						
						if($res == TRUE)
						{
							while ($row = $res->fetch_assoc()) 
							{
								$id=$row['filiereID'];
                                $actif_filiere=($row['actif']==1)?"actif":"inactif";
								echo '<tr >
								            <td>'.$id.'</td>
								            <td><span class="filiere_designation" data-pk="'.$id.'" data-name="filiere_designation">'.$row['designation'].'</span></td>
								            <td><span class="filiere_cycle" data-pk="'.$id.'" data-name="filiere_cycle" data-value="'.$row['cycleID'].'">'.$row['cycle'].'</span></td>
								            <td><span class="filiere_dept" data-pk="'.$id.'" data-name="filiere_dept" data-value="'.$row['departementID'].'">'.$row['dept'].'</span></td>
								            <td><span class="filiere_status label label-'.label($actif_filiere).'"  data-pk="'.$id.'" data-name="filiere_status" data-value="'.$actif_filiere.'">'.$actif_filiere.'</span></td>
								     </tr>';
							}
							$res->close();
						}
						?>
						
						</tbody>
					</table>
				</div>
					
			</div> <!-- Fin Table-->



    <?php  if($AUvalid):?>
			<!-- Modal messages : Ajouter-->
			
			    <div id="ajout" class="modal hide fade">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Ajouter</h3>
					</div>
                    <form id="form-ajout" method="POST" action="process_gestion.php">
					<div class="modal-body">

							<fieldset>
							<legend>Info</legend>
							<input type="hidden" name="type" value="filiere">
							<input type="hidden" name="op" value="ajouter">
							<label for="designation_A">Designation</label>
							<input type="text" name="designation" value="" id="designation_A" required>
							<label for="cycle_A">Cycle</label>
							<select name="cycle" id="cycle_A">
								<?php 
								$sql='SELECT `cycleID`,`designation`, (select `actif` from `cycle_actif` where `cycle_actif`.`cycle`=`cycle`.`cycleID` AND `annee`='.$CurYear.') AS actif FROM `cycle` WHERE `cycle`.annee<='.$CurYear.' HAVING actif=1';
								$res=$bdd->query($sql);
                                if($res==TRUE)
                                {
                                    while ($row = $res->fetch_assoc())
                                        echo '<option value="'.$row['cycleID'].'">'.$row['designation'].'</option>';
                                    $res->close();
                                }

								?>
							</select>
							<label for="dept_A">Departement</label>
							<select name="dept" id="dept_A">
								<?php 
								$sql='SELECT `departementID`,`designation`, (select `actif` from `departement_actif` where `departement_actif`.`departement`=`departement`.`departementID` AND `annee`='.$CurYear.') AS actif FROM `departement` WHERE `departement`.annee<='.$CurYear.' HAVING actif=1';
								$res=$bdd->query($sql);
                                if($res==TRUE)
                                {
                                    while ($row = $res->fetch_assoc())
                                        echo '<option value="'.$row['departementID'].'">'.$row['designation'].'</option>';
                                    $res->close();
                                }

								?>
							</select>
							<label style="display:inline-block;" for="null_A">Null</label>
							<input type="checkbox" id="null_A" name="null"/>
							</fieldset>

					</div>
					<div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="Ajouter"/>
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

        $(document).ready(function() {
            //Filieres
            $('.filiere_designation').editable({
                type: 'text',
                url: 'process_gestion.php',
                title: 'Entrer la designation',
                inputclass : 'input-xlarge',
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
            $('.filiere_cycle').editable({
                type: 'select',
                url: 'process_gestion.php',
                source: 'load.php?type=cycle',
                inputclass : 'input-medium',
                title: 'Cycle?',
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
            $('.filiere_dept').editable({
                type: 'select',
                url: 'process_gestion.php',
                source: 'load.php?type=dept_null',
                inputclass : 'input-medium',
                title: 'Departement?',
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
            $('.filiere_status').editable({
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
                },
                error: function(response, newValue) {
                    //    alert(JSON.stringify(response, null, 4));
                    if(response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return response.mssg;
                    }
                }
            });

            $('#null_A').change(
                function(){
                    if ($(this).is(':checked')){
                        $('#dept_A').prop('disabled',true);
                    }
                    else {
                        $('#dept_A').prop('disabled',false);
                    }
                });
        })
    </script>
    <?php  endif;?>
<?php
    include('include/footer.php');
} else {
    header('location:login.php');
}
?>