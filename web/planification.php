<?php
    define("_VALID_PHP", true);
include_once 'include/connexion_BD.php';
include_once 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=2;
    $script = array("datatable","editable","select2","scripts", "preparation", "switch");
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
                        <li class="active">Planification</li>
					</ul>
			</div>
			
			<!-- Table des enseignants -->
					
			<div class="well">
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Planification des cours</h3>
                    <a class="btn btn-primary" href="planification_import.php">Importer</a>
                </div> <!-- Fin well-small-->
				
				<div class="well" >
					

				  <div class="well well-small" >
				    <h4>Semestre d'automne:</h4>   
				  </div> <!-- Fin well-small-->
				  <div class="well well-small" >
                      <div class="well well-small">
                          <form style="display:inline;" METHOD="GET" action="">
                              <label for="mod_view" style="display:inline;" >Afficher</label>

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
                            <a class="toggle-vis" data-column="2"><span class="label label-info">Semestre</span></a>
                            <a class="toggle-vis" data-column="3"><span class="label label-info">Filière</span></a>
                            <a class="toggle-vis" data-column="7"><span class="label label-info">Elements</span></a>

                        </span>
                      </div>
					<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >
					
						<thead>
							<tr>
								<th data-hide="phone,tablet">#</th>
								<th>Module</th>
                                <th title="Semestre">Sem.</th>
                                <th >Filière</th>
								<th >Sections Cours</th>
								<th >Groupes TD</th>
								<th >Groupes TP</th>
								<th >Elements</th>
								<th >Instancier</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$i=0;
						$CurYid=get_cur_year("id",$bdd);
					//	$sql = 'SELECT module_details.*,`module`.`designation`, `annee_universitaire`. `annee_univ` FROM `module_details` LEFT JOIN `module` ON module.moduleID=module_details.moduleID LEFT JOIN `annee_universitaire` ON `module_details`.`annee_UniversitaireID`=`annee_universitaire`.`annee_UniversitaireID`  WHERE `module_details`.`annee_UniversitaireID`='.$CurYid.'  AND `module_details`.`periode`= 1 ORDER BY module_DetailsID';
						$sql = 'SELECT `module`.*,F.designation AS filiere, (select `actif` from `module_actif` where module_actif.`module`=module.`moduleID` AND `annee`='.$CurYid.') AS actif FROM `module` LEFT JOIN filiere AS F ON F.filiereID=module.filiereID HAVING actif=1 AND MOD( semestre, 2 )';
                        if(!empty($_GET['filiere'])) $sql=$sql." AND filiereID=".$_GET['filiere'];
                        $res = $bdd->query($sql);
                       // echo $sql;
						if($res == TRUE)
						{
							
							while ($row = $res->fetch_assoc()) 
							{
								$sql='SELECT COUNT(*) AS elem_total FROM `element_module` WHERE `moduleID`='.$row['moduleID'];
								$res1 = $bdd->query($sql);
								if ($res1==TRUE && $res1->num_rows >0 && $elem = $res1->fetch_assoc())$elem_total=$elem['elem_total']; else $elem_total="?";
								
								$sql = 'SELECT module_details.* FROM `module_details` WHERE module_details.moduleID='.$row['moduleID'].' AND `module_details`.`annee_UniversitaireID`='.$CurYid.'  AND `module_details`.`periode`= 1';

                                $res1=$bdd->query($sql);
								if($res1 == TRUE && $res1->num_rows >0 )
								{
									$row1 = $res1->fetch_assoc();
									$sql = 'SELECT COUNT(*) AS elem_inst FROM `element_module_details` WHERE `module_DetailsID`='.$row1['module_DetailsID'];
									$res1 = $bdd->query($sql);
									if ($res1==TRUE && $res1->num_rows >0 && $elem = $res1->fetch_assoc())$elem_inst=$elem['elem_inst']; else $elem_inst="?";
									
									if(is_module_details_affected($row1['module_DetailsID'],$bdd) || is_module_details_wished($row1['module_DetailsID'],$bdd) || !$AUvalid ) $active='disabled="disabled"'; else $active="";
									echo '<tr >
									        <td>'.$row1['module_DetailsID'].'</td>
									        <td>'.$row['designation'].'</td>
									        <td>S'.$row['semestre'].'</td>
									        <td>'.$row['filiere'].'</td>
									        <td><span class="ModD_cours"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_cours" >'.$row1['grp_cours'].'</span></td>
									        <td><span class="ModD_td"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_td">'.$row1['grp_td'].'</span></td>
									        <td><span class="ModD_tp"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_tp">'.$row1['grp_tp'].'</span></td>
									        <td><a class="edit-btn" data-id="'.$row1['module_DetailsID'].'" data-periode="1" data-mod="'.$row['moduleID'].'" href="#edit" data-toggle="modal" >'.$elem_inst.'/'.$elem_total.'</a></td>
									        <td class="center "><div data-id="'.$row1['module_DetailsID'].'" class="switch"  data-on="primary" data-off="danger" data-on-label="<i class=\'icon-ok icon-white\'></i>" data-off-label="<i class=\'icon-remove icon-white\'></i>" ><input type="checkbox" checked="checked" '.$active.'></div></td>
									      </tr>';
								}else{
								$i++;
                                    if(!$AUvalid) $active='disabled="disabled"'; else $active="";
								echo '<tr >
								            <td></td><td>'.$row['designation'].'</td>
								            <td>S'.$row['semestre'].'</td>
								            <td>'.$row['filiere'].'</td>
								            <td><span id="grp_cours'.$i.'"  class="myeditable'.$i.'" data-name="grp_cours" data-value="1"></span></td>
								            <td><span id="grp_td'.$i.'"  class="myeditable'.$i.'" data-name="grp_td"></span></td>
								            <td><span id="grp_tp'.$i.'"  class="myeditable'.$i.'" data-name="grp_tp"></span></td>
								            <td><a class="edit-btn" data-id="-1" data-periode="1" data-mod="'.$row['moduleID'].'" href="#edit" data-toggle="modal" ><span id="elem_inst_'.$i.'">0</span>/'.$elem_total.'</a></td>
								            <td class="center "><div data-id="'.$i.'" data-mod="'.$row['moduleID'].'" data-periode="1" class="switch"  data-on-label="<i class=\'icon-ok icon-white\'></i>" data-on="success" data-off="danger" data-off-label="<i class=\'icon-remove icon-white\'></i>" ><input type="checkbox" '.$active.'/></div><button id="save-btn-modD'.$i.'" class="btn btn-primary hide pull-right">Instancier!</button><div style="margin-bottom: 0px;padding: 0px " id="msg'.$i.'" class="alert hide alert-error" ></div></td>
								      </tr>';
								}
							}
							$res->close();
						}
						?>
						
						</tbody>
					</table>
				 </div> <!-- Fin well-small-->
				</div> <!-- Fin well-small-->
				
				<div class="well" >
				  <div class="well well-small" >
				    <h4>Semestre de Printemps:</h4>   
				  </div> <!-- Fin well-small-->
				  <div class="well well-small" >
					<table id="liste2" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >
					
						<thead>
							<tr>
                                <th data-hide="phone,tablet">#</th>
								<th>Module</th>
                                <th title="Semestre">Sem.</th>
                                <th >Filière</th>
                                <th >Sections Cours</th>
								<th>Groupe TD</th>
								<th>Groupe TP</th>
								<th>Elements</th>
								<th>Instancier</th>
							</tr>
						</thead>
						<tbody>
						<?php
					//	$CurYid=get_cur_year("id",$bdd);
					//	$sql = 'SELECT module_details.*,`module`.`designation`, `annee_universitaire`. `annee_univ` FROM `module_details` LEFT JOIN `module` ON module.moduleID=module_details.moduleID LEFT JOIN `annee_universitaire` ON `module_details`.`annee_UniversitaireID`=`annee_universitaire`.`annee_UniversitaireID`  WHERE `module_details`.`annee_UniversitaireID`='.$CurYid.'  AND `module_details`.`periode`= 1 ORDER BY module_DetailsID';
                        $sql = 'SELECT `module`.*, F.designation AS filiere, (select `actif` from `module_actif` where module_actif.`module`=module.`moduleID` AND `annee`='.$CurYid.') AS actif FROM `module` LEFT JOIN filiere AS F ON F.filiereID=module.filiereID HAVING actif=1 AND NOT MOD( semestre, 2 )';
                        if(!empty($_GET['filiere'])) $sql=$sql." AND filiereID=".$_GET['filiere'];
                        $res = $bdd->query($sql);
						
						if($res == TRUE)
						{
							
							while ($row = $res->fetch_assoc()) 
							{
								$sql='SELECT COUNT(*) AS elem_total FROM `element_module` WHERE `moduleID`='.$row['moduleID'];
								$res1 = $bdd->query($sql);
								if ($res1==TRUE && $res1->num_rows >0 && $elem = $res1->fetch_assoc())$elem_total=$elem['elem_total']; else $elem_total="?";
								
								$sql = 'SELECT module_details.* FROM `module_details` WHERE module_details.moduleID='.$row['moduleID'].' AND `module_details`.`annee_UniversitaireID`='.$CurYid.'  AND `module_details`.`periode`= 2';

                                $res1=$bdd->query($sql);
								if($res1 == TRUE && $res1->num_rows >0 )
								{
									$row1 = $res1->fetch_assoc();
									$sql = 'SELECT COUNT(*) AS elem_inst FROM `element_module_details` WHERE `module_DetailsID`='.$row1['module_DetailsID'];
									$res1 = $bdd->query($sql);
									if ($res1==TRUE && $res1->num_rows >0 && $elem = $res1->fetch_assoc())$elem_inst=$elem['elem_inst']; else $elem_inst="?";
									
									if(is_module_details_affected($row1['module_DetailsID'],$bdd) || is_module_details_wished($row1['module_DetailsID'],$bdd)) $active='disabled="disabled"'; else $active="";
									echo '<tr >
									        <td>'.$row1['module_DetailsID'].'</td>
									        <td>'.$row['designation'].'</td>
									        <td>S'.$row['semestre'].'</td>
									        <td>'.$row['filiere'].'</td>
									        <td><span class="ModD_cours"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_cours" >'.$row1['grp_cours'].'</span></td>
									        <td><span class="ModD_td"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_td" >'.$row1['grp_td'].'</span></td>
									        <td><span class="ModD_tp"  data-pk="'.$row1['module_DetailsID'].'" data-name="ModD_tp" >'.$row1['grp_tp'].'</span></td>
									        <td><a class="edit-btn" data-id="'.$row1['module_DetailsID'].'" data-periode="'.$row1['periode'].'" data-mod="'.$row['moduleID'].'" href="#edit" data-toggle="modal" >'.$elem_inst.'/'.$elem_total.'</a></td>
									        <td class="center "><div data-id="'.$row1['module_DetailsID'].'" class="switch"  data-on="primary" data-off="danger" data-on-label="<i class=\'icon-ok icon-white\'></i>" data-off-label="<i class=\'icon-remove icon-white\'></i>" ><input type="checkbox" checked="checked" '.$active.'></div></td>
									      </tr>';
								}else{
                                    if(!$AUvalid) $active='disabled="disabled"'; else $active="";
								$i++;
								echo '<tr >
								        <td></td>
								        <td>'.$row['designation'].'</td>
								        <td>S'.$row['semestre'].'</td>
								        <td>'.$row['filiere'].'</td>
								        <td><span id="grp_cours'.$i.'"  class="myeditable'.$i.'" data-name="grp_cours" data-value="1"></span></td>
								        <td><span id="grp_td'.$i.'"  class="myeditable'.$i.'" data-name="grp_td"></span></td>
								        <td><span id="grp_tp'.$i.'"  class="myeditable'.$i.'" data-name="grp_tp"></span></td>
								        <td><a class="edit-btn" data-id="-1" data-periode="2" data-mod="'.$row['moduleID'].'" href="#edit" data-toggle="modal" ><span id="elem_inst_'.$i.'">0</span>/'.$elem_total.'</a></td>
								        <td class="center "><div data-id="'.$i.'" data-mod="'.$row['moduleID'].'" data-periode="2" class="switch"  data-on-label="<i class=\'icon-ok icon-white\'></i>" data-on="success" data-off="danger" data-off-label="<i class=\'icon-remove icon-white\'></i>" ><input type="checkbox" '.$active.'/></div><button id="save-btn-modD'.$i.'" class="btn btn-primary hide pull-right">Instancier!</button><div style="margin-bottom: 0px;padding: 0px " id="msg'.$i.'" class="alert hide alert-error" ></div></td>
								      </tr>';
								}
							}
							$res->close();
						}
						?>
						
						</tbody>
					</table>
				 </div> <!-- Fin well-small-->
				</div> <!-- Fin well-small-->   
				
				
				</div> <!-- Fin well -->

			


			<!-- Modal messages : Element-->
			
			    <div id="edit" class="modal hide fade" style="width: 750px;margin-left:-375px;margin-top: -239px;top: 50%;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Elements du modules</h3>
					</div>
					<form id="form-modf" method="POST" action="process_gestion.php" >
					<div class="modal-body" style="height: 300px;">
						
							
						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>

					</div>
					</form>
				</div>
			
			<!-- END of Modal messages : Modifier -->
			
			



    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.' (Automne)"';?>;

        var message2=document.getElementById('title').innerHTML;
        message2+=<?php echo '"  '.$CurYear_des.' (Printemps)"';?>;

    </script>
		
<?php

include('include/scripts.php');
include('include/footer.php');
} else {
   header('location:login.php');
}
?>