<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=9;
    $script = array("datatable","editable","scripts", "wizard");
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
					</ul>
			</div>
			
			<!-- -->
					
			<div class="well">
			
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Liste des Cycles</h3>
                    <?php  if($AUvalid):?>
                    <a style="margin-top:8px;"class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a>
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
					<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
					
						<thead>
							<tr>
								<th>#</th>
								<th>Designation</th>
								<th>Nbr de Semestre</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php
                        $CurYear=get_cur_year("id",$bdd);
						if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
                        {
                            if($_GET['filtre']=="inactif")
                                $sql = 'SELECT C.*, (select `actif` from `cycle_actif` where `cycle`=C.`cycleID` AND `annee`='.$CurYear.') AS actif  FROM cycle AS C WHERE C.annee<='.$CurYear.' HAVING (actif=0 OR actif is null)  ORDER BY C.cycleID';
                            else
                                $sql = 'SELECT C.*, (select `actif` from `cycle_actif` where `cycle`=C.`cycleID` AND `annee`='.$CurYear.') AS actif  FROM cycle AS C WHERE C.annee<='.$CurYear.' HAVING actif=1  ORDER BY C.cycleID';
                        }
						else
					    $sql = 'SELECT C.*, (select `actif` from `cycle_actif` where `cycle`=C.`cycleID` AND `annee`='.$CurYear.') AS actif  FROM cycle AS C WHERE C.annee<='.$CurYear.' ORDER BY C.cycleID';

                        $res = $bdd->query($sql);
						
						if($res == TRUE)
						{
							while ($row = $res->fetch_assoc()) 
							{
								$id=$row['cycleID'];
                                $actif_cycle=($row['actif']==1)?"actif":"inactif";
								echo '<tr>
								            <td>'.$id.'</td>
								            <td><span class="cycle_designation" data-pk="'.$id.'" data-name="cycle_designation">'.$row['designation'].'</span></td>
								            <td><span class="cycle_sem" data-pk="'.$id.'" data-name="cycle_sem">'.$row['nb_semestres'].'</span></td>
								            <td><span class="cycle_status label label-'.label($actif_cycle).'"  data-pk="'.$id.'" data-name="cycle_status" data-value="'.$actif_cycle.'">'.$actif_cycle.'</span></td>
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
			<!-- Modal messages : supprimer-->
			
			    <div id="delete" class="modal hide fade">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Confirmation</h3>
					</div>
					<div class="modal-body">
						<p>Etes-vous sûre de vouloir supprimer?</p>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
						<a href="#" class="btn btn-primary">Supprimer</a>
					</div>
				</div>
			
			<!-- END of Modal messages : supprimer-->

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
							<legend>Info</legend>
							<input type="hidden" name="type" value="cycle">
							<input type="hidden" name="op" value="ajouter">
							<label for="designation_A">Designation</label>
							<input type="text" name="designation" value="" id="designation_A" required>
							
							<label for="nb_A">Nbr de Semestre</label>
							<input type="number" onkeypress="return isNumberKey(event)" name="nb" value="" id="nb_A" required>
							</fieldset>

					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
                        <input class="btn btn-primary" type="submit"  value="Ajouter" />
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
            $('.cycle_designation').editable({
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
            $('.cycle_sem').editable({
                type: 'number',
                url: 'process_gestion.php',
                title: 'Nbr de semestres?',
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
            $('.cycle_status').editable({
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

        });
    </script>
    <?php  endif;?>
<?php
include('include/footer.php');
} else {
   header('location:login.php');
}
?>