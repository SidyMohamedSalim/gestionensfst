<?PHP


    if (!defined("_VALID_PHP"))
        die('L\'accès directe a cette page est interdit!');

//constantes

// Valid constant names
define("H_CM","1.5"); // 3/2
define("H_TD","1");
define("H_TP","0.75");// = 3/4

define("_ADMIN","chef departement");
define("_COLLEGE","college");
define("_PROF","enseignant");
define("_DOYEN","doyen");



//retourner l'année en cours de l'utilisateur..
function get_cur_year($type,$bdd) {
$sql="SELECT * FROM `configuration` LEFT JOIN `annee_universitaire` ON annee_courrante=`annee_universitaire`.`annee_UniversitaireID` WHERE `user`=".$_SESSION['user_id'];
$res = $bdd->query($sql);
if($row = $res->fetch_assoc()){ if($type=="id")$R=$row['annee_courrante']; else $R=$row['annee_univ'];}
else $R="????";
return $R;
}

// retourne l'id (prof) de l'utilisateur courrant
function get_prof_id($bdd)
{
    $sql="SELECT enseignantID FROM `user` WHERE `id`=".$_SESSION['user_id'];
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()) return $row['enseignantID'];
    else return "";
}
// retourne le departement de l'utilisateur (professeur) courrant
    function get_prof_dept($bdd)
    {
        $sql="SELECT `departementID` FROM `enseignant` WHERE `enseignantID`=".get_prof_id($bdd);
        $res = $bdd->query($sql);
        if($row = $res->fetch_assoc()) return $row['departementID'];
        else return NULL;
    }
// retourne le nbr des groupes d'un module instancié (TP ou TD) donnant l'id d'un element
    function get_groups_mod($id,$type,$bdd)
    {

        $sql="SELECT DISTINCT M.`grp_td`, M.`grp_tp`, M.`grp_cours` FROM `module_details` AS M, `element_module_details` AS E WHERE M.`module_DetailsID`=E.`module_DetailsID` AND E.`element_Module_DetailsID`=".$id;
    //    echo $sql;
        $res = $bdd->query($sql);
        if($row = $res->fetch_assoc())
        {
            if($type=="cours") return $row['grp_cours'];
            if($type=="TD") return $row['grp_td'];
            elseif($type=="TP") return $row['grp_tp'];
            else return -1;
        }

    }
function get_elem_mod_charge($id,$type,$bdd)
{

    $sql="SELECT `heures_cours`, `heures_td`, `heures_tp` FROM `element_module` WHERE `element_ModuleID` = ".$id;
    //    echo $sql;
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc())
    {
        if($type=="cours") return $row['heures_cours'];
        if($type=="TD") return $row['heures_td'];
        elseif($type=="TP") return $row['heures_tp'];
        else return -1;
    }

}
function get_elem_mod_charge_all($id,$bdd)
{

    $sql="SELECT `heures_cours`, `heures_td`, `heures_tp` FROM `element_module` WHERE `element_ModuleID` = ".$id;
    //    echo $sql;
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc())
    {
        return "Cours: ".$row['heures_cours']."h, TD: ".$row['heures_td']."h, TP: ".$row['heures_tp']."h";
    }
}
// affectaion label (used in affectation.php + load.php)
function get_label($x,$y,$z,$w="") {
                $y.=$w;
                            if($y==0) $span='<span class="label"> '.$z.': '.$x.'/'.$y.' </span>';
							elseif($x==0) $span='<span class="label label-warning"> '.$z.': '.$x.'/'.$y.' </span>';
							elseif($x<$y) $span='<span class="label label-info"> '.$z.': '.$x.'/'.$y.' </span>';
							elseif($x==$y) $span='<span class="label label-success"> '.$z.': '.$x.'/'.$y.' </span>';
							else $span='<span class="label label-important"> '.$z.': '.$x.'/'.$y.' </span>';
							return $span;
}
// affectation badge (affectation.php)
function get_badge($x,$y) {

                            if($y==0) $span='<span class="badge "> '.$x.'/'.$y.' </span>';
                            elseif($x==0) $span='<span class="badge  badge-warning"> '.$x.'/'.$y.' </span>';
							elseif($x<$y) $span='<span class="badge  badge-info"> '.$x.'/'.$y.' </span>';
                            elseif($x==$y) $span='<span class="badge  badge-success"> '.$x.'/'.$y.' </span>';
							else $span='<span class="badge  badge-important"> '.$x.'/'.$y.' </span>';
							return $span;
}
function get_badge_text($x,$type="info") {//warning success important

    $span='<span class="badge  badge-'.$type.'"> '.$x.' </span>';

    return $span;
}
// alert type
function Alert_tag($x,$y) {

if($x>=($y*0.95) && $x<=($y*1.05)) return "success";
else if($x>0 && $x<($y*0.95)) return "info";
else if($x==0 ) return "warning";
else return "error";

}
// label actif
function label($x){
	if($x=="actif" || $x=="OUI") return "success";
	else return "error";
}
// Savoir la designation du semestre
function get_periode_name($x){
if($x==1) return 'Automne';
if($x==2) return 'Printemps';
return '?';

}
//fonction qui retourne le type d'access d'un utilisateur
function get_user_access($id,$bdd)
{
    $sql="SELECT access FROM `user` WHERE `id`=".$id;
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()){
     /*   $access=$row['access'];
        if(defined(_ADMIN) && $access == _ADMIN) return 1;
        if(defined(_COLLEGE) && $access==_COLLEGE) return 2;
        if(defined(_PROF) && $access==_PROF) return 3;
        if(defined(_DOYEN) && $access==_DOYEN) return 4;
*/
        return $row['access'];
    }
    return 0;
}
//foction qui verifie si l'utilisateur courrant est un administrateur
function isAdmin($bdd)
{
    if(!isset($_SESSION['user_id'])) return 0;
    $sql="SELECT access FROM `user` WHERE `id`=".$_SESSION['user_id'];
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()) return ($row['access']==_ADMIN);
}
function isCollege($bdd)
{
    if(!isset($_SESSION['user_id'])) return 0;
    $sql="SELECT access FROM `user` WHERE `id`=".$_SESSION['user_id'];
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()) return ($row['access']==_COLLEGE);
}
function isDoyen($bdd)
{
    if(!isset($_SESSION['user_id'])) return 0;
    $sql="SELECT access FROM `user` WHERE `id`=".$_SESSION['user_id'];
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()) return ($row['access']==_DOYEN);
}
function isAUvalid($bdd)
{
    $sql="SELECT `valid` FROM `annee_universitaire` WHERE annee_UniversitaireID=".get_cur_year("id",$bdd);
    $res = $bdd->query($sql);
    if($row = $res->fetch_assoc()) return ($row['valid']);
    else return 0;
}

//mesure contre XSS
function safe_input($in,$bdd)
{
    return str_replace('"', '&quot;', $bdd->real_escape_string(strip_tags($in)));
}
// savoir si un module_details est affecté (afin de ne pas le supprimer)
function is_module_details_affected($modD,$bdd){
	$sql='SELECT COUNT(*) AS NB FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID`=A.`element_Module_DetailsID` WHERE ED.`module_DetailsID`='.$modD;
	$res = $bdd->query($sql);
	if($res== TRUE)
	{
		if($row=$res->fetch_assoc())
		{
			if($row['NB']>0) return true;
		}
	}
	
	return false;
}

    // savoir si le module_details est souhaité par un enseignant (afin de ne pas le supprimer)
    function is_module_details_wished($modD,$bdd){
        $sql='SELECT COUNT(*) AS NB FROM `fiche_souhait_details` AS FSD LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID`=FSD.`element_Module_DetailsID` WHERE ED.`module_DetailsID`='.$modD;
        $res = $bdd->query($sql);
        if($res== TRUE)
        {
            if($row=$res->fetch_assoc())
            {
                if($row['NB']>0) return true;
            }
        }

        return false;
    }
// savoir la permision a partir d'un grade
function is_permis($grade,$type,$bdd)
{
	if($type=="cours" || $type=="TD" || $type=="TP" )
	{
		$sql="select * from grade where gradeID=".$grade;
		$res= $bdd->query($sql);
		if($res==true && $res->num_rows >0)
		{
			$row=$res->fetch_assoc();
			if($row[$type]) return true;
		}
	}

return false;
}

// savoir si un module est instancié.. (par semestre)
function is_module_instancied($mod,$periode,$annee,$bdd){
	$sql='SELECT COUNT(*) AS NB FROM `module_details` WHERE `moduleID`='.$mod.' AND `annee_UniversitaireID`='.$annee.' AND `periode`='.$periode;
	$res = $bdd->query($sql);
	if($res== TRUE)
	{
		if($row=$res->fetch_assoc())
		{
			if($row['NB']>0) return true;
		}
	}
	
	return false;
}

function is_module_planified($id,$year,$bdd)
{
    $sql="SELECT 1 FROM `module_details` WHERE `moduleID`=".$id." AND `annee_UniversitaireID`=".$year.' LIMIT 1';
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 1;

    return 0;
}
    //
    function is_dept_used($id,$year,$bdd)
    {
        $sql="SELECT 1 FROM `enseignant` WHERE `enseignantID` in (SELECT `enseignant` FROM `enseignant_actif` WHERE `annee`=".$year." AND actif=1) AND `departementID`=".$id." LIMIT 1";
        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 1;

        $sql="SELECT 1, (select `actif` from `filiere_actif` where `filiere_actif`.`filiere`=`filiere`.`filiereID` AND `annee`=".$year.") AS actif FROM `filiere` WHERE `departementID`=".$id." HAVING actif=1 LIMIT 1";
        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 2;
   //     else return $sql;

        return 0;
    }
    //
    function is_filiere_used($id,$year,$bdd)
    {
        $sql="SELECT 1 FROM `module` WHERE `moduleID` in (SELECT `module` FROM `module_actif` WHERE `annee`=".$year." AND actif=1) AND `filiereID`=".$id." LIMIT 1";
        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 1;

        return 0;
    }
    //
    function is_cycle_used($id,$year,$bdd)
    {
        $sql="SELECT 1 FROM `filiere` WHERE `filiereID` in (SELECT `filiere` FROM `filiere_actif` WHERE `annee`=".$year." AND actif=1) AND `cycleID`=".$id." LIMIT 1";
        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 1;

        return 0;
    }
function is_module_actif($mod,$Y,$bdd)
{
    $sql="SELECT 1 FROM `module_actif` WHERE `module`=$mod AND annee=$Y AND actif=1";
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 1;
    return 0;
}

function is_prof_used($id,$year,$bdd)
{
    $sql="SELECT 1 FROM `affectation` WHERE `enseignantID`=".$id." AND `annee_UniversitaireID`=".$year." LIMIT 1";
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 1;

    $sql="SELECT 1 FROM `fiche_souhait_details` WHERE `fiche` in (SELECT `id` FROM `fiche_souhait` WHERE `annee_universitaire`=".$year." ) AND `enseignantID`=".$id." LIMIT 1";
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 2;

    $sql="SELECT 1 FROM `fiche_souhait_valid` WHERE `fiche` in (SELECT `id` FROM `fiche_souhait` WHERE `annee_universitaire`=".$year." ) AND `enseignantID`=".$id." LIMIT 1";
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 3;
/*
    $sql="SELECT 1 FROM `departement_chef` WHERE `annee`=".$year."  AND `enseignant`=".$id." LIMIT 1";
    $res=$bdd->query($sql);
    if($res== TRUE && $res->num_rows>0) return 4;
*/
    return 0;
}
    function is_prof_actif($id,$year,$bdd)
    {
        $sql="SELECT 1 FROM `enseignant_actif` WHERE `enseignant`=".$id." AND `annee_UniversitaireID`=".$year;
        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 1;
        return 0;
    }

    function is_grade_used($id,$year,$bdd)
    {
        $sql="SELECT 1 FROM `enseignant` WHERE `enseignantID` in (SELECT `enseignant` FROM `enseignant_actif` WHERE `annee`=".$year." AND actif=1) AND `grade`=".$id." LIMIT 1";

        $res=$bdd->query($sql);
        if($res== TRUE && $res->num_rows>0) return 1;

        return 0;
    }
    function get_grade_charge($id,$year,$bdd)
    {
        $sql="SELECT `chargeHrs` FROM `grade_actif` WHERE `grade`=".$id." AND `annee`=".$year;
        //echo $sql;
        //die();
        $res=$bdd->query($sql);

        if($res== TRUE && $res->num_rows>0){
            if($row=$res->fetch_assoc()) return $row['chargeHrs'];// (($row['chargeHrs']>0)?$row['chargeHrs']:-2);
        }
        return -1;
    }

// savoir si touts les elements d'un module sont affectés..
function is_affected($mod,$bdd){
	//$sql="SELECT `element_Module_DetailsID`,(`grp_td`+`grp_tp`+1) AS grp FROM `element_module_details` WHERE `module_DetailsID`=".$mod;
//	$sql="SELECT E.`element_Module_DetailsID`,(`grp_td`+`grp_tp`+1) AS grp_t, sum(`groups`) as grp_a FROM `element_module_details` as E LEFT JOIN  `affectation` AS A ON E.`element_Module_DetailsID`=A.`element_Module_DetailsID` WHERE `module_DetailsID`=".$mod;
	$sql="SELECT E.`element_Module_DetailsID`,(`grp_td`+`grp_tp`+`grp_cours`) AS grp_t FROM `element_module_details` as E WHERE E.`module_DetailsID`=".$mod;
	//$sql="SELECT COUNT(*) FROM `module_details` AS M WHERE `module_DetailsID` IN (SELECT `module_DetailsID` FROM `element_module_details` AS E WHERE `element_Module_DetailsID` IN (IFNULL(SUM(affectation.groups),0) FROM affectation as A WHERE A.`element_Module_DetailsID`=E.`element_Module_DetailsID` ))'";
	$res = $bdd->query($sql);
	
	if($res)
		while($row=$res->fetch_assoc())
		{
			$sql='SELECT IFNULL(sum(`groups`),0) as grp_a FROM affectation WHERE `element_Module_DetailsID`='.$row['element_Module_DetailsID'];
			$res2 = $bdd->query($sql);
			if($res2)
			{
				$row2=$res2->fetch_assoc();
				if($row['grp_t']!=$row2['grp_a']) return false;
			}
		}
		return true;
}

// statistique d'un professeur  $i=1 pour afficher le nom etc....
function prof_stats($id,$bdd,$i,$plain=false){
    $curYear=get_cur_year("id",$bdd);
				$sql='SELECT `enseignantID`,`nom`,`prenom`,(SELECT  `grade` FROM `enseignant_actif` WHERE `enseignant`='.$id.' AND `annee`='.$curYear.') AS `grade` FROM `enseignant` AS E WHERE `enseignantID`='.$id;
				$res = $bdd->query($sql);
				if($res) 
				{	
						
						if($row = $res->fetch_assoc())
						{
                            $chargeHrs =getValue('chargeHrs','grade_actif','grade='.$row['grade'].' AND `annee`='.$curYear,$bdd); // (SELECT `chargeHrs` from `grade_actif` AS GA WHERE GA.`grade`=E.grade AND `annee`='.$cur_year.')As `chargeHrs`
                            $gradeCode=getValue('code','grade','gradeID='.$row['grade'],$bdd);

                            if($i==1) echo '<br/><div class="alert alert-info"><span id="proffeseur">Professeur: <strong>'.$row['nom'].' '.$row['prenom'].'</strong></span>  Grade: <strong>'.$gradeCode.'</strong> Charge: <strong>'.$chargeHrs.'Hrs</strong> </div>';
						}
						else echo '<div class="alert alert-error">ERREUR Lors du chargement des infos de l\'enseignant..</div>';
						
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="cours" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="cours" AND M.`periode`=2)
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
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="TD" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_td_aff, IFNULL(sum(`heures_td`*`groups`),0) AS hrs_td_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="TD" AND M.`periode`=2)
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
						$sql='(SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="TP" AND M.`periode`=1)
						UNION ALL (SELECT IFNULL(sum(`groups`),0) AS grp_tp_aff, IFNULL(sum(`heures_tp`*`groups`),0) AS hrs_tp_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$id.' AND A.`annee_UniversitaireID`='.$curYear.' AND A.`nature`="TP" AND M.`periode`=2)
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
                    $cours_partages = $ens->getAffectations($curYear,true);

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
					//	$heures_effecte=($hrs_cours_aff_fall+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);
					//	$heures_effecte=($hrs_cours_aff_fall*H_CM+$hrs_td_aff_fall*H_TD+$hrs_tp_aff_fall*H_TP)+($hrs_cours_aff_spring*H_CM+$hrs_td_aff_spring*H_TD+$hrs_tp_aff_spring*H_TP);

					/*	if($heures_effecte<$row['chargeHrs']) $alert=" ";
						else if($heures_effecte == $row['chargeHrs']) $alert="alert-success";
						else $alert="alert-error";
					*/
                    if($plain==true)
                    {
                        $msg['charge'] = '<strong >'.$heures_effecte.'</strong>';
                        $msg['automne'] = 'cours: <strong>'.($grp_cours_aff_fall+$grp_cours_aff_fall_partage).'</strong>Grps('.($hrs_cours_aff_fall+$hrs_cours_aff_fall_partage).'Hrs), TD: <strong>'.($grp_td_aff_fall+$grp_td_aff_fall_partage).'</strong>Grps('.($hrs_td_aff_fall+$hrs_td_aff_fall_partage).'Hrs),TP: <strong>'.($grp_tp_aff_fall+$grp_tp_aff_fall_partage).'</strong>Grps('.($hrs_tp_aff_fall+$hrs_tp_aff_fall_partage).'Hrs)';
                        $msg['printemps'] = 'cours: <strong>'.($grp_cours_aff_spring+$grp_cours_aff_spring_partage).'</strong>Grps('.($hrs_cours_aff_spring+$hrs_cours_aff_spring_partage).'Hrs), TD: <strong>'.($grp_td_aff_spring+$grp_td_aff_spring_partage).'</strong>Grps('.($hrs_td_aff_spring+$hrs_td_aff_spring_partage).'Hrs),TP: <strong>'.($grp_tp_aff_spring+$grp_tp_aff_spring_partage).'</strong>Grps('.($hrs_tp_aff_spring+$hrs_tp_aff_spring_partage).'Hrs)';
                        return $msg;
                    }else
                    {
                        echo '<div  class="alert alert-'.Alert_tag($heures_effecte,$chargeHrs).'"><span id="charge" data-hrs-aff="'.$heures_effecte.'" data-charge="'.$chargeHrs.'" >Charge Annuelle: <strong >'.$heures_effecte.'</strong>/'.$chargeHrs.'</span><br/><small>';
                        //		</div><div style="width:100px;margin-bottom:0px;display:inline-block;" class="progress progress-striped"><span class="bar" style="width: 20%;"></span></div> //<div style="display:inline-block;">
                        echo 'Automne => cours: <strong>'.($grp_cours_aff_fall+$grp_cours_aff_fall_partage).'</strong>Grps('.($hrs_cours_aff_fall+$hrs_cours_aff_fall_partage).'Hrs), TD: <strong>'.($grp_td_aff_fall+$grp_td_aff_fall_partage).'</strong>Grps('.($hrs_td_aff_fall+$hrs_td_aff_fall_partage).'Hrs),TP: <strong>'.($grp_tp_aff_fall+$grp_tp_aff_fall_partage).'</strong>Grps('.($hrs_tp_aff_fall+$hrs_tp_aff_fall_partage).'Hrs)<br/>';
                        echo 'Printemps => cours: <strong>'.($grp_cours_aff_spring+$grp_cours_aff_spring_partage).'</strong>Grps('.($hrs_cours_aff_spring+$hrs_cours_aff_spring_partage).'Hrs), TD: <strong>'.($grp_td_aff_spring+$grp_td_aff_spring_partage).'</strong>Grps('.($hrs_td_aff_spring+$hrs_td_aff_spring_partage).'Hrs),TP: <strong>'.($grp_tp_aff_spring+$grp_tp_aff_spring_partage).'</strong>Grps('.($hrs_tp_aff_spring+$hrs_tp_aff_spring_partage).'Hrs)';
                        echo '</small></div>';
                    }

				}else echo '<div class="alert alert-error">ERREUR Lors du chargement des stats de l\'enseignant..</div>';			
}
// envoi d'un email
function envoi_mail($message = array("adresse"=>"","nom"=>"", "sujet" => "vide", "corp" => "vide!", "template"=> ""),$bdd)
{

	extract($message);
	if(filter_var($adresse, FILTER_VALIDATE_EMAIL))
	{
		require_once("include/class.phpmailer.php");

		$mail = new PHPMailer();

		$mail->CharSet = 'UTF-8';
		$mail->IsSMTP();  // telling the class to use SMTP
		$mail->SMTPAuth   = true; // SMTP authentication
	//$mail->Host       = "smtp.mail.yahoo.com"; // SMTP server smtp.mail.yahoo.com  or smtp.gmail.com
		$mail->Host       = getValue("valeur","configuration_globale","param='smtp_host'",$bdd); // SMTP server smtp.mail.yahoo.com  or smtp.gmail.com
		$mail->SMTPSecure = 'ssl';
	//$mail->Port       = 465; // SMTP Port
		$mail->Port       = (int) getValue("valeur","configuration_globale","param='smtp_port'",$bdd); // SMTP Port
	
    $smtp_user=getValue("valeur","configuration_globale","param='smtp_user'",$bdd);
		$mail->Username   = $smtp_user; // SMTP account username
	
		$mail->Password   = getValue("valeur","configuration_globale","param='smtp_pass'",$bdd);        // SMTP account password
    $mail->IsHTML(1);

    //   print_r($mail);
        $site_name=getValue("valeur","configuration_globale","param='site_name'",$bdd);
        $mail->SetFrom($smtp_user, $site_name); // FROM
        $mail->AddReplyTo($smtp_user, $site_name); // Reply TO


		$mail->Subject    = $sujet; // email subject
	  $mail->Body       = $corp;

		//todo: remove this
		// To me temporarily )

    //$mail->Body = "Email origine: ".$adresse."</br></br>".$corp; $adresse= "ismail.nait@gmail.com";
    
		$mail->AddAddress($adresse,$nom);
		
     //   echo  $mail->Body."\n";
		if( $mail->Send()) 	//echo 'Mailer error: ' . $mail->ErrorInfo;
		return TRUE;

		else
        {
            echo 'Mailer error: ' . $mail->ErrorInfo;
            return FALSE;
        }

		//	echo 'Mailer error: ' . $mail->ErrorInfo;
			
		$mail->ClearAddresses();
        unset($mail);
    }else return FALSE;
}
			
// sesssion
function sec_session_start() {
        $session_name = 'GS'.md5(dirname($_SERVER['PHP_SELF']).$_SERVER['SERVER_NAME'].$_SERVER['HTTP_USER_AGENT']); // Set a custom session name
        $secure = false; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id. 
 
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session

    if( isset($_SESSION['last_access']) && (time() - $_SESSION['last_access']) > 5*60 )
        session_regenerate_id(true); // regenerated the session, delete the old one.     
}

function login($log, $password, $bdd,$type) {
   // Using prepared Statements means that SQL injection is not possible.
 //   $sql="";
    if($type=="admin")
        $sql=true;
    else
        $sql= NULL; //
   if ($stmt = $bdd->prepare("SELECT id, login, pass, salt FROM user WHERE `adminID` <=> ? AND login = ? LIMIT 1")) {
    //  $stmt->bind_param('s', $sql); // Bind type
      $stmt->bind_param('ss', $sql,$log); // Bind "login" to parameter.
      $stmt->execute(); // Execute the prepared query.
      $stmt->store_result();
      $stmt->bind_result($user_id, $username, $db_password, $salt); // get variables from result.
      $stmt->fetch();

      $password = hash('sha512', $password.$salt); // hash the password with the unique salt.
	  
      if($stmt->num_rows == 1) { // If the user exists
         // We check if the account is locked from too many login attempts
         if(checkbrute($user_id, $bdd) == true) {
             //ToDo:
            // Account is locked
            // Send an email to user saying their account is locked
         //   return false;
            return 1;
         } else {
         if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
            // Password is correct!
 
 
               $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

               $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
               $_SESSION['user_id'] = $user_id; 
               $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
               $_SESSION['username'] = $username;
               $_SESSION['login_string'] = hash('sha512', $password.$user_browser);

             if($type=="admin")
                 $sql=TRUE;
             else
                 $sql=FALSE;
             $_SESSION['admin'] = $sql;
             $_SESSION['last_access'] = time();

             // Login successful.
               return true;    
         } else {
            // Password is not correct
            // We record this attempt in the database
            $now = time();
            $bdd->query("INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')");
            return 2;
         }
      }
      } else {
         // No user exists. 
         return 3;
      }
   }else{
       return 4;
   }
}

function checkbrute($user_id, $bdd) {
   // Get timestamp of current time
   $now = time();
   // All login attempts are counted from the past 15 min.
   $valid_attempts = $now - (15 * 60);
 
   if ($stmt = $bdd->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) { 
      $stmt->bind_param('i', $user_id); 
      // Execute the prepared query.
      $stmt->execute();
      $stmt->store_result();
      // If there has been more than 5 failed logins
      if($stmt->num_rows > 5) {
         return true;
      } else {
         return false;
      }
   }
}

function login_check($bdd) {
   // Check if all session variables are set
   if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {

       if( !isset($_SESSION['last_access']) || (time() - $_SESSION['last_access']) < 60*60 )
           $_SESSION['last_access'] = time();
       else return false;

       $user_id = $_SESSION['user_id'];
     $login_string = $_SESSION['login_string'];
     $username = $_SESSION['username'];
 
     $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
     if ($stmt = $bdd->prepare("SELECT pass FROM user WHERE id = ? LIMIT 1")) { 
        $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
        $stmt->execute(); // Execute the prepared query.
        $stmt->store_result();
 
        if($stmt->num_rows == 1) { // If the user exists
           $stmt->bind_result($password); // get variables from result.
           $stmt->fetch();
           $login_check = hash('sha512', $password.$user_browser);
           if($login_check == $login_string) {
              // Logged In!!!!
              return true;
           } else {
              // Not logged in
              return false;
           }
        } else {
            // Not logged in
            return false;
        }
     } else {
        // Not logged in
        return false;
     }
   } else {
     // Not logged in
     return false;
   }

}

    /**
     * sanitize()
     *
     * @param mixed $string
     * @param bool $trim
     * @return
     */
    function sanitize($string, $trim = false, $int = false, $end_char = '&#8230;', $str = false)
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        $string = trim($string);
        $string = stripslashes($string);
        $string = strip_tags($string);
        $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);

        if ($trim) {
            if (strlen($string) < $trim)
            {
                return $string;
            }

            $string = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string));

            if (strlen($string) <= $trim)
            {
                return $string;
            }

            $out = "";
            foreach (explode(' ', trim($string)) as $val)
            {
                $out .= $val.' ';

                if (strlen($out) >= $trim)
                {
                    $out = trim($out);
                    return (strlen($out) == strlen($string)) ? $out : $out.$end_char;
                }
            }

            //$string = substr($string, 0, $trim);

        }
        if ($int)
            $string = preg_replace("/[^0-9\s]/", "", $string);
        if ($str)
            $string = preg_replace("/[^a-zA-Z\s]/", "", $string);

        return $string;
    }

    /**
     * getRowById()
     *
     * @param mixed $table
     * @param mixed $id
     * @param bool $and
     * @param bool $is_admin
     * @return
     */
    function getRowById($bdd,$table, $id, $and = false, $is_admin = true)
{
    $id = sanitize($id, 8, true);
    if ($and) {
        $sql = "SELECT * FROM " . (string)$table . " WHERE id = '" . $bdd->escape_string((int)$id) . "' AND " . $bdd->escape($and) . "";
    } else
        $sql = "SELECT * FROM " . (string)$table . " WHERE id = '" . $bdd->escape_string((int)$id) . "'";

    $row = $bdd->query($sql);

    if ($row) {
        return $row;
    } else {

    }

}

    /**
     * isRowExistant()
     *
     * @param mixed $table
     * @param mixed $id
     * @param bool $and
     * @param bool $is_admin
     * @return
     */
    function isRowExistant($bdd,$table, $id, $and = false, $is_admin = true)
    {
        $id = sanitize($id, 8, true);
        if ($and) {
            $sql = "SELECT 1 FROM " . (string)$table . " WHERE id = '" . $bdd->escape_string((int)$id) . "' AND " . $bdd->escape($and) . " LIMIT 1";
        } else
            $sql = "SELECT 1 FROM " . (string)$table . " WHERE id = '" . $bdd->escape_string((int)$id) . "' LIMIT 1";

        $row = $bdd->query($sql);

        if ($row && $row->num_rows>0) {
            return TRUE;
        } else {
         /*   if ($is_admin)
                $this->error("Invalid Id - #".$id); */
            return FALSE;
        }

    }

    /**
     * cleanHMTL()
     *
     * @param mixed $text
     * @return
     */
    function cleanHTLM($text) {
        $text =  strtr($text, array('\r\n' => "", '\r' => "", '\n' => ""));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = str_replace('<br>', '<br />', $text);
        return stripslashes($text);
    }

    /**
     * getValue()
     *
     * @param mixed $stwhatring
     * @param mixed $table
     * @param mixed $where
     * @return
     */
    function getValue($what, $table, $where,$bdd)
    {
        global $db;
        $sql = "SELECT $what FROM $table WHERE $where";
        $res = $bdd->query($sql);
        if($res && $res->num_rows>0 && $row=$res->fetch_assoc())
        return $row[$what];
        else return "";
    }
class tools
{
    static function is_entier($x)
    {
        return (!empty($x) && $x == (sanitize($x,false,true)));
    }
    static function sanitize($string, $trim = false, $int = false, $str = false, $end_char = '&#8230;')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        $string = trim($string);
        $string = stripslashes($string);
        $string = strip_tags($string);
        $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);

        if ($trim) {
            if (strlen($string) < $trim)
            {
                return $string;
            }

            $string = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string));

            if (strlen($string) <= $trim)
            {
                return $string;
            }

            $out = "";
            foreach (explode(' ', trim($string)) as $val)
            {
                $out .= $val.' ';

                if (strlen($out) >= $trim)
                {
                    $out = trim($out);
                    return (strlen($out) == strlen($string)) ? $out : $out.$end_char;
                }
            }

            //$string = substr($string, 0, $trim);

        }
        if ($int)
            $string = preg_replace("/[^0-9\s]/", "", $string);
        if ($str)
            $string = preg_replace("/[^a-zA-Z\s]/", "", $string);

        return $string;
    }

}

class enseignant
{
    public $bdd;
    public $email;
    public $grade;
    public $nom;
    public $prenom;
    public $enseignantID;
    public $departementID;
    public $vacataire;
    public $annee;
    public $actif;
    public function __construct($bd)
    {
        $this->bdd = $bd;
    }
    public function getFromId($id)
    {

        if ($stmt = $this->bdd->prepare("SELECT `email`, `grade`, `nom`, `prenom`, `enseignantID`, `departementID`, `vacataire`, `annee` FROM `enseignant` WHERE `enseignantID`= ? LIMIT 1")) {
            $stmt->bind_param('i', $id);
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();
            $stmt->bind_result($this->email, $this->grade, $this->nom, $this->prenom,$this->enseignantID,$this->departementID,$this->vacataire,$this->annee); // get variables from result.
            $stmt->fetch();

            if ($stmt->num_rows == 1) {
                return true;
            }

        }
        return false;
    }
    public function getGrade($year)
    {

        if ($stmt = $this->bdd->prepare("SELECT  `grade`, `actif` FROM `enseignant_actif` WHERE `enseignant`=? AND `annee`=?")) {
            $stmt->bind_param('ii', $this->enseignantID,$year);
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();
            $stmt->bind_result( $this->grade, $this->actif); // get variables from result.
            $stmt->fetch();

            if ($stmt->num_rows == 1) {
                return true;
            }

        }
        return false;
    }
    public function getAffectations($annee,$partage=false,$cours=false,$td=false,$tp=false)
    {
        $liste = array();
        if(tools::is_entier($this->enseignantID)) {
            if($cours)
                $add = " `nature`='cours' AND ";
            elseif($td)
                $add = " `nature`='TD' AND ";
            elseif($tp)
                $add = " `nature`='TP' AND ";
            else
                $add = "";

            if($partage)
                $sql ="SELECT AP.`affectationID` FROM `affectation_partage` AS AP LEFT JOIN affectation AS A ON AP.`affectationID`=A.`affectationID` WHERE $add AP.`enseignantID`=? AND A.annee_UniversitaireID=?";
            else
                $sql ="SELECT `affectationID` FROM `affectation` WHERE $add `enseignantID`=? AND A.annee_UniversitaireID=?";
            //echo $sql.$this->enseignantID;
            if ($stmt = $this->bdd->prepare($sql)) {
                $stmt->bind_param('ii', $this->enseignantID,$annee);
                $stmt->execute(); // Execute the prepared query.
                $stmt->store_result();
                $stmt->bind_result($aff_id); // get variables from result.

                $i=0;
                while($stmt->fetch()) {
                    $liste[$i++] = $aff_id; //new affectation($this->bdd);
                    //$liste[$i++]->getFromId($aff_id);

                }


            }

        }
        return $liste;

    }
    public function getFullName()
    {
        return $this->nom." ".$this->prenom;
    }


}
class affectation
{
    public $bdd;
    public $affectationID;
    public $enseignantID;
    public $element_Module_DetailsID;
    public $annee_UniversitaireID;
    public $nature;
    public $groups;
    public $auto;
    public $partage;

    public function __construct($bd)
    {
        $this->bdd = $bd;
    }
    public function getFromId($id)
    {

        if ($stmt = $this->bdd->prepare("SELECT `enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `affectationID`, `groups`, `auto`, `partage` FROM `affectation` WHERE `affectationID`= ? LIMIT 1")) {
            $stmt->bind_param('i', $id);
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();
            $stmt->bind_result($this->enseignantID, $this->element_Module_DetailsID, $this->annee_UniversitaireID, $this->nature,$this->affectationID,$this->groups,$this->auto,$this->partage); // get variables from result.
            $stmt->fetch();

            if ($stmt->num_rows == 1) {
                return true;
            }

        }
        return false;
    }
    public function insert()
    {
        if($st = $this->bdd->prepare("INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`, `auto`, `partage`) VALUES (?,?,?,?,?,?,?)"))
        {
            $st->bind_param('iiisiii', $this->enseignantID, $this->element_Module_DetailsID, $this->annee_UniversitaireID, $this->nature,$this->groups,$this->auto,$this->partage); // Bind "login" to parameter.
            $st->execute();
            if($st->affected_rows>0)
            {
                $this->affectationID = $st->insert_id;

                return true;
            }
        }
        return false;
    }
    public function update()
    {
        if($this->exists() && $st = $this->bdd->prepare("UPDATE `affectation` SET `enseignantID`=?,`element_Module_DetailsID`=?,`annee_UniversitaireID`=?,`nature`=?,`groups`=?,`auto`=?,`partage`=? WHERE `affectationID`=?"))
        {
            $st->bind_param('iiisiiii', $this->enseignantID, $this->element_Module_DetailsID, $this->annee_UniversitaireID, $this->nature,$this->groups,$this->auto,$this->partage,$this->affectationID); // Bind "login" to parameter.
            $st->execute();
            if($st->affected_rows>0)
            {
                return true;
            }
        }
        return false;
    }

    public function ajouter_partage($enseignantID)
    {
        if(!$this->partage_existe($enseignantID) && $enseignantID!=$this->enseignantID)
        {
            if($st = $this->bdd->prepare("INSERT INTO `affectation_partage`(`affectationID`, `enseignantID`) VALUES (?,?)"))
            {
                $st->bind_param('ii', $this->affectationID, $enseignantID);
                $st->execute();
                if($st->affected_rows>0)
                {
                //    if($this->partage==0)
                    $old_id = $this->enseignantID;
                    {

                        $this->enseignantID=NULL;
                        $this->partage=1;
                        $this->update();

                    }
                    if($old_id!=NULL)
                        $this->ajouter_partage($old_id);

                    return true;
                }
            }
        }else
        {
            if(count($this->partage_ens_liste())==1)
            {
                $this->supprimer_partage($enseignantID);
            }
        }
        return false;
    }
    public function getDetails()
    {
        $sql ="SELECT M.`periode`,ED.`grp_cours`,ED.`grp_td`,ED.`grp_tp`,E.heures_cours,E.heures_td,E.heures_tp  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.affectationID=?";
        //(SELECT IFNULL(sum(`groups`),0) AS grp_cours_aff, IFNULL(sum(`heures_cours`*`groups`),0) AS hrs_cours_aff  FROM `affectation` AS A LEFT JOIN `element_module_details` AS ED ON ED.`element_Module_DetailsID` = A.`element_Module_DetailsID` LEFT JOIN  `module_details` AS M ON M.`module_DetailsID`=ED.`module_DetailsID`  LEFT JOIN `element_module` AS E ON E.`element_ModuleID`=ED.`element_ModuleID` WHERE A.`enseignantID`='.$row['enseignantID'].' AND A.`annee_UniversitaireID`='.$CurYear.' AND A.`nature`="cours" AND M.`periode`=1)

        $res['periode']=0;
        $res['grp_cours']=0;
        $res['grp_td']=0;
        $res['grp_tp']=0;
        $res['heures_cours']=0;
        $res['heures_td']=0;
        $res['heures_tp']=0;

        if(tools::is_entier($this->affectationID)) {


            if ($st = $this->bdd->prepare($sql)) {
                $st->bind_param('i', $this->affectationID);
                $st->execute();

                $st->store_result();
                $st->bind_result($res['periode'], $res['grp_cours'],$res['grp_td'], $res['grp_tp'],$res['heures_cours'],$res['heures_td'],$res['heures_tp']); // get variables from result.

                $st->fetch();

            }
        }
        return $res;

    }

    public function supprimer()
    {
        if(tools::is_entier($this->affectationID)) {

            if(count($this->partage_ens_liste())>0)
            {
                if($st = $this->bdd->prepare("DELETE FROM `affectation_partage` WHERE `affectationID`=?"))
                {
                    $st->bind_param('i', $this->affectationID);
                    $st->execute();
                }
            }
            if ($st = $this->bdd->prepare("DELETE FROM `affectation` WHERE `affectationID`=?")) {
                $st->bind_param('i', $this->affectationID);
                $st->execute();
                if ($st->affected_rows > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    public function supprimer_partage($enseignantID)
    {
        $liste = $this->partage_ens_liste();
        $nbr = count($liste);
        if($this->partage_existe($enseignantID))
        {
            if($nbr==1)
            {
                $this->supprimer();

            }elseif($nbr==2)
            {
                if($st = $this->bdd->prepare("DELETE FROM `affectation_partage` WHERE `affectationID`=?"))
                {
                    $st->bind_param('i', $this->affectationID);
                    $st->execute();
                    if($st->affected_rows>0)
                    {
                        if($liste[0]==$enseignantID)
                            $this->enseignantID=$liste[1];
                        else
                            $this->enseignantID=$liste[0];

                        $this->partage=0;
                        $this->update();

                        return true;
                    }
                }

            }else{
                if($st = $this->bdd->prepare("DELETE FROM `affectation_partage` WHERE `affectationID`=? AND `enseignantID`=?"))
                {
                    $st->bind_param('ii', $this->affectationID, $enseignantID);
                    $st->execute();
                    if($st->affected_rows>0)
                    {
                       return true;
                    }
                }
            }
        }else
        {
            if($this->enseignantID==$enseignantID)
            return  $this->supprimer();
        }
        return false;
    }
    function partage_existe($enseignantID)
    {
        if (tools::is_entier($this->affectationID) && tools::is_entier($enseignantID)) {
            //
            if ($stmt = $this->bdd->prepare("SELECT `id` FROM `affectation_partage` WHERE `affectationID`=? AND `enseignantID`=?")) {
                $stmt->bind_param('ii', $this->affectationID,$enseignantID);
                $stmt->execute(); // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows >0) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    function partage_ens_liste()
    {
        $liste = array();
        if (tools::is_entier($this->affectationID)) {
            //

            if ($stmt = $this->bdd->prepare("SELECT DISTINCT `enseignantID` FROM `affectation_partage` WHERE `affectationID`=? ORDER BY id DESC ")) {
                $stmt->bind_param('i', $this->affectationID);
                $stmt->execute(); // Execute the prepared query.
                $stmt->store_result();
                $stmt->bind_result($ens_id); // get variables from result.

                $i=0;
                while($stmt->fetch())
                {
                    $liste[$i++]=$ens_id;
                }
            }
        }
        return $liste;
    }
    public  function exists()
    {
        if (tools::is_entier($this->affectationID)) {
            //
            if ($stmt = $this->bdd->prepare("SELECT 1 FROM `affectation` WHERE `affectationID`= ?")) {
                $stmt->bind_param('i', $this->affectationID);
                $stmt->execute(); // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    public function init($enseignantID,$element_Module_DetailsID,$annee_UniversitaireID,$nature,$groups,$auto=0)
    {
        $this->enseignantID = $enseignantID;
        $this->element_Module_DetailsID = $element_Module_DetailsID;
        $this->annee_UniversitaireID= $annee_UniversitaireID;
        $this->nature= $nature;
        $this->affectationID= NULL;
        $this->groups= $groups;
        $this->auto= $auto;
        $this->partage= 0;
    }
    public function init2($element_Module_DetailsID,$annee_UniversitaireID,$nature,$groups,$auto=0)
    {
        $this->enseignantID = NULL;
        $this->element_Module_DetailsID = $element_Module_DetailsID;
        $this->annee_UniversitaireID= $annee_UniversitaireID;
        $this->nature= $nature;
        $this->affectationID= NULL;
        $this->groups= $groups;
        $this->auto= $auto;
        $this->partage= 1;
    }
    public function clear()
    {
        $this->enseignantID = NULL;
        $this->element_Module_DetailsID = NULL;
        $this->annee_UniversitaireID= NULL;
        $this->nature= NULL;
        $this->affectationID= NULL;
        $this->groups= NULL;
        $this->auto= NULL;
        $this->partage= NULL;
    }

}
/*
class article
{
    public $bdd;
    public $id;
    public $designation;
    public $lot;
    
    public function __construct($bd)
    {
        $this->bdd = $bd;
    }
    public function getFromId($id)
    {

        if ($stmt = $this->bdd->prepare("SELECT `designation`, `lot`  FROM `article` WHERE `id`= ? LIMIT 1")) {
            $stmt->bind_param('i', $id);
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();
            $stmt->bind_result($this->designation, $this->lot); // get variables from result.
            $stmt->fetch();

            if ($stmt->num_rows == 1) {
                return true;
            }
        }
        return false;
    }
    public inserer()
    {
        //khass ikounou les champs 3amrin 9bel..
    }
    static function getArticles($lot_id)
    {
        $articles = Array();
        
        if ($stmt = $this->bdd->prepare("SELECT `id` ,`designation`, `lot`  FROM `article` WHERE `lot`= ?")) {
        
        $article = new article();
        
            $stmt->bind_param('i', $lot_id);
            $stmt->execute(); 
            $stmt->store_result();
            $stmt->bind_result($article->id,$article->designation, $article->lot); 
            
          $i=0;
            while ($stmt->fetch()) {
                $article[$i++]=$article;
            }
        }
        return $articles;
    }
    //etc
}
class lot
{
    public $bdd;
    public $id;
    public $designation;
    public $nbr_article;
    public $articles;
    
    public function __construct($bd)
    {
        $this->bdd = $bd;
    }
    public function getFromId($id)
    {

      
    }
    public inserer()
    {
        //khass ikounou les champs 3amrin 9bel..
    }
    //etc
}
*/
?>