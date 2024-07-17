<?php
    define("_VALID_PHP", true);

include 'include/connexion_BD.php';
include 'include/fonctions.php';

sec_session_start();
if(login_check($bdd) == true) {
 
   $active=3;
    $script = array("editable","scripts","datatable","affectation_prof");
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
                        <li class="active">Affectattion_enseignant</li>
                        <li >
                            <span class="divider">/</span> <a href="affectation_enseignant_all.php">Tous</a>
                        </li>
					</ul>
			</div>
			
			<!-- Table des enseignants -->
					
			<div class="well">
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Details des Affectations</h3>
				<!--	<a style="margin-top:8px;"class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a>-->
				</div> <!-- Fin well-small-->
				
				<div class="well well-small" >
			

						<div class="well well-small">
                            <h4 style="display: initial;">Liste des enseignants:</h4>
                            <span class="pull-right">
                            <a class="toggle-vis" data-column="0"><span class="label label-info">#</span></a>
                            <a class="toggle-vis" data-column="1"><span class="label label-info">Enseignant</span></a>
                            <a class="toggle-vis" data-column="2"><span class="label label-info">Charge</span></a>
                            <a class="toggle-vis" data-column="3"><span class="label label-info">Automne</span></a>
                            <a class="toggle-vis" data-column="4"><span class="label label-info">Printemps</span></a>
                            <a class="toggle-vis" data-column="5"><span class="label label-info">Action</span></a>
                            </span>
						</div>
						<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
							<thead>
								<tr>
									<th data-hide="phone,tablet">#</th>
									<th>Enseignant</th>
									<th>Charge Annuelle</th>
									<th>Automne</th>
									<th>Printemps</th>
									<th>Action</th>
								</tr>
							</thead>
								<?php


                $CurYear=get_cur_year("id",$bdd);
				$sql='SELECT E.* FROM `enseignant` AS E WHERE E.vacataire=0 AND E.`enseignantID` IN (select `enseignant` from `enseignant_actif` where `actif`=1 AND `annee`='.$CurYear.') ';
				$res = $bdd->query($sql);

				if($res) 
				{	
					while ($row = $res->fetch_assoc()) 
					{
						//cours
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="cours" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="cours" AND M.`periode`=2)
						';
						//}else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
                       // echo $sql;die();
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

                        //TD
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TD" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TD" AND M.`periode`=2)
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
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TP" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TP" AND M.`periode`=2)
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


                        //Affectations partagées
                        $ens = new enseignant($bdd);
                        $ens->getFromId($row['enseignantID']);
                        $cours_partages = $ens->getAffectations($CurYear,true);

                        $grp_cours_aff_fall_partage = 0;
                        $grp_cours_aff_spring_partage = 0;
                        $hrs_cours_aff_fall_partage = 0;
                        $hrs_cours_aff_spring_partage = 0;
                        $grp_td_aff_fall_partage=0;
                        $hrs_td_aff_fall_partage=0;
                        $grp_td_aff_spring_partage=0;
                        $hrs_td_aff_spring_partage=0;
                        $grp_tp_aff_fall_partage=0;
                        $hrs_tp_aff_fall_partage=0;
                        $grp_tp_aff_spring_partage=0;
                        $hrs_tp_aff_spring_partage=0;
                        for($i=0;$i<count($cours_partages);$i++)
                        {

                            $a=new affectation($bdd);
                            $a->getFromId($cours_partages[$i]);
                            $d = $a->getDetails();
                            if($d['periode']==1)
                            {
                                if($a->nature=="cours")
                                {

                                    $grp_cours_aff_fall_partage+=$a->groups;
                                    $hrs_cours_aff_fall_partage += ($d['heures_cours']/count($a->partage_ens_liste()));

                                }
                                elseif($a->nature=="TD")
                                {
                                    $grp_td_aff_fall_partage+=$a->groups;
                                    $hrs_td_aff_fall_partage += ($d['heures_td']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TP")
                                {
                                    $grp_tp_aff_fall_partage +=$a->groups;
                                    $hrs_tp_aff_fall_partage += ($d['heures_tp']/count($a->partage_ens_liste()));
                                }

                            }else{
                                if($a->nature=="cours")
                                {
                                    $grp_cours_aff_spring_partage+=$a->groups;
                                    $hrs_cours_aff_spring_partage += ($d['heures_cours']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TD")
                                {
                                    $grp_td_aff_spring_partage+=$a->groups;
                                    $hrs_td_aff_spring_partage += ($d['heures_td']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TP")
                                {
                                    $grp_tp_aff_spring_partage +=$a->groups;
                                    $hrs_tp_aff_spring_partage += ($d['heures_tp']/count($a->partage_ens_liste()));
                                }
                            }
                        }


                        //	$heures_effecte=($hrs_cours_aff_fall+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
						$heures_effecte=($hrs_cours_aff_fall*H_CM+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring*H_CM+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
                        $heures_effecte_partage=($hrs_cours_aff_fall_partage*H_CM+$hrs_td_aff_fall_partage*H_TD+$hrs_tp_aff_fall_partage*H_TP)+($hrs_cours_aff_spring_partage*H_CM+$hrs_td_aff_spring_partage*H_TD+$hrs_tp_aff_spring_partage*H_TP);

                        $heures_effecte+=$heures_effecte_partage;
                        //	if($row['status']=='actif' || $heures_effecte!=0 )
                        {
					echo '<tr><td>'.$row['enseignantID'].'</td><td><span data-id="'.$row['enseignantID'].'">'.$row['nom'].' '.$row['prenom'].'</span></td>';

                            $ens->getGrade($CurYear);
					$hrs=get_grade_charge($ens->grade,$CurYear,$bdd);
					echo '<td class="alert alert-'.Alert_tag($heures_effecte,$hrs).'"><strong >'.$heures_effecte.'</strong>/'.(($hrs>=0)?$hrs:"?").'</td>';
	
					echo '<td>Cours: <strong>'.($grp_cours_aff_fall+$grp_cours_aff_fall_partage).'</strong>Grps('.($hrs_cours_aff_fall+$hrs_cours_aff_fall_partage).'Hrs)<br/>TD: <strong>'.($grp_td_aff_fall+$grp_td_aff_fall_partage).'</strong>Grps('.($hrs_td_aff_fall+$hrs_td_aff_fall_partage).'Hrs)<br/>TP: <strong>'.($grp_tp_aff_fall+$grp_tp_aff_fall_partage).'</strong>Grps('.($hrs_tp_aff_fall+$hrs_tp_aff_fall_partage).'Hrs)</td>';
					echo '<td>Cours: <strong>'.($grp_cours_aff_spring+$grp_cours_aff_spring_partage).'</strong>Grps('.($hrs_cours_aff_spring+$hrs_cours_aff_spring_partage).'Hrs)<br/> TD: <strong>'.($grp_td_aff_spring+$grp_td_aff_spring_partage).'</strong>Grps('.($hrs_td_aff_spring+$hrs_td_aff_spring_partage).'Hrs)<br/>TP: <strong>'.($grp_tp_aff_spring+$grp_tp_aff_spring_partage).'</strong>Grps('.($hrs_tp_aff_spring+$hrs_tp_aff_spring_partage).'Hrs)</td>';
							
					echo '<td class="center "><a class="stat-btn btn btn-info" href="#edit" data-id="'.$row['enseignantID'].'" data-toggle="modal" ><i class=" icon-eye-open icon-white"></i> Details </a></td></tr>';
					    }
					}
					$res->close();
				}else echo '<div class="alert alert-error">ERREUR Lors du chargement des stats des enseignants..</div>';
						?>
							<tbody>
							</tbody>
						</table>
					
					</div>
				</div> <!-- Fin well-small-->		
			
			
			
			<!-- vacataires-->
			
				
				<div class="well well-small" >
			
					    <h4>Liste des vacataires:</h4>
						
						<table id="liste2" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
							<thead>
								<tr>
									<th data-hide="phone,tablet">#</th>
									<th>Enseignant</th>
									<th>Charge Annuelle</th>
									<th>Automne</th>
									<th>Printemps</th>
									<th>Action</th>
								</tr>
							</thead>
								<?php
						
								
									
				$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `vacataire` AS E WHERE E.`enseignantID` IN (select `enseignant` from `enseignant_actif` where `actif`=1 AND `annee`='.$CurYear.') ';
				$res = $bdd->query($sql);
				if($res) 
				{	
					while ($row = $res->fetch_assoc()) 
					{
						echo '<tr><td>'.$row['enseignantID'].'</td><td><span data-id="'.$row['enseignantID'].'">'.$row['nom'].' '.$row['prenom'].'</span></td>';
						
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="cours" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="cours" AND M.`periode`=2)
						';
						//}else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
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
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TD" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TD" AND M.`periode`=2)
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
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TP" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="TP" AND M.`periode`=2)
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


                        //Affectations partagées
                        $ens = new enseignant($bdd);
                        $ens->getFromId($row['enseignantID']);
                        $cours_partages = $ens->getAffectations($CurYear,true);

                        $grp_cours_aff_fall_partage = 0;
                        $grp_cours_aff_spring_partage = 0;
                        $hrs_cours_aff_fall_partage = 0;
                        $hrs_cours_aff_spring_partage = 0;
                        $grp_td_aff_fall_partage=0;
                        $hrs_td_aff_fall_partage=0;
                        $grp_td_aff_spring_partage=0;
                        $hrs_td_aff_spring_partage=0;
                        $grp_tp_aff_fall_partage=0;
                        $hrs_tp_aff_fall_partage=0;
                        $grp_tp_aff_spring_partage=0;
                        $hrs_tp_aff_spring_partage=0;
                        for($i=0;$i<count($cours_partages);$i++)
                        {
                            $a=new affectation($bdd);
                            $a->getFromId($cours_partages[$i]);
                            $d = $a->getDetails();
                            if($d['periode']==1)
                            {
                                if($a->nature=="cours")
                                {

                                    $grp_cours_aff_fall_partage+=$a->groups;
                                    $hrs_cours_aff_fall_partage += ($d['heures_cours']/count($a->partage_ens_liste()));

                                }
                                elseif($a->nature=="TD")
                                {
                                    $grp_td_aff_fall_partage+=$a->groups;
                                    $hrs_td_aff_fall_partage += ($d['heures_td']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TP")
                                {
                                    $grp_tp_aff_fall_partage +=$a->groups;
                                    $hrs_tp_aff_fall_partage += ($d['heures_tp']/count($a->partage_ens_liste()));
                                }

                            }else{
                                if($a->nature=="cours")
                                {
                                    $grp_cours_aff_spring_partage+=$a->groups;
                                    $hrs_cours_aff_spring_partage += ($d['heures_cours']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TD")
                                {
                                    $grp_td_aff_spring_partage+=$a->groups;
                                    $hrs_td_aff_spring_partage += ($d['heures_td']/count($a->partage_ens_liste()));
                                }
                                elseif($a->nature=="TP")
                                {
                                    $grp_tp_aff_spring_partage +=$a->groups;
                                    $hrs_tp_aff_spring_partage += ($d['heures_tp']/count($a->partage_ens_liste()));
                                }
                            }
                        }


                        //	$heures_effecte=($hrs_cours_aff_fall+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
                        $heures_effecte=($hrs_cours_aff_fall*H_CM+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring*H_CM+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
                        $heures_effecte_partage=($hrs_cours_aff_fall_partage*H_CM+$hrs_td_aff_fall_partage*H_TD+$hrs_tp_aff_fall_partage*H_TP)+($hrs_cours_aff_spring_partage*H_CM+$hrs_td_aff_spring_partage*H_TD+$hrs_tp_aff_spring_partage*H_TP);

                        $heures_effecte+=$heures_effecte_partage;
                        //	if($row['status']=='actif' || $heures_effecte!=0 )
                        {

                            $hrs = get_grade_charge($row['grade'], $CurYear, $bdd);
                            echo '<td class="alert alert-' . Alert_tag($heures_effecte, $hrs) . '"><strong >' . $heures_effecte . '</strong>/' . (($hrs >= 0) ? $hrs : "?") . '</td>';

                            echo '<td>Cours: <strong>' . ($grp_cours_aff_fall + $grp_cours_aff_fall_partage) . '</strong>Grps(' . ($hrs_cours_aff_fall + $hrs_cours_aff_fall_partage) . 'Hrs)<br/>TD: <strong>' . ($grp_td_aff_fall + $grp_td_aff_fall_partage) . '</strong>Grps(' . ($hrs_td_aff_fall + $hrs_td_aff_fall_partage) . 'Hrs)<br/>TP: <strong>' . ($grp_tp_aff_fall + $grp_tp_aff_fall_partage) . '</strong>Grps(' . ($hrs_tp_aff_fall + $hrs_tp_aff_fall_partage) . 'Hrs)</td>';
                            echo '<td>Cours: <strong>' . ($grp_cours_aff_spring + $grp_cours_aff_spring_partage) . '</strong>Grps(' . ($hrs_cours_aff_spring + $hrs_cours_aff_spring_partage) . 'Hrs)<br/> TD: <strong>' . ($grp_td_aff_spring + $grp_td_aff_spring_partage) . '</strong>Grps(' . ($hrs_td_aff_spring + $hrs_td_aff_spring_partage) . 'Hrs)<br/>TP: <strong>' . ($grp_tp_aff_spring + $grp_tp_aff_spring_partage) . '</strong>Grps(' . ($hrs_tp_aff_spring + $hrs_tp_aff_spring_partage) . 'Hrs)</td>';
                        }
                        /*
                            $heures_effecte=($hrs_cours_aff_fall*H_CM+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring*H_CM+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);


                        $hrs=get_grade_charge($row['grade'],$CurYear,$bdd);
                        echo '<td class="alert alert-'.Alert_tag($heures_effecte,$hrs).'"><strong >'.$heures_effecte.'</strong>/'.(($hrs>=0)?$hrs:"?").'</td>';

					echo '<td>Cours: <strong>'.$grp_cours_aff_fall.'</strong>Grps('.$hrs_cours_aff_fall.'Hrs)<br/>TD: <strong>'.$grp_td_aff_fall.'</strong>Grps('.($hrs_td_aff_fall).'Hrs)<br/>TP: <strong>'.$grp_tp_aff_fall.'</strong>Grps('.($hrs_tp_aff_fall).'Hrs)</td>';
					echo '<td>Cours: <strong>'.$grp_cours_aff_spring.'</strong>Grps('.$hrs_cours_aff_spring.'Hrs)<br/> TD: <strong>'.$grp_td_aff_spring.'</strong>Grps('.($hrs_td_aff_spring).'Hrs)<br/>TP: <strong>'.$grp_tp_aff_spring.'</strong>Grps('.($hrs_tp_aff_spring).'Hrs)</td>';
						*/

						echo '<td class="center "><a class="stat-btn btn btn-info" href="#edit" data-id="'.$row['enseignantID'].'" data-toggle="modal" ><i class=" icon-eye-open icon-white"></i> Details </a></td></tr>';
					}
					$res->close();
				}else echo '<div class="alert alert-error">ERREUR Lors du chargement des stats des enseignants..</div>';
						?>
							<tbody>
							</tbody>
						</table>
					
				</div>
				
				
				
				

			
			
				<!-- Modal messages : Affectation (style="width: 750px;margin-left:-375px;margin-top: -239px;top: 50%;") -->
			
			    <div id="edit" class="medium-modal modal hide" tabindex="-1">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
                        <button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>
						<h3>Details des Affectation:</h3>
					</div>
			
					<div class="modal-body" style="min-height: 300px;">
						
							
						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
					</div>
				</div>
			
			<!-- END of Modal messages : Affectation -->
			
			
			



    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.' (ENSEIGNANTS)"';?>;

        var message2=document.getElementById('title').innerHTML;
        message2+=<?php echo '"  '.$CurYear_des.' (VACATAIRES)"';?>;

    </script>
    <?php include('include/scripts.php');?>
<?php

include('include/footer.php');
} else {
   header('location:login.php');
}
?>