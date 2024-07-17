<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
sec_session_start();
if(login_check($bdd) == true) {

    $AUvalid=!isAUvalid($bdd);
    $admin=isAdmin($bdd);
    $college=isCollege($bdd);

    $int = array('options' => array('min_range' => 0));

    if(isset($_GET['type']) && $_GET['type']=='annee')
    {
        $sql = 'SELECT * FROM `annee_universitaire` ORDER BY `annee_UniversitaireID` DESC';

        $res = $bdd->query($sql);
        $ret = array();
        while ($row = $res->fetch_assoc())
            $ret[$row['annee_UniversitaireID']]=$row['annee_univ'];
        print json_encode($ret);
    }

elseif($admin || $college)
{

    $cur_year=get_cur_year("id",$bdd);


	if(isset($_GET['type']))
	{

		$i=1;
		if($_GET['type']=='dept')
		{
			$sql = 'SELECT DP.*,(select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$cur_year.') AS actif FROM departement AS DP HAVING actif=1';
			$res = $bdd->query($sql);
			$ret = array();
			while ($row = $res->fetch_assoc()) 
			$ret[$row['departementID']]=$row['designation'];
				//$ret[""] = "";
		}
		if($_GET['type']=='dept_null')
		{
			$sql = 'SELECT DP.*,(select `actif` from `departement_actif` where `departement`=DP.`departementID` AND `annee`='.$cur_year.') AS actif FROM departement AS DP HAVING actif=1';
			$res = $bdd->query($sql);
			$ret = array();
			while ($row = $res->fetch_assoc()) 
			$ret[$row['departementID']]=$row['designation'];
			$ret[""] = "";
		}
		if($_GET['type']=='status')
		{
		$i=0;
			$ret = array("actif"=>"actif","inactif"=>"inactif");
			/*while ($row = $res->fetch_assoc()) 
			$ret[$row['departementID']]=$row['designation'];
			$ret[""] = "";*/
		}
		//x editabel new affectattion
        if($admin)
		if($_GET['type']=='free_grp' && isset($_GET['ED']) && isset($_GET['t']))
		{
			$ret = array();
			if($_GET['t']=="cours" || $_GET['t']=="TD" || $_GET['t']=="TP")
				{
                    $_GET['ED']=$bdd->real_escape_string(strip_tags($_GET['ED']));

					$sql='SELECT   element_module_details.grp_td, element_module_details.grp_cours,  element_module_details.grp_tp
					FROM element_module_details
					WHERE element_Module_DetailsID ='.$_GET['ED'];
					$res = $bdd->query($sql);
					//echo $sql;
					if($res == TRUE && $res->num_rows ==1)
					{
					
						$ED = $res->fetch_assoc();
					
						if($_GET['t']=="cours") $grp=$ED['grp_cours'];
						if($_GET['t']=="TD") $grp=$ED['grp_td'];
						if($_GET['t']=="TP") $grp=$ED['grp_tp'];
					
						$sql="SELECT  IFNULL(SUM(affectation.groups),0) AS grp_aff FROM affectation WHERE affectation.element_Module_DetailsID = ".$_GET['ED']." AND affectation.nature = '".$_GET['t']."'";
						$res = $bdd->query($sql);
						$row = $res->fetch_assoc();
					
						$grp_aff=$row['grp_aff'];
						
						
						for($i=1;$i<=($grp-$grp_aff);$i++)
						$ret[$i]=$i;
					}
					
				}
		}
		// x editabel modifier grp affectation
        if($admin)
		if($_GET['type']=='free_grp_aff' && isset($_GET['ED']) && isset($_GET['t']))
		{
			$ret = array();
			if($_GET['t']=="cours" || $_GET['t']=="TD" || $_GET['t']=="TP")
				{
                    $_GET['ED']=$bdd->real_escape_string(strip_tags($_GET['ED']));

					$sql='SELECT   element_module_details.grp_td, element_module_details.grp_cours,  element_module_details.grp_tp
					FROM element_module_details
					WHERE element_Module_DetailsID ='.$_GET['ED'];
					$res = $bdd->query($sql);
					//echo $sql;
					if($res == TRUE && $res->num_rows >0) 
					{
					
						$ED = $res->fetch_assoc();
					
						if($_GET['t']=="cours") $grp=$ED['grp_cours'];
						if($_GET['t']=="TD") $grp=$ED['grp_td'];
						if($_GET['t']=="TP") $grp=$ED['grp_tp'];
					
						
						for($i=0;$i<=$grp;$i++)
						$ret[$i]=$i;
					}
					
				}
		}
		

		if($_GET['type']=='grade')
		{
			$sql ='SELECT G.*, (select `actif` from `grade_actif` where `grade`=G.`gradeID` AND `annee`='.$cur_year.') AS actif FROM `grade` AS G HAVING actif=1';
			$res = $bdd->query($sql);
			$ret = array();
			while ($row = $res->fetch_assoc()) 
			$ret[$row['gradeID']]=$row['code'];
		}
        if($_GET['type']=='enseignants')
        {
            $sql ='SELECT `enseignantID`,CONCAT(`nom`,`prenom`) AS NOM, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E  WHERE `vacataire`=0 HAVING actif=1 ORDER BY E.nom';
            $res = $bdd->query($sql);
            $ret = array();
            while ($row = $res->fetch_assoc())
                $ret[$row['enseignantID']]=$row['NOM'];
        }
		if($_GET['type']=='enseignant_edit')
		{
		
			if(isset($_GET['v']) && $_GET['v']=='1') $v=1;
		//	if(isset($_GET['v']) && $_GET['v']=='0') $v=0;
		//	if(isset($_GET['stat']) && $_GET['stat']=='1') $s=0;
			
					if(isset($_GET['t']) && ($_GET['t']=="cours" || $_GET['t']=="TD" || $_GET['t']=="TP"))
			//		if($v==1)
					{
				//		if(isset($s))
                        if(isset($_GET['q']))
						{
							if(isset($v))
						    $sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E LEFT JOIN grade ON grade.gradeID=E.grade WHERE grade.'.$_GET['t'].' =1 AND (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") HAVING actif=1 ORDER BY `vacataire`,`nom` ';//   AND (`vacataire`=0 OR annee='.$cur_year.') AND status="actif"       AND `vacataire`='.$v.' ORDER BY `vacataire`';
						    else
						    $sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E LEFT JOIN grade ON gradeID=grade WHERE grade.'.$_GET['t'].' =1 AND `vacataire`=0 AND (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") HAVING actif=1 ORDER BY nom';
						}
                        else{
                            if(isset($v))
                                $sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E LEFT JOIN grade ON grade.gradeID=E.grade WHERE grade.'.$_GET['t'].' =1  HAVING actif=1 ORDER BY `vacataire`,`nom` ';//   AND (`vacataire`=0 OR annee='.$cur_year.') AND status="actif"       AND `vacataire`='.$v.' ORDER BY `vacataire`';
                            else
                                $sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E LEFT JOIN grade ON gradeID=grade WHERE grade.'.$_GET['t'].' =1 AND `vacataire`=0 HAVING actif=1 ORDER BY nom';

                        }
				/*		else
						{
						if(isset($v))
						$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` AS E  LEFT JOIN grade AS G ON G.gradeID=E.grade WHERE G.'.$_GET['t'].' =1  ORDER BY `vacataire`';
						else
						$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` AS E  LEFT JOIN grade AS G ON G.gradeID=E.grade WHERE G.'.$_GET['t'].' =1 AND `vacataire`=0 ORDER BY `vacataire`';
						}
			*/		//echo $sql;
					}else{
				//		if(isset($s))
						{
							if(isset($v))
							$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E HAVING actif=1 ORDER BY `vacataire`';//  `vacataire`='.$v;
							else
							$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E WHERE vacataire=0 HAVING actif=1 ORDER BY nom';//  `vacataire`='.$v;
						}
			/*			else
						{
						$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` ';//  `vacataire`='.$v;
						}
				*/	}
					
			
			$res = $bdd->query($sql);
			$ret = array();
            if($res)
            {
                while($row = $res->fetch_assoc())
                    $ret[] = array('id'=>$row['enseignantID'],'text'=>$row['nom'].' '.$row['prenom']);

            } else {
                // 0 results send a message back to say so.
                $ret[] = array('id'=>'0','text'=>'Aucun résultat trouvé..','charge'=>'0');
            }
	//		while ($row = $res->fetch_assoc())
	//		$ret[$row['enseignantID']]=$row['nom'].' '.$row['prenom'];
		}
		if($_GET['type']=='enseignant')
		{
			$ret = array();
			//if(isset($_GET['w']))
			//{ 
				
			//	if(isset($_GET['v']) && $_GET['v']=='0') 
				$v=0;
				if(isset($_GET['v']) && $_GET['v']=='1') $v=1;
				if(isset($_GET['status']) && $_GET['status']=='1') $s=0;
				
				if(isset($_GET['q']))
				{
					if($v==1){
					if(isset($s))
					$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E WHERE (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`='.$v.' HAVING actif=1'.' ORDER BY nom';
			//		$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` WHERE annee='.$cur_year.' AND status="actif" AND (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`='.$v;
					else
					$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` WHERE  annee='.$cur_year.' AND (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`='.$v.' ORDER BY nom';
					
					}else{
					if(isset($s))
					$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E WHERE (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`=0 HAVING actif=1'.' ORDER BY nom';
			//		$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` WHERE status="actif" AND (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`='.$v;
					else
					$sql='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` WHERE  (`nom` LIKE "%'.$_GET['q'].'%" OR `prenom` LIKE "%'.$_GET['q'].'%") AND `vacataire`='.$v.' ORDER BY nom';
					
					}
					
				}
				else
				{
					if(isset($s))
					$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E HAVING actif=1"'.' ORDER BY nom';//  `vacataire`='.$v;
					else
					$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant` '.' ORDER BY nom';//  `vacataire`='.$v;
					if(isset($v))
					{
						if(isset($s))
						$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade`, (select `actif` from `enseignant_actif` where `enseignant`=E.`enseignantID` AND `annee`='.$cur_year.') AS actif FROM `enseignant` AS E  WHERE `vacataire`='.$v.' HAVING actif=1'.' ORDER BY nom';
						else
						$sql ='SELECT `enseignantID`,`nom`,`prenom`,`grade` FROM `enseignant`  WHERE `vacataire`='.$v.' ORDER BY nom';
					
					}
				}
				$res = $bdd->query($sql);
				if($res) 
				{
					while($row = $res->fetch_assoc())
						$ret[] = array('id'=>$row['enseignantID'],'text'=>$row['nom'].' '.$row['prenom']);
				
				} else {
					// 0 results send a message back to say so.
					$ret[] = array('id'=>'0','text'=>'Aucun résultat trouvé..','charge'=>'0');
						}
		/*	}
			else
			{
				$sql ='SELECT CONCAT(enseignant.nom, " ",enseignant.prenom) as prof FROM `enseignant`';
				$res = $bdd->query($sql);
				
				while ($row = $res->fetch_assoc()) 
					$ret[$row['prof']]=$row['prof'];
			} */
		}
		if($_GET['type']=='filiere')
		{
			$ret = array();
			if(isset($_GET['s']))
			{
				if(isset($_GET['q']))
                    $sql='SELECT `filiereID`,`designation`, (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`='.$cur_year.') AS actif FROM  `filiere`  WHERE `designation` LIKE "%'.$_GET['q'].'%" HAVING actif=1 ';

           //     $sql='SELECT `filiereID`,`designation` FROM  `filiere` WHERE `designation` LIKE "%'.$_GET['q'].'%"';
				else
                    $sql='SELECT `filiereID`,`designation`, (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`='.$cur_year.') AS actif FROM  `filiere` HAVING actif=1 ';

              //  $sql='SELECT `filiereID`,`designation` FROM  `filiere` ';
				
				$res = $bdd->query($sql);
				if($res) 
				{
					while($row = $res->fetch_assoc())
						$ret[] = array('id'=>$row['filiereID'],'text'=>$row['designation']);
				
				} else {
					// 0 results send a message back to say so.
					$ret[] = array('id'=>'0','text'=>'No Results Found..');
						}
						
			}
			else
			{
		//	$sql ='SELECT `filiereID`,`designation` FROM `filiere`';
                $sql='SELECT `filiereID`,`designation`, (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`='.$cur_year.') AS actif FROM  `filiere` HAVING actif=1 ';

                $res = $bdd->query($sql);
			
			while ($row = $res->fetch_assoc()) 
			$ret[$row['filiereID']]=$row['designation'];
			}
		}
		if($_GET['type']=='cycle')
		{
			$sql ='SELECT cycle.*, (select `actif` from `cycle_actif` where `cycle_actif`.`cycle`=`cycle`.`cycleID` AND `annee`='.$cur_year.') AS actif FROM `cycle` HAVING actif=1';
			$res = $bdd->query($sql);
			$ret = array();
			while ($row = $res->fetch_assoc()) 
			$ret[$row['cycleID']]=$row['designation'];
		}
		if($_GET['type']=='module')
		{
			$ret = array();
			if(isset($_GET['s']))
			{
				if(isset($_GET['q']))
				{
					if(isset($_GET['c']) && $_GET['c']=="naff") 
						$sql='SELECT * FROM `module_details` AS D LEFT JOIN `module` AS M ON D.`moduleID`=M.`moduleID` WHERE `periode`='.$_GET['s'].' AND `annee_UniversitaireID`='.$cur_year.' AND `designation` LIKE "%'.$_GET['q'].'%"';
					else
						$sql='SELECT `moduleID`,`designation` FROM `module` WHERE `moduleID` NOT IN (SELECT `moduleID` FROM `module_details` WHERE `periode`='.$_GET['s'].' AND `annee_UniversitaireID`='.$cur_year.') AND `designation` LIKE "%'.$_GET['q'].'%"';
				}
				else
				{
					if(isset($_GET['c']) && $_GET['c']=="naff") 
						$sql='SELECT * FROM `module_details` AS D LEFT JOIN `module` AS M ON D.`moduleID`=M.`moduleID` WHERE `periode`='.$_GET['s'].' AND `annee_UniversitaireID`='.$cur_year;
					else
						$sql='SELECT `moduleID`,`designation` FROM `module` WHERE `moduleID` NOT IN (SELECT `moduleID` FROM `module_details` WHERE `periode`='.$_GET['s'].' AND `annee_UniversitaireID`='.$cur_year;
				}
				$res = $bdd->query($sql);
				if($res) 
				{
					if(isset($_GET['c']) && $_GET['c']=="naff") 
						while($row = $res->fetch_assoc())
						{
							//echo $row['module_DetailsID'];
							if(!is_affected($row['module_DetailsID'],$bdd))
							$ret[] = array('id'=>$row['module_DetailsID'],'text'=>$row['designation']);
						}
					else
						while($row = $res->fetch_assoc())
							$ret[] = array('id'=>$row['moduleID'],'text'=>$row['designation']);
				
				} else {
					// 0 results send a message back to say so.
					$ret[] = array('id'=>'0','text'=>'No Results Found..');
						}
			}
			else
			{
				$sql ='SELECT `moduleID`,`designation` FROM `module`';
				$res = $bdd->query($sql);

				while ($row = $res->fetch_assoc()) 
				$ret[$row['designation']]=$row['designation'];
			}
		}
//	if($i==1) $res->close();
	print json_encode($ret);
}
else
{

	// Load html

	if(isset($_GET['get']))
	{
        // retourne le nom complet d'un enseignant
        if($_GET['get']=="prof_name" && !empty($_GET['id']))
        {
            $_GET['id']=safe_input($_GET['id'],$bdd);
            $sql="SELECT `nom`,`prenom` FROM `enseignant` WHERE `enseignantID`=".$_GET['id'];
            $res = $bdd->query($sql);
            if($res==true && $res->num_rows ==1 && $row = $res->fetch_assoc()) echo $row['nom'].' '.$row['prenom'];
        }
		//Module.php elements   ////////////////////////////////////////////////////////////////////////////
		if($_GET['get']=="Mod_Elem")
		{
			if(!empty($_GET['id']))
			{
                if (!filter_var($_GET['id'], FILTER_VALIDATE_INT, $int))
                {
                    echo '<div class="alert alert-error">ERREUR: ID du module invalide!</div>';
                    die();
                }
				$sql='SELECT `designation` FROM `module` WHERE `moduleID`="'.$_GET['id'].'"';
				$res = $bdd->query($sql);
				if($res == TRUE)
				{
                    if ($res->num_rows==0)
                    {
                        echo '<div class="alert alert-error">ERREUR: Module inexistant!</div>';
                        die();
                    }
                    if($row = $res->fetch_assoc())
					echo '<div class="well well-small">Module: <strong>'.$row['designation'].'</strong></div>';
					$sql="SELECT `element_module`.*, `departement`.`designation` AS dept,(select `actif` from `element_module_actif` where element_module_actif.`element_module`=element_module.`element_moduleID` AND `annee`=$cur_year) AS actif  FROM `element_module` LEFT JOIN `departement` ON `departement`.`departementID`=`element_module`.`departementID`  WHERE `element_module`.annee <=".$cur_year." AND `moduleID`=".$_GET['id'];

                    $res = $bdd->query($sql);
					if($res) 
					{
							?>
							<h4>Liste des elements:</h4>
							<table id="cur_elements_table" cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
									<!--	<th>Code</th> -->
										<th>Designation</th>
										<th>Heures cours</th>
										<th>Heures TD</th>
										<th>Heures TP</th>
										<th>Departement</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
							<?php	
						$i=1;
						while($row = $res->fetch_assoc())
						{
							$id=$row['element_ModuleID'];
                            $actif_EM=($row['actif']==1)?"actif":"inactif";

							echo '<tr>
							        <td>'.$id.'</td>
							 <!--       <td><span class="elem_code editable" data-pk="'.$id.'" data-name="elem_code">'.$row['code'].'</span></td> -->
							        <td><span class="elem_designation editable" data-pk="'.$id.'" data-name="elem_designation">'.$row['designation'].'</span></td>
							        <td><span class="elem_hrs_cours editable" data-pk="'.$id.'" data-name="elem_hrs_cours">'.$row['heures_cours'].'</span></td>
							        <td><span class="elem_hrs_td editable" data-pk="'.$id.'" data-name="elem_hrs_td">'.$row['heures_td'].'</span></td>
							        <td><span class="elem_hrs_tp editable" data-pk="'.$id.'" data-name="elem_hrs_tp">'.$row['heures_tp'].'</span></td>
							        <td><span class="elem_dep editable" data-pk="'.$id.'" data-name="elem_dept">'.$row['dept'].'</span></td>
							        <td class="center "><span class="elem_status editable label label-'.label($actif_EM).'"  data-pk="'.$id.'" data-mod="'.$_GET['id'].'" data-name="elem_status" data-value="'.$actif_EM.'">'.$actif_EM.'</span></td>
							     </tr>';
							$i++;
						}
						echo 	'</tbody>';
						echo '</table>';
						echo '<input id="cur_elem_nbr" name="cur_elem_nbr" type="hidden" value="'.($i-1).'" />';
                        if($AUvalid):
						echo '<button id="save-btn" data-mod='.$_GET['id'].' class="btn btn-primary pull-right" style="display:none;">Enregistrer!</button>';
						echo '<button id="new_elem" class="btn btn-inverse pull-right"><i class=" icon-plus-sign icon-white"></i></button>';
						else:
                            echo '
                                <script>
                                    $(document).ready(function() {
                                    $(".editable").not(".annee").editable("toggleDisabled");
                                    });
                                </script>
                            ';
                        endif;
                            echo '<div id="msg" class="alert hide alert-error" ></div>';
					}else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
				}else echo '<div class="alert alert-error">ERREUR Lors du chargement du module..</div>';				
				
			}else echo '<div class="alert alert-error">ERREUR</div>';
		}////////////////////////////////////////////////////////////////////////////
		// Preparation -> module-details  (planification.php)  ////////////////////////////////////////////////////////////////////////////
		if($_GET['get']=="ModElem")
		{
			if(isset($_GET['id']) && isset($_GET['mod']) && isset($_GET['periode']))
			{
				$options = array(
					'options' => array(
                      'min_range' => -1  ) );
					  $options1 = array(
				'options' => array(
                      'min_range' => 1,
					  'max_range' => 2,
                      )
				);
				if(filter_var($_GET['id'], FILTER_VALIDATE_INT, $options) == FALSE || filter_var($_GET['mod'], FILTER_VALIDATE_INT, $options) == FALSE || filter_var($_GET['periode'], FILTER_VALIDATE_INT, $options1) == FALSE) {echo '<div class="alert alert-error">ERREUR dans les données interne..</div>';	}
			//	$sql='SELECT * FROM `module_details` LEFT JOIN `module` ON `module_details`.`moduleID` =`module`.`moduleID` LEFT JOIN `annee_universitaire` ON `module_details`.`annee_UniversitaireID`=`annee_universitaire`.`annee_UniversitaireID` WHERE `module_DetailsID`="'.$_GET['id'].'"';
				$sql='SELECT M.`designation`, F.`designation` AS filiere FROM `module` AS M LEFT JOIN `filiere` AS F ON F.`filiereID`=M.`filiereID` WHERE M.`moduleID`='.$_GET['mod'];
				$res = $bdd->query($sql);
				if($res == TRUE && $res->num_rows >0) 
				{	if($row = $res->fetch_assoc())
					echo '<div class="well well-small">Module: <strong><a href="module.php?mod='.$_GET['mod'].'">'.$row['designation'].'</a></strong>  ('.$row['filiere'].')<span class="pull-right">Année Universitaire: '.get_cur_year("des",$bdd).' ('.get_periode_name($_GET['periode']).')</span></div>';
				//	$sql="SELECT * FROM `element_module_details` LEFT JOIN `element_module` ON `element_module`.`element_ModuleID`=`element_module_details`.`element_ModuleID`  WHERE `module_DetailsID`=".$_GET['id'];
					
					$sql="SELECT element_module.*, (select `actif` from `element_module_actif` where element_module_actif.`element_module`=element_module.`element_moduleID` AND `annee`=$cur_year) AS actif FROM `element_module` WHERE `moduleID`=".$_GET['mod'];
					
					$res = $bdd->query($sql);
					if($res == TRUE && $res->num_rows >0) 
					{
							?>
							<h4>Liste des elements:</h4>
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
								<thead>
									<tr>
										<th>Designation</th>
										<th>Status</th>
										<th>Instancié?</th>
									</tr>
								</thead>
								<tbody>
							<?php	
						$i=1;
						while($row = $res->fetch_assoc())
						{
                            $actif_EM=($row['actif']==1)?"actif":"inactif";
							echo '<tr><td>'.$row['designation'].'</td><td class="center "><span class="label label-'.label($actif_EM).'" >'.$actif_EM.'</span></td>';
							if($_GET['id']!=-1)
							$sql ='SELECT * FROM `element_module_details` AS EM WHERE EM.`element_ModuleID`='.$row['element_ModuleID'].' AND EM.module_DetailsID='.$_GET['id'];
							else
							$sql ='SELECT * FROM `element_module_details` AS EM LEFT JOIN module_details AS MD ON MD.`module_DetailsID`=EM.`module_DetailsID` WHERE EM.`element_ModuleID`='.$row['element_ModuleID'].' AND `periode`='.$_GET['periode'].' AND `annee_UniversitaireID`='.$cur_year;



							$res1= $bdd->query($sql);
							if($res1 == TRUE && $res1->num_rows >0) 
							{
								if($actif_EM=="inactif") $stat_label='<span class="label label-important">OUI</span>';
								else $stat_label='<span class="label label-success">OUI</span>';
							}
							else
							{
								if($actif_EM=="inactif") $stat_label='<span class="label">NON</span>';
								else $stat_label='<span class="label label-important">NON</span>';
							}
							
							echo '<td>'.$stat_label.'</td></tr>';
							$i++;
						}
						echo 	'</tbody>';
						echo '</table>';
					}else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
				}else echo '<div class="alert alert-error">ERREUR Lors de chargement du module..</div>';				
				
			}else echo '<div class="alert alert-error">ERREUR Interne</div>';
		}////////////////////////////////////////////////////////////////////////////

		// affectation details  (affectation.php)  ////////////////////////////////////////////////////////////////////////////
		if($admin)
        if($_GET['get']=="Affect")
		{
			if(isset($_GET['id']))
			{
				//echo $_GET['id'];
				$sql='SELECT * FROM `module_details` LEFT JOIN `module` ON `module_details`.`moduleID` =`module`.`moduleID` LEFT JOIN `annee_universitaire` ON `module_details`.`annee_UniversitaireID`=`annee_universitaire`.`annee_UniversitaireID` WHERE `module_DetailsID`="'.$_GET['id'].'"';
				$res = $bdd->query($sql);
				if($res) 
				{	if($row = $res->fetch_assoc())
					echo '<div class="well well-small">Module: <strong>'.$row['designation'].'</strong> <span class="pull-right">Année Universitaire: '.$row['annee_univ'].' ('.$row['periode'].')</span></div>';
					$sql="SELECT *,(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$cur_year.") AS cours,
(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=$cur_year) AS TD,
(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=$cur_year) AS TP FROM `element_module_details` LEFT JOIN `element_module` ON `element_module`.`element_ModuleID`=`element_module_details`.`element_ModuleID`  WHERE `module_DetailsID`=".$_GET['id'];
					$res = $bdd->query($sql);
              //      echo $sql;
					if($res == TRUE) 
					{ 
					echo '<h4>Liste des elements:</h4>';
					while($row = $res->fetch_assoc())
						{
                            $H_cours = get_elem_mod_charge($row['element_ModuleID'],"cours",$bdd);
                            $H_TD = get_elem_mod_charge($row['element_ModuleID'],"TD",$bdd);
                            $H_TP = get_elem_mod_charge($row['element_ModuleID'],"TP",$bdd);

							echo '<table cellpadding="0" cellspacing="0" border="0" style="background-color: rgb(245, 245, 245);" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Designation</th>
										<th>'.get_label($row['cours'],$row['grp_cours'],"Cours").' ('.$H_cours.' hrs)</th>
										<th>'.get_label($row['TD'],$row['grp_td'],"TD").' ('.$H_TD.' hrs)</th>
										<th>'.get_label($row['TP'],$row['grp_tp'],"TP").' ('.$H_TP.' hrs)</th>
									</tr>
								</thead>
								<tbody>';
								
						
						
							echo '<tr><td style="width: 25%;" title="'.$row['designation'].'"><div style="text-overflow:ellipsis; width: 162.75px;white-space: nowrap;overflow: hidden;">'.$row['designation'].'</div></td>';
							//cours
							echo '<td  style="width: 25%;">';
							$sql='SELECT `affectationID` FROM `affectation` WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="cours"';
							
							$res1 = $bdd->query($sql);
							if($res1) 
							while($row1 = $res1->fetch_assoc())
							{
                                $a = new affectation($bdd);
                                $a->getFromId($row1['affectationID']);


                                if($a->partage==0)
                                {
                                    echo '<div >';
                                    $ens = new enseignant($bdd);
                                    $ens->getFromId($a->enseignantID);
                                    echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>
                                            <span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_cours'].')</span>
                                        ';
                                }else
                                {
                                    echo '<div style="border: 2px solid;">';
                                    $liste = $a->partage_ens_liste();
                                    for($i=0;$i<count($liste);$i++) {
                                        $ens = new enseignant($bdd);
                                        $ens->getFromId($liste[$i]);


                                        if($i==0)
                                        {
                                            echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                            echo '<span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_cours'].')</span>';

                                        }else
                                            echo '
                                            <span class="" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                    }
                                  //


                                }

                                echo '</div>';

							}
							echo '</td>';
							// TD
						
							echo '<td style="width: 25%;">';
							$sql='SELECT affectationID FROM `affectation` WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="TD"';
							
							$res1 = $bdd->query($sql);
							if($res1) 
							while($row1 = $res1->fetch_assoc())
							{
                                // style="display:inline-block; text-overflow:ellipsis; max-width: 120px;white-space: nowrap;overflow: hidden;"

                                $a = new affectation($bdd);
                                $a->getFromId($row1['affectationID']);


                                if($a->partage==0)
                                {
                                    echo '<div >';
                                    $ens = new enseignant($bdd);
                                    $ens->getFromId($a->enseignantID);
                                    echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>
                                            <span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_td'].')</span>
                                        ';

                                }else
                                {
                                    echo '<div style="border: 2px solid;">';
                                    $liste = $a->partage_ens_liste();
                                    for($i=0;$i<count($liste);$i++) {
                                        $ens = new enseignant($bdd);
                                        $ens->getFromId($liste[$i]);


                                        if($i==0)
                                        {
                                            echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                            echo '<span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_td'].')</span>';

                                        }else
                                            echo '
                                            <span class="" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                    }
                                    //


                                }

                                echo '</div>';



                            }
							echo '</td>';
							
						;
						echo '<td style="width: 25%;">';
							$sql='SELECT affectationID FROM `affectation`  WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="TP"';
							
							$res1 = $bdd->query($sql);
							if($res1) 
							while($row1 = $res1->fetch_assoc())
							{
								//echo '<div><span class="small-span" title="'.$row1['nom'].' '.$row1['prenom'].'" >'.$row1['nom'].' '.$row1['prenom'].'</span><span style="vertical-align:top;margin-left:5px;">('.$row1['groups'].'/'.$row['grp_tp'].')</span></div>';
                                $a = new affectation($bdd);
                                $a->getFromId($row1['affectationID']);


                                if($a->partage==0)
                                {
                                    echo '<div >';
                                    $ens = new enseignant($bdd);
                                    $ens->getFromId($a->enseignantID);
                                    echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>
                                            <span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_tp'].')</span>
                                        ';

                                }else
                                {
                                    echo '<div style="border: 2px solid;">';
                                    $liste = $a->partage_ens_liste();
                                    for($i=0;$i<count($liste);$i++) {
                                        $ens = new enseignant($bdd);
                                        $ens->getFromId($liste[$i]);


                                        if($i==0)
                                        {
                                            echo '
                                            <span class="small-span" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                            echo '<span style="vertical-align:top;margin-left:5px;">('.$a->groups.'/'.$row['grp_tp'].')</span>';

                                        }else
                                            echo '
                                            <span class="" title="'.$ens->getFullName().'">'.$ens->getFullName().'</span>

                                        ';
                                    }
                                    //


                                }

                                echo '</div>';
                            }
							echo '</td>';
							//$i++;
						}
						echo '</tr>';
						echo 	'</tbody>';
						echo '</table>';
					}else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
				}else echo '<div class="alert alert-error">ERREUR Lors du chargement de module..</div>';				
				
			}else echo '<div class="alert alert-error">ERREUR</div>';
			
		}////////////////////////////////////////////////////////////////////////////
		//statistique d'un enseignant ////////////////////////////////////////////////////////////////////////////
        if($admin)
        if($_GET['get']=="prof_stat")
		{
			if(isset($_GET['id']) && !empty($_GET['id']))
			{
						prof_stats($_GET['id'],$bdd,1);

			}else echo '<div class="alert alert-error">ERREUR</div>';
		}// end prof_stat  ////////////////////////////////////////////////////////////////////////////
		// affectation wizard card 2  ////////////////////////////////////////////////////////////////////////////
        if($admin)
        {

            if($_GET['get']=="Affect2")
            {
                if(!$AUvalid)
                {
                    echo '<div class="alert alert-warning"> Cette année est validé! impossible de modifier..</div>';
                    die();
                }
                if(isset($_GET['id']) && isset($_GET['prof']))
                {
                    if(!is_int((int)$_GET['id']) && !is_int((int)$_GET['prof'])){
                        echo '<div class="alert alert-error">Problème dans les donnèes</div>';
                        die();
                    }
                    $sql='SELECT `enseignantID`,`nom`,`prenom`,(SELECT  `grade` FROM `enseignant_actif` WHERE `enseignant`='.$_GET['prof'].' AND `annee`='.$cur_year.') AS `grade` FROM `enseignant` AS E WHERE `enseignantID`='.$_GET['prof'];
                    $res = $bdd->query($sql);
                    if($res)
                    {
                        if($row = $res->fetch_assoc())
                        {
                            $prof=$row['nom'].' '.$row['prenom'];
                            $gradeId=$row['grade'];
                            $chargeHrs =getValue('chargeHrs','grade_actif','grade='.$row['grade'].' AND `annee`='.$cur_year,$bdd); // (SELECT `chargeHrs` from `grade_actif` AS GA WHERE GA.`grade`=E.grade AND `annee`='.$cur_year.')As `chargeHrs`
                            $gradeCode=getValue('code','grade','gradeID='.$row['grade'],$bdd);
                            //	$vaca=$row['vacataire'];
                            //	echo $grade.'  '.$vaca;
                            echo '<br/><div style="margin-bottom: 5px;" class="alert alert-info">Professeur: <strong>'.$prof.'</strong>  Grade: <strong>'.$gradeCode.'</strong> Charge: <strong>'.$chargeHrs.'Hrs</strong>';

                            echo '<span id="progress_bar"></span></div>';
                        }
                        else echo '<div class="alert alert-error">ERREUR Lors du chargement des infos de l\'enseignant..</div>';
                    }

                    $sql='SELECT * FROM `module_details` LEFT JOIN `module` ON `module_details`.`moduleID` =`module`.`moduleID` LEFT JOIN `annee_universitaire` ON `module_details`.`annee_UniversitaireID`=`annee_universitaire`.`annee_UniversitaireID` WHERE `module_DetailsID`="'.$_GET['id'].'"';
                    $res = $bdd->query($sql);
                    if($res)
                    {	if($row = $res->fetch_assoc())
                        echo '<div class="well well-small">Module: <strong>'.$row['designation'].'</strong> <span class="pull-right">Année Universitaire: '.$row['annee_univ'].' ('.$row['periode'].')</span></div>';

                        echo '<h4>Liste des elements:</h4>';
                        echo '<div class="accordion" id="element_affectation">';
                        $sql="SELECT *,(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=$cur_year) AS cours,
					(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=$cur_year) AS TD,
					(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=$cur_year) AS TP FROM `element_module_details` LEFT JOIN `element_module` ON `element_module`.`element_ModuleID`=`element_module_details`.`element_ModuleID`  WHERE `module_DetailsID`=".$_GET['id'];
                        $res = $bdd->query($sql);


                        if($res)
                        {
                            $i=1;
                            while($row = $res->fetch_assoc())
                            {
                                echo '
							<div class="accordion-group">';
                                //	<input class="inline check"  data-i="'.$i.'" type="checkbox" id="Checkbox'.$i.'" value="'.$row['element_Module_DetailsID'].'">
                                echo'		<div class="accordion-heading" style="display:inline-block;">
									<a class="accordion-toggle" id="elem-link'.$i.'" data-toggle="collapse" data-parent="#element_affectation" href="#elem'.$i.'">'.$row['designation'].'</a>
								</div>
							<div style="height: 0px;" id="elem'.$i.'" class="accordion-body collapse">
							<div class="accordion-inner">';
                                echo '<input type="hidden" name="elem'.$i.'" value="'.$row['element_Module_DetailsID'].'">';
                                echo '<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
								<thead>
									<tr>
										<th style="width:136px" id="th_cours_'.$i.'" data-aff="'.$row['cours'].'" data-grp="'.$row['grp_cours'].'" data-hrs="'.$row['heures_cours'].'">'.get_label($row['cours'],$row['grp_cours'],"Cours").' </th>
										<th style="width:136px" id="th_TD_'.$i.'" data-aff="'.$row['TD'].'" data-grp="'.$row['grp_td'].'" data-hrs="'.$row['heures_td'].'">'.get_label($row['TD'],$row['grp_td'],"TD").' </th>
										<th style="width:136px" id="th_TP_'.$i.'" data-aff="'.$row['TP'].'" data-grp="'.$row['grp_tp'].'" data-hrs="'.$row['heures_tp'].'">'.get_label($row['TP'],$row['grp_tp'],"TP").' </th>
									</tr>
								</thead>
								<tbody>';

                                //cours
                                echo '<td>';
                                $sql='SELECT * FROM `affectation` LEFT JOIN `enseignant` ON `affectation`.`enseignantID`=`enseignant`.`enseignantID` WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="cours"';

                                $res1 = $bdd->query($sql);
                                if($res1)
                                    while($row1 = $res1->fetch_assoc())
                                    {
                                        echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 65px;white-space: nowrap;overflow: hidden;" title="'.$row1['nom'].' '.$row1['prenom'].'">'.$row1['nom'].' '.$row1['prenom'].'</span><span style="vertical-align:top;margin-left:5px;"> ('.$row1['groups'].'/1)</span><br/>'; //<a class="aff-btn" href="#edit-aff" onclick="load_edit(this);" data-toggle="modal" data-nature="1" data-elem-id="'.$row['element_Module_DetailsID'].'" data-prof="'.$row1['enseignantID'].'"><i class="icon-edit"></i></a>
                                    }

                                if(($row['grp_cours']-$row['cours'])!=0){
                                    /*			if($vaca==1){
                                                echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><select  title="non permis pour un vacataire"  style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;" DISABLED>';
                                                echo '<input  id="elem'.$i.'_cours" name="elem'.$i.'_cours" type="hidden" value="0">';
                                                }
                                                else */
                                    {
                                        if( !is_permis($gradeId,"cours",$bdd)){echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><select  title="non permis pour un '.$gradeCode.'"  style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;" DISABLED>';
                                            echo '<input  id="elem'.$i.'_cours" name="elem'.$i.'_cours" type="hidden" value="0">';
                                        }
                                        else
                                        {
                                            echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><select  id="elem'.$i.'_cours" name="elem'.$i.'_cours" style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;">';
                                            for($j=($row['grp_cours']-$row['cours']);$j>=0;$j--)
                                                echo '  <option value="'.$j.'" >'.$j.'</option>';


                                        }
                                    }
                                    echo '</select>';

                                }else echo '<input  id="elem'.$i.'_cours" name="elem'.$i.'_cours" type="hidden" value="0">';

                                echo '</td>';

                                // TD
                                echo '<td>';
                                $sql='SELECT * FROM `affectation` LEFT JOIN `enseignant` ON `affectation`.`enseignantID`=`enseignant`.`enseignantID` WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="TD"';

                                $res1 = $bdd->query($sql);
                                if($res1)
                                    while($row1 = $res1->fetch_assoc())
                                    {
                                        echo '<div><span style="display:inline-block; text-overflow:ellipsis; max-width: 65px;white-space: nowrap;overflow: hidden;" title="'.$row1['nom'].' '.$row1['prenom'].'">'.$row1['nom'].' '.$row1['prenom'].'</span><span style="vertical-align:top;margin-left:5px;"> ('.$row1['groups'].'/'.$row['TD'].')</span></div>'; //<a  class="aff-btn" href="#edit-aff" data-id="1" data-toggle="modal"><i  class="icon-edit"></i></a>
                                    }

                                if(($row['grp_td']-$row['TD'])!=0){
                                    if( !is_permis($gradeId,"TD",$bdd)){echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><input id="elem'.$i.'_TD" name="elem'.$i.'_TD" type="hidden" value="0"><select title="non permis pour un '.$gradeCode.'" style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;"  DISABLED>'; //style="width: 15px;height: 15px;margin-top: 8px;margin-left:5px;"
                                    }else
                                    {
                                        echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><select id="elem'.$i.'_TD" name="elem'.$i.'_TD" style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;"  >'; //style="width: 15px;height: 15px;margin-top: 8px;margin-left:5px;"

                                        for($j=($row['grp_td']-$row['TD']);$j>=0;$j--)
                                            echo '  <option value="'.$j.'" >'.$j.'</option>';
                                    }
                                    echo '</select>';

                                }else echo '<input id="elem'.$i.'_TD" name="elem'.$i.'_TD" type="hidden" value="0">';

                                echo '</td>';

                                //	TP
                                echo '<td>';
                                $sql='SELECT * FROM `affectation` LEFT JOIN `enseignant` ON `affectation`.`enseignantID`=`enseignant`.`enseignantID` WHERE `affectation`.`element_Module_DetailsID`='.$row['element_Module_DetailsID'].' AND `affectation`.`nature`="TP"';

                                $res1 = $bdd->query($sql);
                                if($res1)
                                    while($row1 = $res1->fetch_assoc())
                                    {
                                        echo '<div><span style="display:inline-block; text-overflow:ellipsis; max-width: 65px;white-space: nowrap;overflow: hidden;" title="'.$row1['nom'].' '.$row1['prenom'].'">'.$row1['nom'].' '.$row1['prenom'].'</span><span style="vertical-align:top;margin-left:5px;"> ('.$row1['groups'].'/'.$row['TP'].')</span></div>';
                                    }

                                if(($row['grp_tp']-$row['TP'])!=0){
                                    if( !is_permis($gradeId,"TP",$bdd)){ echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><input id="elem'.$i.'_TP" name="elem'.$i.'_TP" type="hidden" value="0"><select title="non permis pour un '.$gradeCode.'"  style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;" DISABLED>';

                                    }
                                    else
                                    {
                                        echo '<span style="display:inline-block; text-overflow:ellipsis; max-width: 70px;white-space: nowrap;overflow: hidden;" title="'.$prof.'">'.$prof.'</span><select id="elem'.$i.'_TP" name="elem'.$i.'_TP"  style="width: 45px;height: 30px;margin-top: -5px;margin-left:5px;">';

                                        for($j=($row['grp_tp']-$row['TP']);$j>=0;$j--)
                                            echo '  <option value="'.$j.'" >'.$j.'</option>';
                                    }
                                    echo '</select>';
                                }else echo '<input id="elem'.$i.'_TP" name="elem'.$i.'_TP" type="hidden" value="0">';
                                echo '</td>';
                                echo 	'</tbody>';
                                echo '</table>';
                                echo'	</div>
					  </div>
					</div>';
                                $i++;
                            }
                            echo '</div>';
                            $i--;
                            echo '<input type="hidden" name="i" id="i" value="'.$i.'"/>	';
                        }else echo '<div class="alert alert-error"> ERREUR Lors du chargement des elements..</div>';
                    }else echo '<div class="alert alert-error">ERREUR Lors du chargement de module..</div>';

                }else echo '<div class="alert alert-error">ERREUR</div>';

            }

        }//else echo '<div class="alert alert-warning">L\'affectation est réservé au chef du département!</div>';

		// Detail des affectation par enseignants   ////////////////////////////////////////////////////////////////
        if($admin)
        if($_GET['get']=="Affect_stat")
		{
			if(isset($_GET['prof']))
			{
			//get the professor's name
			if(!empty($_GET['prof']))
        {
            $_GET['prof']=safe_input($_GET['prof'],$bdd);
            $sql="SELECT `nom`,`prenom` FROM `enseignant` WHERE `enseignantID`=".$_GET['prof'];
            $res = $bdd->query($sql);
            if($res==true && $res->num_rows ==1 && $row = $res->fetch_assoc()) echo '<h4>'.$row['nom'].' '.$row['prenom'].'<h4/>';
            else echo '<h1>Prof inexistant<h1/>';
           
        }else
        echo '<h1>Requete vide<h1/>';
        
			
			echo '
				<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a href="#Automne" data-toggle="tab">Automne</a></li>
					<li><a href="#Printemps" data-toggle="tab">Printemps</a></li>
				</ul>';
				echo '<div class="tab-content">';
				
				echo '<div class="tab-pane active" id="Automne">';
		//		$cur_year=get_cur_year("id",$bdd);
				$sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`=1 AND `annee_UniversitaireID`='.$cur_year;

				$res = $bdd->query($sql);
				if($res == TRUE && $res->num_rows >0) 
				while($row = $res->fetch_assoc())
				{
					// savoir les elements de module instancié..
					$sql="SELECT DISTINCT `element_Module_DetailsID`, element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID  WHERE `annee_UniversitaireID`=".$cur_year." AND (affectation.`enseignantID`=".$_GET['prof']." OR affectation_partage.`enseignantID`=".$_GET['prof']." )) ";
				
					$res1 = $bdd->query($sql);
					if($res1 == TRUE && $res1->num_rows >0) 
					{
						echo '<div style="margin-bottom: 0px;" class="well well-small">Module:'.$row['mod_des'].'</div>';
						echo '<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
						while($row1 = $res1->fetch_assoc())
						{
                           // $H_cours = get_elem_mod_charge($row1['element_ModuleID'],"cours",$bdd);
                           // $H_TD = get_elem_mod_charge($row1['element_ModuleID'],"TD",$bdd);
                           // $H_TP = get_elem_mod_charge($row1['element_ModuleID'],"TP",$bdd);
                            $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
							// cours
							$i=1;
							$sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$_GET['prof'];
						
							$res2 = $bdd->query($sql);
							if($res2 == TRUE && $res2->num_rows >0) 
							{
								if($row2 = $res2->fetch_assoc())
								{
									if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
									echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
								}
							}

                            //cours partagé
                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$_GET['prof'];

                           // echo $sql;
                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0)
                            {

                                if($row2 = $res2->fetch_assoc())
                                {
                                    if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                    echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                }
                            }

							//TD
							$sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$_GET['prof'];

							$res2 = $bdd->query($sql);
							if($res2 == TRUE && $res2->num_rows >0) 
							{
								if($row2 = $res2->fetch_assoc())
								{
									if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
									echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
								}
							}
                            //TD partagé
                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$_GET['prof'];

                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0)
                            {
                                if($row2 = $res2->fetch_assoc())
                                {
                                    if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                    echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                }
                            }
							//TP
							$sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$_GET['prof'];


							$res2 = $bdd->query($sql);
							if($res2 == TRUE && $res2->num_rows >0) 
							{
								if($row2 = $res2->fetch_assoc())
								{
									if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
									echo get_label($row2['groups'],$row2['grp_tp'],"TP");
								}
							}
                            //TP partagé
                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$_GET['prof'];
                     //   echo $sql;
                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0)
                            {
                                if($row2 = $res2->fetch_assoc())
                                {
                                    if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                    echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                }
                            }
							$i=0;
							echo '</td></tr>';
						}
						echo '</table>';
					}
					
				}
				echo '</div>';
				
				echo '<div class="tab-pane" id="Printemps">';
		//		$cur_year=get_cur_year("id",$bdd);
				$sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`= 2 AND `annee_UniversitaireID`='.$cur_year;

                $res = $bdd->query($sql);
                if($res == TRUE && $res->num_rows >0)
                    while($row = $res->fetch_assoc())
                    {
                        // savoir les elements de module instancié..
                        $sql="SELECT DISTINCT `element_Module_DetailsID`, element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID  WHERE `annee_UniversitaireID`=".$cur_year." AND (affectation.`enseignantID`=".$_GET['prof']." OR affectation_partage.`enseignantID`=".$_GET['prof']." )) ";
                 
                        $res1 = $bdd->query($sql);
                        if($res1 == TRUE && $res1->num_rows >0)
                        {
                            echo '<div style="margin-bottom: 0px;" class="well well-small">Module:'.$row['mod_des'].'</div>';
                            echo '<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                            while($row1 = $res1->fetch_assoc())
                            {
                                $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
                                // cours
                                $i=1;
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                    }
                                }

                                //cours partagé
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$_GET['prof'];

                                // echo $sql;
                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {

                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                    }
                                }

                                //TD
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                    }
                                }
                                //TD partagé
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                    }
                                }
                                //TP
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                    }
                                }
                                //TP partagé
                                $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$cur_year." AND affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$_GET['prof'];
                                //   echo $sql;
                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation']."   ".get_badge_text("($H)").'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                    }
                                }
                                $i=0;
                                echo '</td></tr>';
                            }
                            echo '</table>';
                        }

                    }
                echo '</div>';

			}
			
		}////////////////////////////////////////////////////////////////////////////
		
		// Detail des affectation par element de module + type ////////////////////////////////////////////////////////////////

        if($_GET['get']=="Edit_Affect")
		{
            if(!$admin)
            {
                echo '<div class="alert alert-warning">L\'affectation est réservé au chef du département!</div>';
                die();
            }
			if(isset($_GET['idE']) && isset($_GET['t']))
			{
				if($_GET['t']=="cours" || $_GET['t']=="TD" || $_GET['t']=="TP")
				{
				
				$sql='SELECT  element_module.designation, element_module.moduleID, ED.grp_td, ED.grp_cours, ED.grp_tp FROM element_module_details AS ED
					INNER JOIN element_module
					ON ED.element_ModuleID = element_module.element_ModuleID
					WHERE ED.element_Module_DetailsID ='.$_GET['idE'];
				$res = $bdd->query($sql);
				//echo $sql;
				if($res == TRUE && $res->num_rows >0) 
				{
					
					$ED = $res->fetch_assoc();
					
					if($_GET['t']=="cours") $grp=$ED['grp_cours'];
					if($_GET['t']=="TD") $grp=$ED['grp_td'];
					if($_GET['t']=="TP") $grp=$ED['grp_tp'];
					
					$sql="SELECT  IFNULL(SUM(affectation.groups),0) AS grp_aff FROM affectation WHERE affectation.element_Module_DetailsID = ".$_GET['idE']." AND affectation.nature = '".$_GET['t']."'";
					$res = $bdd->query($sql);
					$row = $res->fetch_assoc();

					$grp_aff=$row['grp_aff'];
					echo '<div style="margin-bottom: 0px;" class="well well-small">Element de Module: <strong><a href="module.php?mod='.$ED['moduleID'].'">'.$ED['designation'].'</a></strong>      <span>'.get_label($grp_aff,$grp,$_GET['t']).'</span></div>';
					echo '<table id="aff_table" cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
					echo '<thead>
							<tr>
								<th>Enseignant</th>
								<th>Nbr de groupe</th>';
					//			<th>Action</th>
						echo'	</tr>
						  </thead>
						  <tbody>';
					$sq1l="SELECT
					enseignant.enseignantID,
					enseignant.nom,  enseignant.prenom,  affectation.groups, affectation.affectationID, affectation.partage FROM affectation
					INNER JOIN enseignant     ON affectation.enseignantID = enseignant.enseignantID
					WHERE affectation.element_Module_DetailsID = ".$_GET['idE']." AND affectation.nature = '".$_GET['t']."'";
                    $sql="SELECT  affectation.affectationID FROM affectation
					WHERE affectation.element_Module_DetailsID = ".$_GET['idE']." AND affectation.nature = '".$_GET['t']."'";

                    $res = $bdd->query($sql);
					while($row = $res->fetch_assoc())
					{	
						echo '<tr>';
                        if($AUvalid)$temp="";
                        else $temp="0";

                        $a = new affectation($bdd);
                        $a->getFromId($row['affectationID']);

						echo '
                        <td>';
                        if($a->partage==1)
                        {

                            $liste = $a->partage_ens_liste();
                            for($i=0;$i<count($liste);$i++)
                            {
                                $ens = new enseignant($bdd);
                                $ens->getFromId($liste[$i]);

                                if($i==0)
                                echo '
                            <span title="'.$ens->getFullName().'" style="display:inline-block; text-overflow:ellipsis; max-width: 200px;white-space: nowrap;overflow: hidden;">'.$ens->getFullName().'</span>';
                                else
                                    echo '
                            <span title="'.$ens->getFullName().'" style="display:inline-block; text-overflow:ellipsis; max-width: 200px;white-space: nowrap;overflow: hidden;">'.$ens->getFullName().' <a class="supprimer_partage" data-ens_id="'.$ens->enseignantID.'" data-aff_id="'.$a->affectationID.'" href="#" title="supprimer"><i class="icon-minus-sign" ></i></a></span>

                            ';
                                if($i<count($liste))
                                    echo '<br>';

                            }

                        }
                        else
                        {
                            $ens = new enseignant($bdd);
                            $ens->getFromId($a->enseignantID);

                            echo '
                            <span title="'.$ens->getFullName().'" style="display:inline-block; text-overflow:ellipsis; max-width: 200px;white-space: nowrap;overflow: hidden;">'.$ens->getFullName().'</span>
                            ';
                        }


						echo '

                            <button id="save-btn1" data-ED="'.$row['affectationID'].'" class="btn btn-primary pull-right" style="display:none;">Enregistrer!</button>
                            <button  class="ajouter_partage btn btn-primary pull-right" data-affect_id="'.$row['affectationID'].'"><i class=" icon-plus-sign icon-white"></i></button>
						</td>';
						echo '
                        <td>
                            <span class="aff_grp'.$temp.' editable" data-pk="'.$row['affectationID'].'" data-value="'.$a->groups.'" data-name="new_grp">'.$a->groups.'</span>
                        </td>';  //.get_badge($row['groups'],$grp).'</td>';

						echo '</tr>';
					}
					echo ' </tbody>';
					echo '</table>';

					echo '<input id="free_grp" name="free_grp" type="hidden" value="'.($grp-$grp_aff).'" />';
                    if($AUvalid):
                    echo '<button id="save-btn" data-t="'.$_GET['t'].'" data-ED="'.$_GET['idE'].'" class="btn btn-primary pull-right" style="display:none;">Enregistrer!</button>';
					echo '<button id="new_elem" class="btn btn-inverse pull-right"><i class=" icon-plus-sign icon-white"></i></button>';
                    else:
                    echo '
                                <script>
                                    $(document).ready(function() {
                                    $(".editable").not(".annee").editable("toggleDisabled");
                                    });
                                </script>
                            ';
                    endif;
                    echo '<div id="msg" class="alert hide alert-error" ></div>';
				}
				
				}else echo '<div class="alert alert-error">ERREUR Lors du chargement..</div>';	
			}
		}

        // Detail de la fiche de souhait par enseignant     ////////////////////////////////////////////////////////////////
        if($_GET['get']=="Fiche_details")
        {
            if(!empty($_GET['prof']) && !empty($_GET['fiche']))
            {
                echo '
				<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a href="#Automne" data-toggle="tab">Automne</a></li>
					<li><a href="#Printemps" data-toggle="tab">Printemps</a></li>
				</ul>';
                echo '<div class="tab-content">';

                echo '<div class="tab-pane active" id="Automne">';
         //     $cur_year=get_cur_year("id",$bdd);
                $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`=1 AND `annee_UniversitaireID`='.$cur_year;

                $res = $bdd->query($sql);
                if($res == TRUE && $res->num_rows >0)
                    while($row = $res->fetch_assoc())
                    {
                        // savoir les elements de module instancié..
                        $sql="SELECT DISTINCT `element_Module_DetailsID` FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `fiche_souhait_details` WHERE `fiche`=".$_GET['fiche']." AND fiche_souhait_details.`groups`!=0 AND `enseignantID`=".$_GET['prof']." ) ";

                        $res1 = $bdd->query($sql);
                        if($res1 == TRUE && $res1->num_rows >0)
                        {
                            echo '<div style="margin-bottom: 0px;" class="well well-small">Module:'.$row['mod_des'].'</div>';
                            echo '<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                            while($row1 = $res1->fetch_assoc())
                            {
                                // cours
                                $i=1;
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"cours\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {

                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                    }
                                }

                                //TD
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"TD\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                    }
                                }
                                //TP
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"TP\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                    }
                                }
                                $i=0;
                                echo '</td></tr>';
                            }
                            echo '</table>';
                        }

                    }
                echo '</div>';

                echo '<div class="tab-pane" id="Printemps">';
     //           $cur_year=get_cur_year("id",$bdd);
                $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`= 2 AND `annee_UniversitaireID`='.$cur_year;

                $res = $bdd->query($sql);
                if($res == TRUE && $res->num_rows >0)
                    while($row = $res->fetch_assoc())
                    {
                        // savoir les elements de module instancié..
                        $sql="SELECT DISTINCT `element_Module_DetailsID` FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `fiche_souhait_details` WHERE `fiche`=".$_GET['fiche']." AND fiche_souhait_details.`groups`!=0 AND `enseignantID`=".$_GET['prof']." ) ";

                        $res1 = $bdd->query($sql);
                        if($res1 == TRUE && $res1->num_rows >0)
                        {
                            echo '<div style="margin-bottom: 0px;" class="well well-small">Module:'.$row['mod_des'].'</div>';
                            echo '<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                            while($row1 = $res1->fetch_assoc())
                            {
                                // cours
                                $i=1;
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"cours\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {

                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                    }
                                }

                                //TD
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"TD\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                    }
                                }
                                //TP
                                $sql="SELECT fiche_souhait_details.groups, fiche_souhait_details.nature, fiche_souhait_details.id, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((fiche_souhait_details INNER JOIN element_module_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE fiche_souhait_details.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND fiche_souhait_details.`fiche`=".$_GET['fiche']." AND fiche_souhait_details.nature=\"TP\" AND fiche_souhait_details.`groups`!=0 AND fiche_souhait_details.`enseignantID`=".$_GET['prof'];

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    if($row2 = $res2->fetch_assoc())
                                    {
                                        if ($i++==1) echo '<tr><td data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                        echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                    }
                                }
                                $i=0;
                                echo '</td></tr>';
                            }
                            echo '</table>';
                        }

                    }
                echo '</div>';



                echo '</div>';
            }else  echo'
            <div style="margin: 0px;margin-top: 10px;" class=" alert alert-error">
            <span>Erreur dans la requete GET</span>
            </div>
            ';

        }////////////////////////////////////////////////////////////////////////////

        //les template email
        if($admin || ($college && isset($_GET['id']) && $_GET['id']=5 ))
        if($_GET['get']=="Template")
        {
            if(!filter_var($_GET['id'], FILTER_VALIDATE_INT, $int)){
                echo'



            <div style="margin: 0px;margin-top: 10px;" class=" alert alert-error">
            <span>Erreur dans la requete GET</span>
            </div>
            ';
                die();
            }

             {
                $sql="SELECT * FROM `email_template` WHERE `id`=".$_GET['id'];
                $temp=$bdd->query($sql);
                if($temp==FALSE) die('<div style="margin: 0px;margin-top: 10px;" class=" alert alert-error"><span>Problème interne!</span></div>');
                if($temp->num_rows==0) die('<div style="margin: 0px;margin-top: 10px;" class=" alert alert-error"><span>Maquette inexistante!</span></div>');
            }
            $row=$temp->fetch_assoc();

            echo'
            <div>
            <input type="hidden" name="id" value="'.$_GET['id'].'"/>
            <label for="sujet">Sujet:</label>
                <input style="display:block;width: 95%;" type="text" name="sujet" id="sujet" value="'.$row['sujet'].'"/>
                <label> Message:
                    <textarea style="display:block;width: 95%;" name="corp" >'.$row['corp'].'</textarea>
                </label>
                <p class="editorloader" ><a href="javascript:loadeditor();">Charger l\'editeur</a></p>
            </div>
            ';


            echo '
            <div class="bs-example">

                <div class="well-small" title="Les variables a utiliser..">
                Variables:
                ';
            if(!empty($row['variables'])):
                $vars = explode(";", $row['variables']);
            foreach ($vars as $value) {
                echo '<code>['.$value.']</code>  ';
            }
                endif;
            echo '
                </div>
            </div>


            ';

        }

    }//fin if 'get' is set
}
} //if admin

}

?>