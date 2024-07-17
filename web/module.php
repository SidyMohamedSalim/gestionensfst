<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=6;
    $script = array("datatable","editable","scripts","script_module","select2", "wizard");
include('include/header.php');

    $AUvalid=!isAUvalid($bdd);

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');
    $CurYear=get_cur_year("id",$bdd);
    $CurYear_des=get_cur_year("des",$bdd);

    if(!empty($_GET['filiere'])) $_GET['filiere']= sanitize($_GET['filiere'],false,true);
?>
	
		<!--Body content-->
			<!-- horizontal nav -->
			
			<div>
					<ul class="breadcrumb">
						<li>
							<a href="#">Home</a> <span class="divider">/</span>
						</li>
                        <li class="active">
							Modules
						</li>
					</ul>
			</div>
			
			<!-- -->
					
			<div class="well">
			
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Liste des Modules</h3>
                    <?php  if($AUvalid):?>
					<button style="margin-top:8px;" id="open-wizard" class="btn btn-success btn-large pull-right"><i class="icon-pencil icon-white"></i>Nouveau</button>
                    <?php  endif; ?>
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
							<select name="filiere" id="sel_filiere" >
								<option value=""></option>
								<?php
                                $sql='SELECT `filiereID`,`designation`, (SELECT `actif` FROM `filiere_actif` where filiere=filiereID AND annee='.$CurYear.') AS actif FROM  `filiere` WHERE `filiere`.annee<='.$CurYear.' HAVING actif=1';
                                $res = $bdd->query($sql);
								if($res)
								while ($row = $res->fetch_assoc()) 
								{
									echo '<option value="'.$row['filiereID'].'"';
									if(isset($_GET['filiere']) && $_GET['filiere'] ==$row['filiereID']) echo 'selected';
									echo '>'.$row['designation'].'</option>';
								}
					
								?>
							</select>
							<input type="submit" value="Recharger">
						</form>
                        <span class="pull-right">
                          <a class="toggle-vis" data-column="0"><span class="label label-info">#</span></a>
                          <a class="toggle-vis" data-column="1"><span class="label label-info">Designation</span></a>
                          <a class="toggle-vis" data-column="2"><span class="label label-info">Filière</span></a>
                          <a class="toggle-vis" data-column="3"><span class="label label-info">Semestre</span></a>
                          <a class="toggle-vis" data-column="4"><span class="label label-info">Elements</span></a>
                          <a class="toggle-vis" data-column="5"><span class="label label-info">Status</span></a>

                      </span>
					</div>
				    <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
					
						<thead>
							<tr>
								<th data-hide="phone,tablet">#</th>
							<!--	<th>Code</th>  -->
								<th>Designation</th>
								<th>Filière</th>
								<th>Semestre</th>
								<th>Elements</th>
								<th>Status</th>
								
							</tr>
						</thead>
						<tbody>
						<?php

						if(isset($_GET['filtre']) && ($_GET['filtre']=="actif" || $_GET['filtre']=="inactif"))
						{
                            if($_GET['filtre']=="inactif")
                            {
                                $sql = 'SELECT MD.*, `filiere`.`designation` AS filiere, (select `actif` from `module_actif` where `module`=MD.`moduleID` AND `annee`='.$CurYear.') AS actif FROM `module` AS MD LEFT JOIN `filiere` ON `filiere`.`filiereID`=MD.`filiereID`  WHERE MD.annee<='.$CurYear;
                                if(!empty($_GET['filiere'])) $sql=$sql." AND MD.filiereID=".$_GET['filiere'];
                                $sql=$sql." HAVING (actif=0 OR actif is null)";
                            }
                            else
                            {
                                $sql = 'SELECT MD.*, `filiere`.`designation` AS filiere, (select `actif` from `module_actif` where `module`=MD.`moduleID` AND `annee`='.$CurYear.') AS actif FROM `module` AS MD LEFT JOIN `filiere` ON `filiere`.`filiereID`=MD.`filiereID` WHERE MD.annee<='.$CurYear;
                                if(!empty($_GET['filiere'])) $sql=$sql." AND MD.filiereID=".$_GET['filiere'];
                                $sql=$sql." HAVING actif=1";
                            }

						//	$sql = 'SELECT module.*, `filiere`.`designation` AS filiere FROM `module` LEFT JOIN `filiere` ON `filiere`.`filiereID`=`module`.`filiereID` WHERE module.status="'.$_GET['filtre'].'" ';
						//	if(!empty($_GET['filiere'])) $sql=$sql." AND module.filiereID=".$_GET['filiere']." ORDER BY module.moduleID";
						}else
						{
						//	$sql = 'SELECT module.*, `filiere`.`designation` AS filiere FROM `module` LEFT JOIN `filiere` ON `filiere`.`filiereID`=`module`.`filiereID` ';
						//	if(!empty($_GET['filiere'])) $sql=$sql." WHERE module.filiereID=".$_GET['filiere']." ORDER BY module.moduleID";
                            $sql = 'SELECT MD.*, `filiere`.`designation` AS filiere, (select `actif` from `module_actif` where `module`=MD.`moduleID` AND `annee`='.$CurYear.') AS actif FROM `module` AS MD LEFT JOIN `filiere` ON `filiere`.`filiereID`=MD.`filiereID` WHERE MD.annee<='.$CurYear;
                            if(!empty($_GET['filiere'])) $sql=$sql." AND MD.filiereID=".$_GET['filiere'];

                        }
                        $sql.=" ORDER BY MD.moduleID";
                    //    echo $sql;
						$res = $bdd->query($sql);
						
						if($res == TRUE)
						{
							while ($row = $res->fetch_assoc()) 
							{
								$id=$row['moduleID'];
                                $actif_mod=($row['actif']==1)?"actif":"inactif";
								$sql="SELECT COUNT(*) AS nb_elem FROM `element_module` WHERE element_module.annee<=".$CurYear." AND `moduleID`=".$id;
								$res1 = $bdd->query($sql);
								if($res1 == TRUE)
								{
									if($row1 = $res1->fetch_assoc()) $nb_elem=$row1['nb_elem'];
									else $nb_elem=0;
								
									echo '<tr >
									            <td>'.$id.'</td>
									<!--        <td><span class="module_code" data-pk="'.$id.'" data-name="module_code">'.$row['code'].'</span></td> -->
									            <td><span class="module_designation" data-pk="'.$id.'" data-name="module_designation">'.$row['designation'].'</span></td>
									            <td><span class="module_filiere" data-pk="'.$id.'" data-name="module_filiere" data-value="'. $row['filiereID'].'">'.$row['filiere'].'</span></td>
									            <td><span class="module_sem" data-pk="'.$id.'" data-name="module_sem">'.$row['semestre'].'</span></td>
									            <td> <a class="edit_elem" data-id="'.$id.'" href="#edit_elem" data-toggle="modal" >'.$nb_elem.'</a> </td>
									            <td class="center "><span class="module_status label label-'.label($actif_mod).'"  data-pk="'.$id.'" data-name="module_status" data-value="'.$actif_mod.'">'.$actif_mod.'</span></td>
									      </tr>
									      ';
								}
							}
							$res->close();
						}
						?>
						
						</tbody>
					</table>
				</div> <!-- Fin well-small-->
					
			</div>
			
			<!-- -->
			
			<!-- Modal messages : Modifier-->
			
			    <div id="edit_elem" data-mod="<?php if(isset($_GET['mod'])) echo $_GET['mod']; else echo "-1"; ?>" class="modal hide fade" style="width: 750px;margin-left:-375px;margin-top: -239px;top: 50%;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Details du Module</h3>
					</div>
					<div class="modal-body" style="height: 300px;">
						
							
						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
					</div>
				</div>
			
			<!-- END of Modal messages : Modifier -->
    <?php  if($AUvalid):?>
			<!-- Wizard :  -->
			<style type="text/css">
				.wizard-steps {width:20%;}
			</style>
			<div class="wizard" id="wizard_mod_ajout">

				<h1>Nouveau module</h1>

				<div class="wizard-card" data-cardname="card1">

					<h3>Module:</h3>
						<input type="hidden" name="wizard_mod_ajout"/>
						<label for="mod_designation">Designation:</label>
						<input class="input-xlarge" type="text" name="mod_designation" id="mod_designation" data-validate="not_empty_str" />


						<label for="mod_code">Code:</label>
						<input type="text" name="mod_code" id="mod_code"/>
						<label for="mod_filiere">Filière:</label>
						<select name="mod_filiere" id="mod_filiere" >
							
							<?php
								$sql='SELECT `filiereID`,`designation`, (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`='.$CurYear.') AS actif FROM `filiere` WHERE `filiere`.annee<='.$CurYear.' HAVING actif=1 ';

                                $res = $bdd->query($sql);
								if($res == TRUE)
								while ($row = $res->fetch_assoc()) 
								{
									echo '<option value="'.$row['filiereID'].'">'.$row['designation'].'</option>';
								}
					
							?>
						</select>
						<label for="mod_sem">Semestre d'étude:</label>
						<input style="width: 25px;" type="number" name="mod_sem" id="mod_sem" onkeypress="return isNumberKey(event)" data-validate="not_empty_nbr" />

                </div>

				<div class="wizard-card" data-cardname="card2">

					<h3>Elements</h3>
						<input type="hidden" name="i" id="i" value="1"/>
						<table id="elements" cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Designation</th>
										<th>Heures Cours</th>
										<th>Heures TD</th>
										<th>Heures TP</th>
										<th>Departement</th>
									</tr>
								</thead>
								<tbody id="elems">
									<tr>
										<td><input type="text" class="input-medium" name="elem1_designation" id="elem1_designation" data-validate="not_empty_str"  /></td>
										<td><input type="number" style="width: 35px;" name="elem1_cours" id="elem1_cours" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>
										<td><input type="number" style="width: 35px;" name="elem1_td" id="elem1_td" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>
										<td><input type="number" style="width: 35px;" name="elem1_tp" id="elem1_tp" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>
										<td>
											<select class="input-medium" name="elem1_dept" id="elem1_dept" >
											<?php
									//			$sql='SELECT `departementID`,`designation` FROM  `departement` ';
												$sql='SELECT DP.*,(select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$CurYear.') AS actif FROM departement AS DP WHERE DP.annee<='.$CurYear.' HAVING actif=1 ';
												$res = $bdd->query($sql);
												if($res == TRUE)
												while ($row = $res->fetch_assoc()) 
												{
													echo '<option value="'.$row['departementID'].'">'.$row['designation'].'</option>';
												}
					
												?>
											</select>
										</td>
									</tr>
								</tbody>
						</table>
						<a  id="add_elem" class="btn btn-inverse pull-right"><i class=" icon-plus-sign icon-white"></i></a>
						<a  id="drop_elem" class="btn btn-inverse pull-right disabled"><i class=" icon-minus-sign icon-white"></i></a>

                </div>


			    <div class="wizard-success">
					<div class="alert alert-success">
						Le module a été ajouté <strong>avec succès</strong>
					</div>
		
					<a class="btn ajouter_nouv">Ajouter un autre module</a>
					<span style="padding:0 10px">ou</span>
					<a class="btn fini">Terminer</a>
				</div>

				<div class="wizard-error">
					<div class="alert alert-error">
						Erreurs...
					</div>
				</div>

				<div class="wizard-failure">
					<div class="alert alert-error">
					Problème lors de l'envois des information..
					</div>
				</div>
			</div>
			<!-- END Wizard :  -->
    <?php  endif;?>




<script>
    var message1=document.getElementById('title').innerHTML;
    message1+=<?php echo '"  '.$CurYear_des.'"';?>;

</script>
<?php

include('include/scripts.php');
include('include/footer.php');

} else {
   header('location:login.php');
}
?>