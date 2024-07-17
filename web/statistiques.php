<?php
    define("_VALID_PHP", true);
include_once './include/connexion_BD.php';
include_once './include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=10;
    $script = array("editable", "scripts");
include_once './include/header.php';
    /* ToDo: definir une methode isAdmin est l'inclure dans le test de login
      ToDo: redirection vers une page d'erreur avec message...

   */

    if($access != _ADMIN) header('location:index.php');
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
				
			<div class="well">
			
				<div class="well well-small" >
					<h3 style="display:inline-block;">Statistiques</h3>
				</div>
				
				<div class="well" >
					<div class="row-fluid">
					<div>
						
						<?PHP
						
						$curYear=get_cur_year("id",$bdd);
						$sql='SELECT
  SUM(element_module.heures_cours) AS hrs_cours,
  SUM(element_module.heures_td * element_module_details.grp_td) AS hrs_td,
  SUM(element_module.heures_tp * element_module_details.grp_tp) AS hrs_tp,
  COUNT(element_module.heures_cours) AS grp_cours,
  SUM(element_module_details.grp_td) AS grp_td,
  SUM(element_module_details.grp_tp) AS grp_tp
FROM element_module_details
  INNER JOIN element_module
    ON element_module_details.element_ModuleID = element_module.element_ModuleID
  INNER JOIN module_details
    ON element_module_details.module_DetailsID = module_details.module_DetailsID
WHERE module_details.annee_UniversitaireID = '.$curYear.' AND module_details.periode = 1
';
               //         echo $sql;
                  //      die();
						$res = $bdd->query($sql);
						if($res==TRUE && $row = $res->fetch_assoc())
						{
							$hrs_cours_total_fall=$row['hrs_cours'];
							$grp_cours_total_fall=$row['grp_cours'];
							$hrs_td_total_fall=$row['hrs_td'];
							$grp_td_total_fall=$row['grp_td'];
							$hrs_tp_total_fall=$row['hrs_tp'];
							$grp_tp_total_fall=$row['grp_tp'];
							
						}
						$sql='SELECT
  SUM(element_module.heures_cours) AS hrs_cours,
  SUM(element_module.heures_td * element_module_details.grp_td) AS hrs_td,
  SUM(element_module.heures_tp * element_module_details.grp_tp) AS hrs_tp,
  COUNT(element_module.heures_cours) AS grp_cours,
  SUM(element_module_details.grp_td) AS grp_td,
  SUM(element_module_details.grp_tp) AS grp_tp
FROM element_module_details
  INNER JOIN element_module
    ON element_module_details.element_ModuleID = element_module.element_ModuleID
  INNER JOIN module_details
    ON element_module_details.module_DetailsID = module_details.module_DetailsID
WHERE module_details.annee_UniversitaireID = '.$curYear.' AND module_details.periode = 2
';
						$res = $bdd->query($sql);
						if($res==TRUE && $row = $res->fetch_assoc())
						{
							$hrs_cours_total_spring=$row['hrs_cours'];
							$grp_cours_total_spring=$row['grp_cours'];
							$hrs_td_total_spring=$row['hrs_td'];
							$grp_td_total_spring=$row['grp_td'];
							$hrs_tp_total_spring=$row['hrs_tp'];
							$grp_tp_total_spring=$row['grp_tp'];
							
						}
						
					//	$heures_total=($hrs_cours_total_fall+$hrs_td_total_fall*H_TD+$hrs_tp_total_fall*H_TP)+($hrs_cours_total_spring+$hrs_td_total_spring*H_TD+$hrs_tp_total_spring*H_TP);
						$heures_total=($hrs_cours_total_fall+$hrs_td_total_fall+$hrs_tp_total_fall)+($hrs_cours_total_spring+$hrs_td_total_spring+$hrs_tp_total_spring);
						//('.round(($heures_total*(1/H_TD)),2).' HTD)
						echo '<span>Nbr total d\'heure à affecter: <strong>'.$heures_total.' HRS</strong> </span><br/>';
						
						/////*  nbr des enseignant: 1-participants dans des affectation, 2-actif  */ 
						
					//	$sql='SELECT count(*) AS NB_PROF FROM `enseignant` WHERE `enseignantID` IN (SELECT DISTINCT `enseignantID` FROM `affectation` WHERE `annee_UniversitaireID`='.$curYear.')';
						$sql='SELECT `enseignantID`,`grade` FROM `enseignant` WHERE `enseignantID` IN (SELECT DISTINCT `enseignantID` FROM `affectation` WHERE `annee_UniversitaireID`='.$curYear.')';

						$res = $bdd->query($sql);
						if($res==TRUE  )
						{
                            $NB_PROF_CM=0;
                            $NB_PROF_TD=0;
                            $NB_PROF_TP=0;
                            while($row = $res->fetch_assoc())
                            {
                                if(is_permis($row['grade'],"cours",$bdd))
                                    $NB_PROF_CM++;
                                if(is_permis($row['grade'],"TD",$bdd))
                                    $NB_PROF_TD++;
                                if(is_permis($row['grade'],"TP",$bdd))
                                    $NB_PROF_TP++;
                            }

						}
						$sql='SELECT `enseignantID`,`grade` FROM `enseignant` WHERE `enseignantID` IN (select DISTINCT `enseignant` from `enseignant_actif` where `actif`=1 AND `annee`='.$curYear.') ';
				//		echo $sql;
						$res = $bdd->query($sql);
						if($res==TRUE )
						{
						//	$NB_PROF_ACTIF=$row['NB_PROF_ACTIF'];
                            $NB_PROF_ACTIF_CM=0;
                            $NB_PROF_ACTIF_TD=0;
                            $NB_PROF_ACTIF_TP=0;
                            while($row = $res->fetch_assoc())
                            {
                                if(is_permis($row['grade'],"cours",$bdd))
                                    $NB_PROF_ACTIF_CM++;
                                if(is_permis($row['grade'],"TD",$bdd))
                                    $NB_PROF_ACTIF_TD++;
                                if(is_permis($row['grade'],"TP",$bdd))
                                    $NB_PROF_ACTIF_TP++;
                            }
						}
						
						
						
						
						
						
						
						
						
						
						////*   */ 
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="cours" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="cours" AND M.`periode`=2)
						';

						$res1 = $bdd->query($sql);
						if($row1 = $res1->fetch_assoc())
						{
							$grp_cours_aff_fall=$row1['grp_cours_aff'];
							$hrs_cours_aff_fall=$row1['hrs_cours_aff'];
						}else{
							$grp_cours_aff_fall='?';
							$hrs_cours_aff_fall=0;
						}
						if($row1 = $res1->fetch_assoc())
						{
							$grp_cours_aff_spring=$row1['grp_cours_aff'];
							$hrs_cours_aff_spring=$row1['hrs_cours_aff'];
						}else{
							$grp_cours_aff_spring='?';
							$hrs_cours_aff_spring=0;
						}
						
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE  A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="TD" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="TD" AND M.`periode`=2)
						';
						$res1 = $bdd->query($sql);
						if($row1 = $res1->fetch_assoc())
						{
							$grp_td_aff_fall=$row1['grp_td_aff'];
							$hrs_td_aff_fall=$row1['hrs_td_aff'];
						}else{
							$grp_td_aff_fall='?';
							$hrs_td_aff_fall=0;
						}
						if($row1 = $res1->fetch_assoc())
						{
							$grp_td_aff_spring=$row1['grp_td_aff'];
							$hrs_td_aff_spring=$row1['hrs_td_aff'];
						}else{
							$grp_td_aff_spring='?';
							$hrs_td_aff_spring=0;
						}
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE  A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="TP" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE  A.`annee_UniversitaireID`='.get_cur_year("id",$bdd).' AND A.`nature`="TP" AND M.`periode`=2)
						';

						$res1 = $bdd->query($sql);
						if($row1 = $res1->fetch_assoc())
						{
							$grp_tp_aff_fall=$row1['grp_tp_aff'];
							$hrs_tp_aff_fall=$row1['hrs_tp_aff'];
						}else{
							$grp_tp_aff_fall='?';
							$hrs_tp_aff_fall=0;
						}
						if($row1 = $res1->fetch_assoc())
						{
							$grp_tp_aff_spring=$row1['grp_tp_aff'];
							$hrs_tp_aff_spring=$row1['hrs_tp_aff'];
						}else{
							$grp_tp_aff_spring='?';
							$hrs_tp_aff_spring=0;
						}
						
					//	$heures_effecte=($hrs_cours_aff_fall+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
						$heures_effecte=($hrs_cours_aff_fall+$hrs_td_aff_fall+$hrs_tp_aff_fall)+($hrs_cours_aff_spring+$hrs_td_aff_spring+$hrs_tp_aff_spring);

                    // ('.round(($heures_effecte*(1/H_TD)),2).' HTD)
						echo '<span>Nbr d\'heure affectées: <strong>'.$heures_effecte.' HRS</strong> </span>';
						
						
						if($NB_PROF_ACTIF_CM==0 || $NB_PROF_ACTIF_TD==0 || $NB_PROF_ACTIF_TP==0 )
						{
							$moy_cours_prof_actif=0;
							$moy_td_prof_actif=0;
							$moy_tp_prof_actif=0;
						
						}
						else{
							$moy_cours_prof_actif=round((($hrs_cours_total_fall+$hrs_cours_total_spring)/$NB_PROF_ACTIF_CM),2);
						//	$moy_td_prof_actif=round(((($hrs_td_total_fall+$hrs_td_total_spring)*H_TD)/$NB_PROF_ACTIF),2);
							$moy_td_prof_actif=round(((($hrs_td_total_fall+$hrs_td_total_spring))/$NB_PROF_ACTIF_TD),2);
						//	$moy_tp_prof_actif=round(((($hrs_tp_total_fall+$hrs_tp_total_spring)*H_TP)/$NB_PROF_ACTIF),2);
							$moy_tp_prof_actif=round(((($hrs_tp_total_fall+$hrs_tp_total_spring))/$NB_PROF_ACTIF_TP),2);

						}
						if($NB_PROF_CM==0 || $NB_PROF_TD==0 || $NB_PROF_TP==0)
						{
							$moy_cours_prof=0;
							$moy_td_prof=0;
							$moy_tp_prof=0;
						}
						else{
							$moy_cours_prof=round((($hrs_cours_total_fall+$hrs_cours_total_spring)/$NB_PROF_CM),2);
						//	$moy_td_prof=round(((($hrs_td_total_fall+$hrs_td_total_spring)*H_TD)/$NB_PROF),2);
							$moy_td_prof=round(((($hrs_td_total_fall+$hrs_td_total_spring))/$NB_PROF_TD),2);
						//	$moy_tp_prof=round(((($hrs_tp_total_fall+$hrs_tp_total_spring)*H_TP)/$NB_PROF),2);
							$moy_tp_prof=round(((($hrs_tp_total_fall+$hrs_tp_total_spring))/$NB_PROF_TP),2);
						}
						
	
				//		echo $NB_PROF_CM.'   '.$NB_PROF_TD.'   '.$NB_PROF_TP.' actif  '.$NB_PROF_ACTIF_CM.'   '.$NB_PROF_ACTIF_TD.'   '.$NB_PROF_ACTIF_TP.'   ';
						
				echo'	</div>
					<br/>
					<h4> Details</h4>
					<div >
						<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>Somme</th>
							<th>Moyenne</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>Hrs Cours: '.($hrs_cours_aff_fall+$hrs_cours_aff_spring).'/'.($hrs_cours_total_fall+$hrs_cours_total_spring).' HRS</td>
							<td> '.$moy_cours_prof.'HRS / Enseignant  ('.$moy_cours_prof_actif.' / Ens. (Tous))</td>
						</tr>
						<tr>
							<td>Hrs TD: '.(($hrs_td_aff_fall+$hrs_td_aff_spring)).'/'.(($hrs_td_total_fall+$hrs_td_total_spring)).' HRS</td>
							<td> '.$moy_td_prof.'HRS / Enseignant  ('.$moy_td_prof_actif.' / Ens. (Tous))</td>
						</tr>
						<tr>
							<td>Hrs TP: '.(($hrs_tp_aff_fall+$hrs_tp_aff_spring)).'/'.(($hrs_tp_total_fall+$hrs_tp_total_spring)).' HRS</td>
							<td> '.$moy_tp_prof.'HRS / Enseignant  ('.$moy_tp_prof_actif.' / Ens. (Tous))</td>
						</tr>
						
						</tbody>
						</table>
					</div>';
					
					?>
					</div>
				</div>
				
				
			</div>	
				
				
				

		
		
<?php

    include './include/scripts.php';
    include './include/footer.php';
} else {
    header('location:login.php');
}
?>