<?php
    define("_VALID_PHP", true);
include_once 'include/connexion_BD.php';
include_once 'include/fonctions.php';
sec_session_start();
if(login_check($bdd) == true) {

//option de validation des entiers
$int = array('options' => array('min_range' => 1));
$admin=isAdmin($bdd);
$college=isCollege($bdd);
$doyen=isDoyen($bdd);

$curYear=get_cur_year("id",$bdd);
    /*  Configuration: records per page  */
    if(!empty($_GET['ePP']))
    {
        $l=sanitize($_GET['ePP'],false,true);
        $sql="UPDATE `configuration` SET `elementParPage`='".$l."' WHERE user=".$_SESSION['user_id'];
        $bdd->query($sql);
        die();
    }
 /*  Configuration: theme  */
    if(!empty($_GET['theme']))
    {
        $l=sanitize($_GET['theme']);
        $sql="UPDATE `configuration` SET `theme`='".$l."' WHERE user=".$_SESSION['user_id'];
        $bdd->query($sql);
        die();
    }

  //  $AUvalid=!isAUvalid($bdd);
// x-editable 
if(isset($_POST['value']) && isset($_POST['name']) && isset($_POST['pk']))
{
	/*print $_POST['id'];*/
//	$myFile = "testFile.txt";
//	$fh = fopen($myFile, 'w') or die("can't open file");
//	$stringData = "value=".$_POST['value']."\n".$_POST['name']."\n".$_POST['pk'];
//	fwrite($fh, $stringData);  fclose($fh);

	//anti xss protection
    if(!is_array($_POST['value']))
    $_POST['value']=safe_input($_POST['value'],$bdd);//$bdd->real_escape_string(strip_tags($_POST['value']));
    $_POST['pk']=sanitize($_POST['pk'],false,true);//$bdd->real_escape_string(strip_tags($_POST['pk']));

	//annee universitaire
	if($_POST['name']=="annee")
	{
        if (!filter_var($_POST['value'], FILTER_VALIDATE_INT, $int))
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Id invalide!!"));
            die();
        }
        $sql="UPDATE `configuration` SET `annee_courrante`=".$_POST['value']." WHERE user=".$_SESSION['user_id'];;

        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
    if($admin || $doyen )
    {
        // Modification de l'annèe universitaire
        if($_POST['name']=="au_description")
        {
            if (!filter_var($_POST['pk'], FILTER_VALIDATE_INT, $int))
            {
                echo json_encode(array('succes'=> false, 'mssg'=> "Id invalide!!"));
                die();
            }
            if ($stmt = $bdd->prepare("UPDATE `annee_universitaire` SET `annee_univ`=? WHERE `annee_UniversitaireID`=?")) {
                $stmt->bind_param('si', $_POST['value'], $_POST['pk']);
                // Execute the prepared query.
                $stmt->execute();
                //   $stmt->store_result();

                if($stmt->affected_rows == 1) {
                    echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
                } else {
                    echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
                }
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!"));
        }
        if($_POST['name']=="au_valid")
        {

            if (!filter_var($_POST['pk'], FILTER_VALIDATE_INT, $int))
            {
                echo json_encode(array('succes'=> false, 'mssg'=> "Id invalide!!"));
                die();
            }
            if ($stmt = $bdd->prepare("UPDATE `annee_universitaire` SET `valid`=? WHERE `annee_UniversitaireID`=?")) {
                $stmt->bind_param('ii', $_POST['value'], $_POST['pk']);
                // Execute the prepared query.
                $stmt->execute();
                //   $stmt->store_result();

                if($stmt->affected_rows == 1) {
                    echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
                } else {
                    echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
                }
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!"));
        }
    }
//only admin
if($admin || $college)
{
    //users
    if($admin)
    if($_POST['name']=="user_access")
    {
        $access="";
        if($_POST['value']=="1") $access = _ADMIN;
        elseif($_POST['value']==2) $access = _COLLEGE;
        elseif($_POST['value']==3) $access = _PROF;

        if(empty($access)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans l'accès!"));
            die();
        }
        $sql="SELECT `id` FROM `user` WHERE `access`='"._ADMIN."'";
        $res=$bdd->query($sql);
        if($res == TRUE)
        {
            if($res->num_rows >0)
            {
                if($row=$res->fetch_assoc())
                {
                    $admin_id=$row['id'];

                    if($admin_id==$_POST['pk']){
                        echo json_encode(array('succes'=> false, 'mssg'=> "Pour changer le chef du departement il faut choisir un autre.."));
                        die();
                    }
                    if($access==_ADMIN){
                        $sql="UPDATE `user` SET `access`='"._PROF."' WHERE `id`=".$admin_id;
                        $bdd->query($sql);
                    }

                }else  {echo "Problème interne!!";  die();}

            }
          //  else;
          //      $sql='INSERT INTO `grade_actif`(`grade`,`chargeHrs` ,`annee`) VALUES ('.($_POST['pk']).','.$_POST['value'].','.$curYear.')';
            $sql="UPDATE `user` SET `access`='".$access."' WHERE `id`=".$_POST['pk'];
            if($bdd->query($sql))
                echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
            else
                echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));


        }else echo 'Problème interne!';



    }
	//grade
	if($_POST['name']=="grade_code")
	{
        $sql='UPDATE `grade` SET `code`="'.$_POST['value'].'" WHERE `gradeID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

    }
	if($_POST['name']=="grade_designation")
	{
		$sql='UPDATE `grade` SET `designation`="'.$_POST['value'].'" WHERE `gradeID`='.$_POST['pk'];

        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}	
	if($_POST['name']=="grade_charge")
	{	
		if (!filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)) 
		{
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!"));
			die();
		}

        $sql="SELECT `id` FROM `grade_actif` WHERE `grade`=".$_POST['pk']." AND `annee`=".$curYear;
        $res=$bdd->query($sql);
        if($res == TRUE)
        {
            if($res->num_rows >0)
            {

                if($row=$res->fetch_assoc())
                {
                    $sql='UPDATE `grade_actif` SET `chargeHrs`='.$_POST['value'].' WHERE `id`='.$row['id'];

                }else  {echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!"));  die();}

            }
            else
                $sql='INSERT INTO `grade_actif`(`grade`,`chargeHrs` ,`annee`) VALUES ('.($_POST['pk']).','.$_POST['value'].','.$curYear.')';

        //    $sql='UPDATE `grade` SET `chargeHrs`="'.$_POST['value'].'" WHERE `gradeID`='.$_POST['pk'];

            //$bdd->affected_rows ==0
            if($bdd->query($sql))
                echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
            else
                echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!"));
	}	
	if($_POST['name']=="grade_permission")
	{
		$err=0;
		if(in_array("1", $_POST['value']))
		{
			$sql='UPDATE `grade` SET `cours`=1 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		else
		{
			$sql='UPDATE `grade` SET `cours`=0 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		if(in_array("2", $_POST['value']))
		{
			$sql='UPDATE `grade` SET `TD`=1 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		else
		{
			$sql='UPDATE `grade` SET `TD`=0 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		if(in_array("3", $_POST['value']))
		{
			$sql='UPDATE `grade` SET `TP`=1 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		else
		{
			$sql='UPDATE `grade` SET `TP`=0 WHERE `gradeID`='.$_POST['pk'];
			$res = $bdd->query($sql);
			if($res== FALSE ) $err++;
		}
		
		if($err==0)
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
    if($_POST['name']=="grade_status")
    {
        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT `id` FROM `grade_actif` WHERE `grade`=".$_POST['pk']." AND `annee`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                if($res->num_rows >0)
                {
                    $actif=($_POST['value']=="actif")?1:0;

                    $grade_used=is_grade_used($_POST['pk'],$curYear,$bdd);
                    if(!$actif && $grade_used){
                            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Il existe des enseignants actif de ce grade cette année!!"));
                        die();
                    }

                    if($row=$res->fetch_assoc())
                    {
                        //     $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
                        $sql='UPDATE `grade_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];
                        //         echo $sql;
                    }else  {echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));  die();}

                }
                else
                    $sql='INSERT INTO `grade_actif`(`grade`, `annee`) VALUES ('.($_POST['pk']).','.$curYear.')';

                $bdd->query($sql);

                echo json_encode(array('succes'=> true));
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));
    }
	//enseignants
	if($_POST['name']=="prof_nom")
	{
		$sql='UPDATE `enseignant` SET `nom`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="prof_email")
	{
        if (!filter_var($_POST['value'], FILTER_VALIDATE_EMAIL))
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Adresse email invalide!!"));
            die();
        }
            $sql='UPDATE `enseignant` SET `email`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

	}
	if($_POST['name']=="prof_prenom")
	{
		$sql='UPDATE `enseignant` SET `prenom`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="prof_dept")
	{
        if(is_prof_used($_POST['pk'],$curYear,$bdd)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (...)!!"));
            die();
        }
		$sql='UPDATE `enseignant` SET `departementID`='.$_POST['value'].' WHERE `enseignantID`='.$_POST['pk'];
		if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

	}
	if($_POST['name']=="prof_grade")
	{
        if(is_prof_used($_POST['pk'],$curYear,$bdd)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (...)!!"));
            die();
        }

        $sql="SELECT `id`, `grade`,`actif` FROM `enseignant_actif` WHERE `enseignant`=".$_POST['pk']." AND `annee`=".$curYear;
        $res=$bdd->query($sql);
        if($res == TRUE)
        {
            if($res->num_rows >0)
            {
                if($row=$res->fetch_assoc())
                {
                    if($row['actif']==0)
                    {
                        echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: enseignant inactif!!"));
                        die();
                    }
                    //     $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
                    $sql='UPDATE `enseignant_actif` SET `grade`='.$_POST['value'].' WHERE `id`='.$row['id'];
                    //         echo $sql;
                }else { echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));  die();}

            }
            else{
                echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: enseignant inactif!!"));
                die();
            }
            //    $sql='INSERT INTO `enseignant_actif`(`enseignant`, `annee`) VALUES ('.($_POST['pk']).','.$curYear.')';

            $bdd->query($sql);
            //
            echo json_encode(array('succes'=> true));
        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

        /*
		$sql='UPDATE `enseignant` SET `grade`='.$_POST['value'].' WHERE `enseignantID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	*/
    }
	if($_POST['name']=="prof_status")
	{
        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT `id` FROM `enseignant_actif` WHERE `enseignant`=".$_POST['pk']." AND `annee`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                $actif=($_POST['value']=="actif")?1:0;
                if($res->num_rows >0)
                {
                    $prof_used=is_prof_used($_POST['pk'],$curYear,$bdd);
                    if(!$actif && $prof_used){

                        if($prof_used==1)
                            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (affectations)!!"));
                        elseif($prof_used==2)
                            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (shouaits)!!"));
                        elseif($prof_used==3)
                            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (fiche)!!"));
                        elseif($prof_used==4)
                            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: cet enseignant participe cette année (chef d'un département)!!"));

                        die();
                    }

                    if($row=$res->fetch_assoc())
                    {
                        //     $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
                        $sql='UPDATE `enseignant_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];
               //         echo $sql;
                    }else { echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));  die();}

                }
                else
                    $sql='INSERT INTO `enseignant_actif`(`enseignant`, `annee`, `actif`) VALUES ('.($_POST['pk']).','.$curYear.','.$actif.')';
              //      $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];

                $bdd->query($sql);
           //
                echo json_encode(array('succes'=> true));
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));


        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));
           // print '{succes: false, mssg: "Problème dans la valeur!!"}';
	}
	//Departements
	if($_POST['name']=="dept_designation")
	{
		$sql='UPDATE `departement` SET `designation`="'.$_POST['value'].'" WHERE `departementID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="dept_chef")
	{

        $sql="SELECT `id` FROM `departement_chef` WHERE `departement`=".$_POST['pk']." AND `annee`=".$curYear;
        $res=$bdd->query($sql);
        if($res == TRUE)
        {
            if($res->num_rows >0)
            {
                if($row=$res->fetch_assoc())
                {


                    $sql="UPDATE `departement_chef` SET `enseignant`=".$_POST['value']." WHERE `id`=".$row['id'];
                    //         echo $sql;
                }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

            }
            else
                $sql="INSERT INTO `departement_chef`( `departement`, `enseignant`, `annee`) VALUES (".$_POST['pk'].",".$_POST['value'].",".$curYear.")";


            if($bdd->query($sql))
            echo json_encode(array('succes'=> true));
            else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!".$sql));

        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

	}
	if($_POST['name']=="dept_status")
	{

        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT `id` FROM `departement_actif` WHERE `departement`=".$_POST['pk']." AND `annee`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                if($res->num_rows >0)
                {
                    $actif=($_POST['value']=="actif")?1:0;
                    if(!$actif && is_dept_used($_POST['pk'],$curYear,$bdd)){
                        echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Ce département est utilisé cette annèe !!"));
                        die();
                    }
                    if($row=$res->fetch_assoc())
                    {
                        //     $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];
                        $sql='UPDATE `departement_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];
                        //         echo $sql;
                    }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                }
                else
                    $sql='INSERT INTO `departement_actif`(`departement`, `annee`) VALUES ('.($_POST['pk']).','.$curYear.')';
                //      $sql='UPDATE `enseignant` SET `status`="'.$_POST['value'].'" WHERE `enseignantID`='.$_POST['pk'];

                if($bdd->query($sql))
                    echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
                else
                    echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

             //   echo json_encode(array('succes'=> true));
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));


        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));


	}
	//Cycles
	if($_POST['name']=="cycle_designation")
	{
		$sql='UPDATE `cycle` SET `designation`="'.$_POST['value'].'" WHERE `cycleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="cycle_sem")
	{
        if (!filter_var($_POST['value'], FILTER_VALIDATE_INT, $int))
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
		$sql='UPDATE `cycle` SET `nb_semestres`='.$_POST['value'].' WHERE `cycleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="cycle_status")
	{

        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT `id` FROM `cycle_actif` WHERE `cycle`=".$_POST['pk']." AND `annee`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                if($res->num_rows >0)
                {
                    $actif=($_POST['value']=="actif")?1:0;
                    if(!$actif && is_cycle_used($_POST['pk'],$curYear,$bdd)){
                        echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Ce cycle est utilisé cette annèe !!"));
                        die();
                    }
                    if($row=$res->fetch_assoc())
                    {
                        $sql='UPDATE `cycle_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];

                    }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                }
                else
                    $sql='INSERT INTO `cycle_actif`(`cycle`, `annee`) VALUES ('.($_POST['pk']).','.$curYear.')';

                if($bdd->query($sql))
                    echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
                else
                    echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));


        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));

	}
	//Filieres
	if($_POST['name']=="filiere_designation")
	{
		$sql='UPDATE `filiere` SET `designation`="'.$_POST['value'].'" WHERE `filiereID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="filiere_cycle")
	{
        if(is_filiere_used($_POST['pk'],$curYear,$bdd)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Cette filière est utilisée cette annèe !!"));
            die();
        }
		$sql='UPDATE `filiere` SET `cycleID`="'.$_POST['value'].'" WHERE `filiereID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="filiere_dept")
	{
        if(is_filiere_used($_POST['pk'],$curYear,$bdd)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Cette filière est utilisée cette annèe !!"));
            die();
        }

		if($_POST['value']=="")
		$sql='UPDATE `filiere` SET `departementID`=NULL WHERE `filiereID`='.$_POST['pk'];
		else
		$sql='UPDATE `filiere` SET `departementID`="'.$_POST['value'].'" WHERE `filiereID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="filiere_status")
	{

        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT `id` FROM `filiere_actif` WHERE `filiere`=".$_POST['pk']." AND `annee`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                if($res->num_rows >0)
                {
                    $actif=($_POST['value']=="actif")?1:0;
                    if(!$actif && is_filiere_used($_POST['pk'],$curYear,$bdd)){
                        echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: Cette filière est utilisée cette annèe !!"));
                        die();
                    }
                    if($row=$res->fetch_assoc())
                    {
                        $sql='UPDATE `filiere_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];

                    }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                }
                else
                    $sql='INSERT INTO `filiere_actif`(`filiere`, `annee`) VALUES ('.($_POST['pk']).','.$curYear.')';

                if($bdd->query($sql))
                    echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
                else
                    echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));

            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));


        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));

        /*
		$sql='UPDATE `filiere` SET `status`="'.$_POST['value'].'" WHERE `filiereID`='.$_POST['pk'];
		$bdd->query($sql); */
	}
	//Module
	if($_POST['name']=="module_code")
	{
		$sql='UPDATE `module` SET `code`="'.$_POST['value'].'" WHERE `moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="module_designation")
	{
		$sql='UPDATE `module` SET `designation`="'.$_POST['value'].'" WHERE `moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="module_filiere")
	{
        if(is_module_planified($_POST['pk'],$curYear,$bdd)){
            echo json_encode(array('succes'=> false, 'mssg'=> "Impossible: le module est plannifié cette annèe !!"));
            die();
        }
		$sql='UPDATE `module` SET `filiereID`="'.$_POST['value'].'" WHERE `moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="module_sem")
	{

        if (!filter_var($_POST['value'], FILTER_VALIDATE_INT, $int))
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
        $sql="SELECT 1 FROM `module_details` WHERE `moduleID`=".$_POST['pk']." AND `annee_UniversitaireID`=".$curYear;

        $res=$bdd->query($sql);
        if($res && $res->num_rows>0)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Ce module est planifié pour cette année!!")); die();
        }

        $sql='UPDATE `module` SET `semestre`="'.$_POST['value'].'" WHERE `moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="module_status")
	{
        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT 1 FROM `module_details` WHERE `moduleID`=".$_POST['pk']." AND `annee_UniversitaireID`=".$curYear;
            $res=$bdd->query($sql);
            if($res == TRUE)
            {
                $planified=$res->num_rows;
                $sql="SELECT `id` FROM `module_actif` WHERE `module`=".$_POST['pk']." AND `annee`=".$curYear;
                $res=$bdd->query($sql);
                if($res == TRUE)
                {
                    $actif=($_POST['value']=="actif")?1:0;
                    $num=$res->num_rows;
                    if($num >0)
                    {

                        if($actif==0 && $planified!=0) //todo remove the 1st condition
                        {
                           echo json_encode(array('succes'=> false, 'mssg'=> "Le module est planifié pour cette année!!"));
                            die();
                        }
                        if($row=$res->fetch_assoc())
                        {
                            $sql='UPDATE `module_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];

                        }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                    }
                    else
                        $sql='INSERT INTO `module_actif`(`module`, `annee`,`actif`) VALUES ('.($_POST['pk']).','.$curYear.','.$actif.')';

                    if($bdd->query($sql)) $no_err=1;
                    //mise a jour des elements
                    $sql="UPDATE `element_module_actif` SET `actif`=".$actif." WHERE `element_module` in (SELECT `element_ModuleID` FROM `element_module` WHERE `moduleID`=".$_POST['pk'].") AND `annee`=".$curYear;
                    $bdd->query($sql);
                    //just in case
                    if($bdd->affected_rows == 0)
                    {
                        $sql="SELECT `element_ModuleID` FROM `element_module` WHERE `moduleID`=".$_POST['pk'];
                        $res=$bdd->query($sql);
                        if($res == TRUE && $res->num_rows >0)
                        {
                            while($row=$res->fetch_assoc())
                            {
                                $sql='INSERT INTO `element_module_actif`(`element_module`, `annee`,`actif`) VALUES ('.$row['element_ModuleID'].','.$curYear.','.$actif.')';
                                if($bdd->query($sql)) $no_err++;
                            }
                        }

                    }

            //        echo json_encode(array('succes'=> false, 'mssg'=> $sql));
                    if(isset($no_err))echo json_encode(array('succes'=> true));
                    else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));


            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));



        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));


	}
	// Elements de module 
	if($_POST['name']=="elem_code")
	{
		$sql='UPDATE `element_module` SET `code`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_designation")
	{
		$sql='UPDATE `element_module` SET `designation`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_hrs_cours")
	{
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
		$sql='UPDATE `element_module` SET `heures_cours`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_hrs_td")
	{
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
		$sql='UPDATE `element_module` SET `heures_td`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_hrs_tp")
	{
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
		$sql='UPDATE `element_module` SET `heures_tp`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_dept")
	{
		$sql='UPDATE `element_module` SET `departementID`="'.$_POST['value'].'" WHERE `element_moduleID`='.$_POST['pk'];
        if($bdd->query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
	}
	if($_POST['name']=="elem_status")
	{

        if($_POST['value']=="actif" || $_POST['value']=="inactif")
        {
            $sql="SELECT EM.`moduleID`, (SELECT 1 FROM `module_details` AS MD WHERE MD.`moduleID`=EM.`moduleID` AND MD.`annee_UniversitaireID`=".$curYear." LIMIT 1) AS actif FROM `element_module` AS EM WHERE `element_moduleID`=".$_POST['pk'];
            $res=$bdd->query($sql);
            if($res == TRUE && $res->num_rows >0 && ($row=$res->fetch_assoc()))
            {
                if($row['actif']!=1)
                {
                    $mod=$row['moduleID'];
                    //cas ou le module est inactif ".$_POST['pk']."
                    $sql="SELECT `actif` FROM `module_actif` WHERE `module` in (SELECT `moduleID` FROM `element_module` WHERE `element_ModuleID`=".$_POST['pk'].") AND `annee`=".$curYear;
                    $res=$bdd->query($sql);
                    if($res == TRUE && $res->num_rows >0)
                    {
                        if($row=$res->fetch_assoc())
                            if($row['actif']==0)
                            {
                                echo json_encode(array('succes'=> false, 'mssg'=> "Le module est inactif!!"));
                                die();
                            }
                    }else{
                        echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne (module non actif)!!"));
                        die();
                    }


                    $sql="SELECT `id` FROM `element_module_actif` WHERE `element_module`=".$_POST['pk']." AND `annee`=".$curYear;
                    $res=$bdd->query($sql);
                    $actif=($_POST['value']=="actif")?1:0;
                    if($res == TRUE )
                    {
                        if($res->num_rows >0)
                        {

                            if($row=$res->fetch_assoc())
                            {
                                $sql='UPDATE `element_module_actif` SET `actif`='.$actif.' WHERE `id`='.$row['id'];

                            }else  echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

                        }
                        else
                            $sql='INSERT INTO `element_module_actif`(`element_module`, `annee`,`actif`) VALUES ('.($_POST['pk']).','.$curYear.','.$actif.')';

                        $bdd->query($sql);
                        //le cas où tous les element sont inactif-> le module aussi



                        $sql="SELECT 1 FROM `element_module_actif` WHERE `element_module` in (SELECT `element_ModuleID` FROM `element_module` WHERE `moduleID`=".$mod.") AND `annee`=".$curYear." AND `actif`=1";
                        $res=$bdd->query($sql);
                        if($res == TRUE && $res->num_rows ==0)
                        {
                            $sql='UPDATE `module_actif` SET `actif`=0 WHERE `module`='.$mod;
                            $bdd->query($sql);
                        }

                        echo json_encode(array('succes'=> true));
                    }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));
                }else{
                    echo json_encode(array('succes'=> false, 'mssg'=> "L'element de module est planifié pour cette année!!"));
                }
            }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème interne!!"));

        }else echo json_encode(array('succes'=> false, 'mssg'=> "Problème dans la valeur!!"));


	}
    //Planification des module
    if($_POST['name']=="ModD_cours")
    {
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
        $sql='UPDATE `module_details` SET `grp_cours`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];
        $sql.='; UPDATE `element_module_details` SET `grp_cours`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];

        if($bdd->multi_query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
    }
    if($_POST['name']=="ModD_td")
    {
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
        $sql='UPDATE `module_details` SET `grp_td`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];
        $sql.='; UPDATE `element_module_details` SET `grp_td`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];

        if($bdd->multi_query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
    }
    if($_POST['name']=="ModD_tp")
    {
        $int = array('options' => array('min_range' => 0));
        if (filter_var($_POST['value'], FILTER_VALIDATE_INT, $int)===FALSE)
        {
            echo json_encode(array('succes'=> false, 'mssg'=> "Nombre invalide!!")); die();
        }
        $sql='UPDATE `module_details` SET `grp_tp`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];
        $sql.='; UPDATE `element_module_details` SET `grp_tp`="'.$_POST['value'].'" WHERE `module_DetailsID`='.$_POST['pk'];

        if($bdd->multi_query($sql))
            echo json_encode(array('succes'=> true, 'mssg'=> "mis à jour avec succès!!"));
        else
            echo json_encode(array('succes'=> false, 'mssg'=> "Problème lors de la mise à jour!!"));
    }
	///modifier grp affectation
    if($admin)
	if($_POST['name']=="new_grp" && isset($_GET['t']))
	{
		if($_GET['t']=="cours" || $_GET['t']=="TD" || $_GET['t']=="TP")
		{
			if($_POST['value']==0)
			{
				$sql="DELETE FROM `affectation` WHERE `affectationID`=".$_POST['pk'];
				$res = $bdd->query($sql);
				if($res==false) print 'ERREUR interne: DELETE'; 
				die();
			}

					$sql="SELECT  groups AS cur_grp_aff,element_Module_DetailsID,nature  FROM affectation WHERE affectation.affectationID = ".$_POST['pk'];
						$res = $bdd->query($sql);
						if($res == false || $res->num_rows ==0) {print 'ERREUR interne: ID affectation incorrecte'; die();}
						$row = $res->fetch_assoc();
						
						$cur_grp_aff=$row['cur_grp_aff'];
						if($row['nature']!=$_GET['t']) {print 'ERREUR interne: nature differents..'; die();}
						$idE=$row['element_Module_DetailsID'];
					
				$sql='SELECT   element_module_details.grp_td,element_module_details.grp_cours,  element_module_details.grp_tp
					FROM element_module_details
					WHERE element_Module_DetailsID ='.$row['element_Module_DetailsID'];
					$res = $bdd->query($sql);
					//echo $sql;
					if($res == TRUE && $res->num_rows >0) 
					{
					
						$ED = $res->fetch_assoc();
					
						if($_GET['t']=="cours") $grp=$ED['grp_cours'];
						if($_GET['t']=="TD") $grp=$ED['grp_td'];
						if($_GET['t']=="TP") $grp=$ED['grp_tp'];
						
						$sql="SELECT  affectation.groups AS cur_grp_aff FROM affectation WHERE affectation.affectationID = ".$_POST['pk'];
						$res = $bdd->query($sql);
						if($res == false || $res->num_rows ==0) {print 'ERREUR interne: ID affectation incorrecte'; die();}
						$row = $res->fetch_assoc();
						
						$cur_grp_aff=$row['cur_grp_aff'];
						
						$sql="SELECT  IFNULL(SUM(affectation.groups),0) AS grp_aff FROM affectation WHERE affectation.element_Module_DetailsID = ".$idE." AND affectation.nature = '".$_GET['t']."'";
						$res = $bdd->query($sql);
						if($res == false || $res->num_rows ==0) {print 'ERREUR interne: sql'; die();}
						$row = $res->fetch_assoc();
					
						$grp_aff=$row['grp_aff'];
						
						if((($grp_aff-$cur_grp_aff)+$_POST['value'] )> $grp) {print 'overflow: choisir un nbr plus petit'; die();}
						$sql="UPDATE `affectation` SET `groups`= ".$_POST['value']." WHERE `affectationID`=".$_POST['pk'];
						$res = $bdd->query($sql);
						if($res==false) {print 'ERREUR interne: update'; die();}
				
					}
		}
	}
}
	////
//	fwrite($fh, $sql); 
//	fclose($fh);
}
else
{

if($admin || $college)
{
    //suppression d'un parteage
    if(isset($_GET['delete']) && $_GET['delete']=="affect_partage" ) {
        if (!empty($_GET['aff_id']) && !empty($_GET['ens_id'])) {

            $a = new affectation($bdd);
            $a->getFromId($_GET['aff_id']);
            if(!$a->supprimer_partage($_GET['ens_id']))
            print '{"msg": "problème lors de la suppression" }';
            else
                print '{"ok": "1" }';

            die();
        }else
        {
            print '{"msg": "Données vides!" }';
            die();
        }
    }

	//Module details suppression
	if(isset($_GET['delete']) && $_GET['delete']=="modD" )
	{
		if(isset($_GET['id']) )
		{
			$options = array(
				'options' => array(
                      'min_range' => 0
                      )
			);
	
			if (!filter_var($_GET['id'], FILTER_VALIDATE_INT, $options)) 
			{
				print 'ERREUR INTERNE: id non valide'; die();
			}
			if(is_module_details_affected($_GET['id'],$bdd)) 
			{
				print 'ERREUR: Le module participe dans des affectations..'; die();
			}
			$sql="DELETE FROM `element_module_details` WHERE `module_DetailsID`=".$_GET['id'];
			
			$res = $bdd->query($sql);
			
			if($res == TRUE && $bdd->affected_rows >0)
			{
				$sql="DELETE FROM `module_details` WHERE `module_DetailsID`=".$_GET['id'];
				
				$res = $bdd->query($sql);
				if($res == true && $bdd->affected_rows>0) {print 'true'; die();}
				else{print 'ERREUR lors de la suppresion du module (elements supprimé...)'; die();}
			}else {print 'ERREUR lors de la suppresion des elements'; die();}
			
		}else
		print 'ERREUR INTERNE: id not set'; 
	}
	// xeditable new module element
	if(isset($_GET['new']) && $_GET['new']=="elem_mod" )
	{
		if(!empty($_GET['mod']))
		{
                $options = array(
                    'options' => array(
                        'min_range' => 0
                    )
                );

			if( !empty($_POST['elem_code']) && !empty($_POST['elem_designation']) && isset($_POST['elem_hrs_cours']) && isset($_POST['elem_hrs_td']) && isset($_POST['elem_hrs_tp'] ) && !empty($_POST['elem_dept']) )
			{
                $_POST['elem_code']=safe_input($_POST['elem_code'],$bdd);
                $_POST['elem_designation']=safe_input($_POST['elem_designation'],$bdd);
                $_POST['elem_hrs_cours']=safe_input($_POST['elem_hrs_cours'],$bdd);
                $_POST['elem_hrs_td']=safe_input($_POST['elem_hrs_td'],$bdd);
                $_POST['elem_hrs_tp']=safe_input($_POST['elem_hrs_tp'],$bdd);
                $_POST['elem_dept']=safe_input($_POST['elem_dept'],$bdd);

                if (filter_var($_POST['elem_hrs_cours'], FILTER_VALIDATE_INT, $options)===FALSE)
                {
                    print '{"errors": {"Heures_cours": "Nombre invalide!!"} }';
                    die();
                }
                if ( filter_var($_POST['elem_hrs_td'], FILTER_VALIDATE_INT, $options)===FALSE )
                {
                    print '{"errors": {"Heures_TD": "Nombre invalide!!"} }';
                    die();
                }
                if ( filter_var($_POST['elem_hrs_tp'], FILTER_VALIDATE_INT, $options)===FALSE)
                {
                    print '{"errors": {"Heures_TP": "Nombre invalide!!"} }';
                    die();
                }
				$sql="SHOW TABLE STATUS LIKE 'element_module'";
				$res = $bdd->query($sql);
				if($res == true)
				{
					if($stat = $res->fetch_assoc())
					{
						$newID=$stat['Auto_increment'];
						$sql='INSERT INTO `element_module`(`element_ModuleID`,`code`, `designation`, `heures_cours`, `heures_td`, `heures_tp`, `departementID`, `moduleID`, `annee`) VALUES ( '.$newID.', "'.$_POST['elem_code'].'","'.$_POST['elem_designation'].'","'.$_POST['elem_hrs_cours'].'","'.$_POST['elem_hrs_td'].'","'.$_POST['elem_hrs_tp'].'","'.$_POST['elem_dept'].'","'.$_GET['mod'].'",'.$curYear.')';
						if($bdd->query($sql) == TRUE)
                        {

                            $sql='INSERT INTO `element_module_actif`(`element_module`, `annee`) VALUES ('.$newID.','.$curYear.')';
                            $bdd->query($sql);

                            print '{"id": '.$newID.'}';
                        }
						else
						print '{"errors": {"Requete": "probleme dans la requete sql!"} }';
						
					//	print '{"errors": {"Requete": "'.$sql.'"} }';
						
					}
				}
			}
			else print '{"errors": {"ERREUR": "Il faut remplir tous les champs!!"} }';
			
		}
		else print '{"errors": {"ERREUR": "ID module non trouvé!!"} }';
	}
	if(isset($_GET['new']) && $_GET['new']=="prof" )
	{     
		
		if( !empty($_POST['prof_nom']) && !empty($_POST['prof_prenom']) && !empty($_POST['prof_grade']) && !empty($_POST['prof_email'] ) )
		{
			if (!filter_var($_POST['prof_email'], FILTER_VALIDATE_EMAIL)) 
			{
				print '{"errors": {"Email": "Non valide!"} }'; die();
			}
            $_POST['prof_nom']=safe_input($_POST['prof_nom'],$bdd);
            $_POST['prof_prenom']=safe_input($_POST['prof_prenom'],$bdd);
            $_POST['prof_grade']=safe_input($_POST['prof_grade'],$bdd);

            $sql="SHOW TABLE STATUS LIKE 'enseignant'";
			$res = $bdd->query($sql);
			if($res == true)
				{
					if($stat = $res->fetch_assoc())
					{
						$newID=$stat['Auto_increment'];
						if(!empty($_GET['v']) && $_GET['v']==1)
						$sql='INSERT INTO `enseignant`(`enseignantID`, `nom`, `prenom`, `email`, `grade`, `vacataire`, `annee`) VALUES ('.$newID.', "'.($_POST['prof_nom']).'","'.($_POST['prof_prenom']).'","'.$_POST['prof_email'].'","'.($_POST['prof_grade']).'",1,'.$curYear.')';
						else
                        {
                            if(empty($_POST['prof_dept'])){ print '{"errors": {"ERREUR": "Problème dans les données1!!"} }';die();}

                            $sql='INSERT INTO `enseignant`(`enseignantID`, `nom`, `prenom`, `email`, `grade`, `departementID`, `annee`) VALUES ('.$newID.', "'.($_POST['prof_nom']).'","'.($_POST['prof_prenom']).'","'.$_POST['prof_email'].'","'.($_POST['prof_grade']).'","'.($_POST['prof_dept']).'",'.$curYear.')';

                        }

                        if($bdd->query($sql) == TRUE){
                            print '{"id": '.$newID.'}';
                            $sql='INSERT INTO `enseignant_actif`(`enseignant`, `annee`, `grade`) VALUES ('.$newID.','.$curYear.','.$_POST['prof_grade'].')';
                            $bdd->query($sql);
                        }
                        else
                            print '{"errors": {"Requete": "probleme dans la requete sql!"} }';


					}
				}
			}
			else print '{"errors": {"ERREUR": "Problème dans les données!!"} }';
			
	}
}
    //new shouhait
    if(isset($_GET['new']) && $_GET['new']=="fiche_elem_mod" && !empty($_GET['idE']) && !empty($_GET['t']) && !empty($_GET['fiche']) )
    {
        $sql="SELECT * FROM `fiche_souhait` WHERE `id`=".$_GET['fiche'];

        $res = $bdd->query($sql);
        if($res==true && $res->num_rows ==0)
        {print '{"errors": "Fiche inexistante!" }'; die();}
        elseif ($row = $res->fetch_assoc())
        {
            //ToDo: cas ou le prof a validé la fiche: permetre la modification ou pas?
            if($row['active']==0)
            {print '{"errors": "La période de pre-affection est teminée!" }'; die();}
        }else {print '{"errors": "erreur interne!" }'; die();}

        if($_GET['t']!="cours" && $_GET['t']!="TD" && $_GET['t']!="TP")
        {
            print '{"errors": "erreur interne!" }'; die();
        }

        $grp_t=get_groups_mod($_GET['idE'],$_GET['t'],$bdd);
        if( !empty($_GET['prof']) && !empty($_GET['what'])  )
        {
            $sql="SELECT `id`,`groups` FROM `fiche_souhait_details` WHERE `element_Module_DetailsID`=".$_GET['idE']." AND `nature`=\"".$_GET['t']."\" AND `enseignantID`=".$_GET['prof'];

            $res = $bdd->query($sql);
            if($res==true && $res->num_rows >0)
            {
                $row = $res->fetch_assoc();
                $grp=$grp_t;
      //          echo $grp;

                if($_GET['what']=='add')
                {
                    if($grp==$row['groups']){print '{"errors": "Impossible d\'augmenter" }'; die();}
                    else $grp=$row['groups']+1;
                }
                elseif ($_GET['what']=='drop')
                {
                    if($row['groups']==0){print '{"errors":"Impossible de diminuer" }'; die();}
                    else $grp=$row['groups']-1;
                }
                else {print '{"errors": "probleme dans la requete Get" }'; die();}
                $sql="UPDATE `fiche_souhait_details` SET `groups`=".$grp." WHERE `id`=".$row['id'];

                $res = $bdd->query($sql);
                if($res==true) {print '{"success": {"choosed":"'.$grp.'","total":"'.$grp_t.'"} }';die(); }
                else
                {print '{"errors":  "probleme dans la requete sql (update)!" }'; die();}
            }
            else
            {
                if ($_GET['what']=='drop')
                {
                    print '{"errors":  "Imposible de diminuer!" }'; die();
                }
                if ($_GET['what']=='add')
                {
                    $sql="SHOW TABLE STATUS LIKE 'fiche_souhait_details'";
                    $res = $bdd->query($sql);
                    if($res == true)
                    {
                        if($stat = $res->fetch_assoc())
                        {
                            $newID=$stat['Auto_increment'];

                            $sql='INSERT INTO `fiche_souhait_details`(`id`, `fiche`, `enseignantID`, `element_Module_DetailsID`, `nature`, `groups`) VALUES ("'.$newID.'","'.$_GET['fiche'].'","'.$_GET['prof'].'","'.$_GET['idE'].'","'.$_GET['t'].'","1")';

                            if($bdd->query($sql) == TRUE) {print '{"success": {"choosed":"1","total":"'.$grp_t.'"} }';die(); }
                            else
                                print '{"errors": "probleme dans la requete sql!"} }';

                        }
                    }
                }
            }

        }
            print '{"errors":  "erreur interne!" }';

    }

if($admin || $college)
{

	//new affectation
    if($admin)
	if(isset($_GET['new']) && $_GET['new']=="affect_elem_mod" && isset($_GET['idE']) && isset($_GET['t']) )
	{     
		if($_GET['t']!="cours" && $_GET['t']!="TD" && $_GET['t']!="TP")
		{
			print '{"errors": {"Errurs": "erreur interne!"} }'; die();
		}
		if( isset($_POST['new_aff_prof']) && isset($_POST['new_aff_grp']) )
		{
			$sql="SELECT `affectationID`,`groups` FROM `affectation` WHERE `element_Module_DetailsID`=".$_GET['idE']." AND `nature`='".$_GET['t']."' AND `enseignantID`=".$_POST['new_aff_prof'];
			$res = $bdd->query($sql);

			if($res==true && $res->num_rows >0)
			{
				$row = $res->fetch_assoc();
				$sql="UPDATE `affectation` SET `groups`=".((int)$_POST['new_aff_grp']+(int)$row['groups'])." WHERE `affectationID`=".$row['affectationID'];
               //     print '{"errors": {"Errurs": '.$sql.'} }'; die();
				$res = $bdd->query($sql);
				if($res==true) print '{"id": '.$row['affectationID'].'}';
				else
				{print '{"errors": {"Requete": "probleme dans la requete sql (update)!"} }'; die();}
			}
			else
			{
			$sql="SHOW TABLE STATUS LIKE 'affectation'";
			$res = $bdd->query($sql);
			if($res == true)
				{
					if($stat = $res->fetch_assoc())
					{
						$newID=$stat['Auto_increment'];
						
						$sql='INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `affectationID`, `groups`) VALUES ("'.$_POST['new_aff_prof'].'","'.$_GET['idE'].'","'.$curYear.'","'.$_GET['t'].'","'.$newID.'","'.$_POST['new_aff_grp'].'")';
						
						if($bdd->query($sql) == TRUE) print '{"id": '.$newID.'}';
						else
						print '{"errors": {"Requete": "probleme dans la requete sql!"} }';
						
						
					}
				}
			}
			
		}else print '{"errors": {"ERREUR": "Problème dans les données!!"} }';
			
	}

    //new affectation
    if($admin)
        if(isset($_GET['new']) && $_GET['new']=="partage" ) {

            if (!empty($_POST['new_partage_prof']) && !empty($_GET['aff_id'])) {

                $a=new affectation($bdd);
                $a->getFromId($_GET['aff_id']);
                if($a->ajouter_partage($_POST['new_partage_prof']))
                print '{"id": '.$a->affectationID.'}';
            //    $a->supprimer_partage($_POST['new_partage_prof']);

            }else print '{"errors": {"ERREUR": "Problème dans les données!!"} }';
        }

    // new elemDetails
	if(isset($_GET['new']) && $_GET['new']=="modD" && isset($_GET['mod']) && isset($_GET['periode']))
	{     
		
		if( isset($_POST['grp_td']) && isset($_POST['grp_tp']) && isset($_POST['grp_cours']) )
		{
			$options = array(
				'options' => array(
                      'min_range' => 0
                      )
			);

            $options1 = array(
				'options' => array(
                      'min_range' => 1,
					  'max_range' => 2,
                      )
			);
			$err=0;
            if (filter_var($_POST['grp_cours'], FILTER_VALIDATE_INT, $options)===FALSE)
            {
                if($err==0) {print '{"errors": {'; $err=1;}
                print '"Cours": "Nombre Invalide!"';
            }
			if (filter_var($_POST['grp_td'], FILTER_VALIDATE_INT, $options)===FALSE)
			{
				if($err==0) {print '{"errors": {'; $err=1;}
				print '"TD": "Nombre Invalide!"';
			}
			if (filter_var($_POST['grp_tp'], FILTER_VALIDATE_INT, $options)===FALSE)
			{
				if($err==0) {print '{"errors": {'; $err=1;} else print ', ';
				print '"TP": "Nombre Invalide!"';
			}
			if (filter_var($_GET['mod'], FILTER_VALIDATE_INT, $options)===FALSE)
			{
				if($err==0) {print '{"errors" {:'; $err=1;} else print ', ';
				print '"Err Interne": "Module Invalide!"';
			}
			if (filter_var($_GET['periode'], FILTER_VALIDATE_INT, $options1)===FALSE)
			{
				if($err==0) {print '{"errors": {'; $err=1;} else print ', ';
				print '"Err Interne": "periode Invalide!"';
			}
			if($err==1) { print '}}';  die();}
			else{
			//	$curYear=get_cur_year("id",$bdd);
				if(is_module_instancied($_GET['mod'],$_GET['periode'],$curYear,$bdd)) {print '{"errors": {"Module": "Module déjà instancié!"} }'; die();}
				if(!is_module_actif($_GET['mod'],$curYear,$bdd)) {print '{"errors": {"Module": "Ce module est inactif!"} }'; die();}

						$sql="SHOW TABLE STATUS LIKE 'module_details'";
						$res = $bdd->query($sql);
						
						if($res == TRUE)
						{
							$stat = $res->fetch_assoc();
							
							$sql='SELECT `element_ModuleID`, (select `actif` from `element_module_actif` where element_module_actif.`element_module`=element_module.`element_moduleID` AND `annee`='.$curYear.') AS actif  FROM `element_module` WHERE `moduleID`='.$_GET['mod'].' HAVING actif=1';
							$elem = $bdd->query($sql);
							if($elem == FALSE) {print '{"errors": {"Requete": "Problèmes lors de la récupéation des elemnts!"} }'; die();}
							
							if($elem->num_rows ==0) {print '{"errors": {"ELEMENTS": "Ce module ne contient pas des elements actif!"} }'; die();}
							
							$sql="INSERT INTO `module_details`(`module_DetailsID`, `moduleID`, `periode`, `grp_cours`, `grp_td`, `grp_tp`, `annee_UniversitaireID` ) VALUES (".$stat['Auto_increment'].",".$_GET['mod'].",".$_GET['periode'].",".$_POST['grp_cours'].",".$_POST['grp_td'].",".$_POST['grp_tp'].",".$curYear.")";
							$res= $bdd->query($sql);
					
							if($res == TRUE)
							{
								$i=0;
								$a=0;
								while($row = $elem->fetch_assoc())
								{
									$sql="INSERT INTO `element_module_details`(`module_DetailsID`, `element_ModuleID` , `grp_cours`, `grp_td`, `grp_tp` ) VALUES (".$stat['Auto_increment'].",".$row['element_ModuleID'].",".$_POST['grp_cours'].",".$_POST['grp_td'].",".$_POST['grp_tp'].")";
									$res=$bdd->query($sql);
									if($res == FALSE) $i++;
									else $a++;
									
								}
								if($i!=0) {print '{"errors": {"Requete": "Problème lors de l\'insertion de '.$i.' elements!"} }'; die();}
							}else {print '{"errors": {"Requete": "Problème lors de l\'insertion!"} }'; die();}
							print '{"id": '.$stat['Auto_increment'].', "nb": '.$a.'}';
						}else {print '{"errors": {"Base de données": "Problèmes dans la table <module_details>!"} }'; die();}
				}
		}else print '{"errors": {"ERREUR": "Problème dans les données!!"} }';
			
	}
 /*
	if(isset($_GET['id']) && isset($_GET['type']) && isset($_GET['op']) )
	{
		if($_GET['op']=='supp')
		{
			if($_GET['type']=='enseignant')
			{
				$sql="DELETE FROM `enseignant` WHERE `enseignant`.`enseignantID` = ".$_GET['id'];
			}
			if($_GET['type']=='departement')
			{
				$sql="DELETE FROM `departement` WHERE `departement`.`departementID` = ".$_GET['id'];
			}
			if($_GET['type']=='cycle')
			{
				$sql="DELETE FROM `cycle` WHERE `cycle`.`cycleID` = ".$_GET['id'];
			}
			if($_GET['type']=='filiere')
			{
				$sql="DELETE FROM `filiere` WHERE `filiere`.`filiereID` = ".$_GET['id'];
			}
			//echo $sql;
			//die();
			$bdd->query($sql);
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			
		}
	}
 */
	//else header('Location: ' . $_SERVER['HTTP_REFERER']);
	
	if(isset($_POST['type']) && isset($_POST['op']))
	{
		// Nouvelle Annee +(modification..)
        if($admin)
		if($_POST['type']=='annee')
		{
			if(!empty($_POST['annee']))
			{
                $_POST['annee']=safe_input($_POST['annee'],$bdd);
				if($_POST['op']=='ajouter')
				{
                    $sql="SHOW TABLE STATUS LIKE 'annee_universitaire'";
                    $res = $bdd->query($sql);
                    if($res == true)
                    {
                        if($stat = $res->fetch_assoc())
                        {
                            $newID=$stat['Auto_increment'];
                            $sql="SELECT max(`annee_UniversitaireID`) As an_prec FROM `annee_universitaire` ";
                            $res = $bdd->query($sql);
                            if($res == true && $row = $res->fetch_assoc())
                            {
                                $sql="INSERT INTO `annee_universitaire` (`annee_univ`, `annee_UniversitaireID`) VALUES ('".$_POST['annee']."', ".$newID.")";
                                if($bdd->query($sql) && !empty($row['an_prec'])) //
                                {
                                    $sql="INSERT INTO `cycle_actif`(`cycle`, `annee`, `actif`) SELECT `cycle`, $newID, `actif` FROM `cycle_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `departement_actif`(`departement`, `annee`, `actif`) SELECT `departement`, $newID, `actif` FROM `departement_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `element_module_actif`(`element_module`, `annee`, `actif`) SELECT `element_module`, $newID, `actif` FROM `element_module_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `filiere_actif`(`filiere`, `annee`, `actif`) SELECT `filiere`, $newID, `actif` FROM `filiere_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `module_actif`(`module`, `annee`, `actif`) SELECT `module`, $newID, `actif` FROM `module_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `grade_actif`(`grade`, `annee`, `chargeHrs`, `actif`) SELECT `grade`, $newID, `chargeHrs`, `actif` FROM `grade_actif` WHERE `annee`=".$row['an_prec'].";";
                                    $sql=$sql."INSERT INTO `enseignant_actif`(`enseignant`, `grade`, `annee`, `actif`) SELECT `enseignant`, `grade`, $newID, `actif` FROM `enseignant_actif` WHERE `annee`=".$row['an_prec'];
                                    $bdd->multi_query($sql);
                             //       echo $sql;

                                }
                                //todo recopier les activités

                            }

                        }
                    }

				}
                //todo implemnter la modification
		/*		if($_POST['op']=='modifier' && isset($_POST['id']) )
					$sql="UPDATE `annee_universitaire` SET `annee_univ` = '".$_POST['annee']."'  WHERE `annee_UniversitaireID` =".$_POST['id'].";";
			*/

				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
		}
     /*
		if($_POST['type']=='enseignant')
		{
			if(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['dept']) && isset($_POST['grade']))
			{
				if($_POST['op']=='ajouter')
				{
					$sql="INSERT INTO `enseignant` (`nom` ,`prenom` ,`email` ,`grade` ,`departementID`) VALUES ('".$_POST['nom']."','".$_POST['prenom']."','".$_POST['email']."','".$_POST['dept']."','".$_POST['grade']."')";
				}
				if($_POST['op']=='modifier' && isset($_POST['id']) )
					$sql="UPDATE `enseignant` SET `nom` = '".$_POST['nom']."', `prenom` = '".$_POST['prenom']."', `email` = '".$_POST['email']."', `grade` = '".$_POST['grade']."',`departementID`='".$_POST['dept']."'  WHERE `enseignant`.`enseignantID` =".$_POST['id'].";";

				$bdd->query($sql);
				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
		}
     */
		if($_POST['type']=='departement')
		{
			if(isset($_POST['designation']) ) //&& isset($_POST['enseignant'])
			{
				if($_POST['op']=='ajouter')
				{
                    $_POST['designation']=safe_input($_POST['designation'],$bdd);
                    $sql="SHOW TABLE STATUS LIKE 'departement'";
                    $res = $bdd->query($sql);
                    if($res == true)
                    {
                        if($stat = $res->fetch_assoc())
                        {
                            $newID=$stat['Auto_increment'];
                            $sql="INSERT INTO `departement` (`designation`,`departementID`, `annee` ) VALUES ('".$_POST['designation']."', ".$newID.", ".$curYear.")";
                            if(!$bdd->query($sql))
                                echo 'Problèmes..';

                            $sql='INSERT INTO `departement_actif`(`departement`, `annee`) VALUES ('.$newID.','.$curYear.')';
                            $bdd->query($sql);
                        }
                    }
				}

		//		if($_POST['op']=='modifier' && isset($_POST['id']))
		//			$sql="UPDATE `departement` SET `designation` = '".$_POST['designation']."', `chef_enseignantID` = '".$_POST['enseignant']."' WHERE `departement`.`departementID` =".$_POST['id'].";";
			

				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}

		}
		if($_POST['type']=='cycle')
		{
			if(isset($_POST['designation']) && isset($_POST['nb']) )
			{
				if($_POST['op']=='ajouter')
				{
                    $_POST['designation']=safe_input($_POST['designation'],$bdd);
                    $_POST['nb']=safe_input($_POST['nb'],$bdd);

                    $sql="SHOW TABLE STATUS LIKE 'cycle'";
                    $res = $bdd->query($sql);
                    if($res == true)
                    {
                        if($stat = $res->fetch_assoc())
                        {
                            $newID=$stat['Auto_increment'];
                            $sql="INSERT INTO `cycle` (`designation` ,`nb_semestres`,`cycleID`, `annee`) VALUES ('".$_POST['designation']."','".$_POST['nb']."', ".$newID.", ".$curYear.")";
                            $bdd->query($sql);

                            $sql='INSERT INTO `cycle_actif`(`cycle`, `annee`) VALUES ('.$newID.','.$curYear.')';
                            $bdd->query($sql);
                        }
                    }

				}
			/*	if($_POST['op']=='modifier' && isset($_POST['id']))
					$sql="UPDATE `cycle` SET `designation` = '".$_POST['designation']."', `nb_semestres` = '".$_POST['nb']."' WHERE `cycle`.`cycleID` =".$_POST['id'].";";				
			*/

				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['type']=='filiere')
		{
			if(!empty($_POST['designation']) && !empty($_POST['cycle']) )
			{
				if($_POST['op']=='ajouter')
				{
                    $_POST['designation']=safe_input($_POST['designation'],$bdd);
                    $_POST['cycle']=safe_input($_POST['cycle'],$bdd);
             //       $_POST['dept']=safe_input($_POST['dept'],$bdd);


                    $sql="SHOW TABLE STATUS LIKE 'filiere'";
                    $res = $bdd->query($sql);
                    if($res == true)
                    {
                        if($stat = $res->fetch_assoc())
                        {
                            $newID=$stat['Auto_increment'];
                            if(!empty($_POST['dept']))
                            $sql="INSERT INTO `filiere` (`filiereID`,`designation` ,`cycleID` ,`departementID`, `annee`) VALUES (".$newID.",'".$_POST['designation']."','".$_POST['cycle']."','".safe_input($_POST['dept'],$bdd)."', ".$curYear.")";
                            else
                            $sql="INSERT INTO `filiere` (`filiereID`,`designation` ,`cycleID`, `annee` ) VALUES (".$newID.",'".$_POST['designation']."','".$_POST['cycle']."', ".$curYear.")";

                            $bdd->query($sql);
                     //       echo $$sql;
                            $sql='INSERT INTO `filiere_actif`(`filiere`, `annee`) VALUES ('.$newID.','.$curYear.')';
                            $bdd->query($sql);
                        }
                    }

                }
		/*		if($_POST['op']=='modifier' && isset($_POST['id']))
					$sql="UPDATE `filiere` SET `designation` = '".$_POST['designation']."', `cycleID` = '".$_POST['cycle']."', `departementID` = '".$_POST['dept']."' WHERE `filiere`.`filiereID` =".$_POST['id'].";";				
		*/
//die();

			}
            header('Location: ' . $_SERVER['HTTP_REFERER']);
		}

	}
    //todo A verifier..+supp
	/*
	if(isset($_POST['wizard']))
	{
        die();
		//	$myFile = "testFile.txt";
		//	$fh = fopen($myFile, 'w') or die("can't open file");
			
			if(!empty($_POST['periode']) && !empty($_POST['module']) && !empty($_POST['nbr']) )
			{
                if (!filter_var($_POST['module'], FILTER_VALIDATE_INT, $int)
                    || !filter_var($_POST['nbr'], FILTER_VALIDATE_INT, $int)
                    || !filter_var($_POST['periode'], FILTER_VALIDATE_INT, $int) )
                    die();

                $sql='SELECT COUNT(*) AS NB_ELEM FROM `element_module` WHERE `element_module`.`moduleID`="'.$_POST['module'].'"';
				$res = $bdd->query($sql);
				
				if($res == TRUE)
				{
					$row = $res->fetch_assoc();
					$NB_ELEM = $row['NB_ELEM'];
					$err=0;
					
					for($i=1;$i<=$NB_ELEM;$i++)
					{
						$GTD='GTD'.$i;
						$GTP='GTP'.$i;
						$ELEM='el_mod'.$i;
						if(!isset($_POST[$GTD]) || !isset($_POST[$GTP])) { $err=3; break;}
					}
					if(!$err)
					{
						$sql="SHOW TABLE STATUS LIKE 'module_details'";
						$res = $bdd->query($sql);
						
						if($res)
						{
							
							$stat = $res->fetch_assoc();
							
							$sql="INSERT INTO `module_details`(`module_DetailsID`, `moduleID`, `nb_etudiants`, `periode`,  `annee_UniversitaireID` ) VALUES (".$stat['Auto_increment'].",".$_POST['module'].",".$_POST['nbr'].",".$_POST['periode'].",".$curYear.")";
					//		$stringData = "\n=".$sql." *\n";
					//		fwrite($fh, $stringData);
							$res= $bdd->query($sql);
							if($res == TRUE)
							{
								for($i=1;$i<=$NB_ELEM;$i++)
								{
									$GTD='GTD'.$i;
									$GTP='GTP'.$i;
									$ELEM='el_mod'.$i;
									$sql="INSERT INTO `element_module_details`(`module_DetailsID`, `element_ModuleID` , `grp_td`, `grp_tp` ) VALUES (".$stat['Auto_increment'].",".$_POST[$ELEM].",".$_POST[$GTD].",".$_POST[$GTP].")";
						//			$stringData = "\nsql=".$sql." \n";
						//			fwrite($fh, $stringData);
									$bdd->query($sql);
								}
							}else $err=5;
						}else $err=4;
					}
				}else $err=2;
			}else $err=1;
			
			
		//	$stringData = "\nerreur=".$err;
		//	fwrite($fh, $stringData);

		//	fclose($fh);
			
	}
    */
	//wizard affectation
    if($admin)
	if(isset($_POST['wizard_affect']))
	{
		if(isset($_POST['periode']) && isset($_POST['module']) && isset($_POST['enseignant']) && isset($_POST['i']) )
		{
		$err=0;
			for($i=1;$i<=$_POST['i'];$i++)
			{
				if(!isset($_POST['elem'.$i]) || !isset($_POST['elem'.$i.'_cours']) || !isset($_POST['elem'.$i.'_TD']) || !isset($_POST['elem'.$i.'_TP']) )
				{ $err=1;break;}
			}
			if($err) return false;
			else
			{
				for($i=1;$i<=$_POST['i'];$i++)
				{
					if(!empty($_POST['elem'.$i]) )
					{ 	
						$sql="SELECT *,(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=1) AS cours,
					(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=1) AS TD,
					(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=1) AS TP FROM `element_module_details` LEFT JOIN `element_module` ON `element_module`.`element_ModuleID`=`element_module_details`.`element_ModuleID`  WHERE `element_Module_DetailsID`=".$_POST['elem'.$i]."
					";
					
						$res= $bdd->query($sql);
						if($res == TRUE)
						{
							$row = $res->fetch_assoc();
							
							if(!empty($_POST['elem'.$i.'_cours']))
							{
							
									if(($row['cours']+$_POST['elem'.$i.'_cours']) <= $row['grp_cours'])//$row['cours']==0 && $_POST['elem'.$i.'_cours']==1)
									{
										$sql='INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`) VALUES ("'.$_POST['enseignant'].'","'.$_POST['elem'.$i].'","'.$curYear.'","cours","'.$_POST['elem'.$i.'_cours'].'")';
										$res1= $bdd->query($sql);
										if(!$res1) echo 'probleme lors de l\'affectation du cours';
								
									}else echo '<div class="alert alert-error">problemes dans les valeurs du cours(overflow)..</div>';
							
							}
							if(!empty($_POST['elem'.$i.'_TD']))
							{
							
									if(($row['TD']+$_POST['elem'.$i.'_TD']) <= $row['grp_td'])
									{
										$sql='SELECT * FROM `affectation` WHERE `enseignantID`='.$_POST['enseignant'].' AND `element_Module_DetailsID`='.$_POST['elem'.$i].' AND `annee_UniversitaireID`='.$curYear.' AND `nature`="TD"';
										$res1= $bdd->query($sql);
										if($res1 === TRUE)
										{
											$row1=$res1->fetch_assoc();
											$new=$_POST['elem'.$i.'_TD']+$row1['groups'];
											$sql='UPDATE `affectation` SET `groups`='.$new.' WHERE `affectationID`='.$row1['affectationID'];
										}
										else
										$sql='INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`) VALUES ("'.$_POST['enseignant'].'","'.$_POST['elem'.$i].'","'.$curYear.'","TD","'.$_POST['elem'.$i.'_TD'].'")';
										echo $sql;
										$res2= $bdd->query($sql);
										if(!$res2) echo 'probleme lors de l\'affectation du TD';
								
									}else echo '<div class="alert alert-error">problemes dans la valeurs du TD(overflow)..</div>';
							
							}
							if(!empty($_POST['elem'.$i.'_TP']))
							{
							//	echo $row['TP'].' '.$_POST['elem'.$i.'_TP'].' '.$row['grp_tp'].'lkkkk ';
									if(($row['TP']+$_POST['elem'.$i.'_TP']) <= $row['grp_tp'])
									{
										$sql='SELECT * FROM `affectation` WHERE `enseignantID`='.$_POST['enseignant'].' AND `element_Module_DetailsID`='.$_POST['elem'.$i].' AND `annee_UniversitaireID`='.$curYear.' AND `nature`="TP"';
										$res1= $bdd->query($sql);
										if($res1 === TRUE)
										{
											$row1=$res1->fetch_assoc();
											$new=$_POST['elem'.$i.'_TP']+$row1['groups'];
											$sql='UPDATE `affectation` SET `groups`='.$new.' WHERE `affectationID`='.$row1['affectationID'];
										}
										else
										$sql='INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`) VALUES ("'.$_POST['enseignant'].'","'.$_POST['elem'.$i].'","'.$curYear.'","TP","'.$_POST['elem'.$i.'_TP'].'")';
										
										$res2= $bdd->query($sql);
										if(!$res2) echo '<div class="alert alert-error">probleme lors de l\'affectation du TP</div>';
								
									}else echo '<div class="alert alert-error">problemes dans la valeur du TP (overflow)..</div>';
							
							}
						}
					
					}
				}
			}
		}else return false;
	} //fin wizard
	// wizard
    if($admin)
	if(isset($_POST['wizard_mod_ajout']))
	{
	
		if(isset($_POST['mod_designation']) && isset($_POST['mod_code']) && isset($_POST['mod_filiere']) && isset($_POST['mod_sem']) && isset($_POST['i']) )
		{
		$err=0;

            if (filter_var($_POST['mod_filiere'], FILTER_VALIDATE_INT, $int)===FALSE
                || filter_var($_POST['mod_sem'], FILTER_VALIDATE_INT, $int)===FALSE
                || filter_var($_POST['i'], FILTER_VALIDATE_INT, $int)===FALSE )
             //   return false;
            {
                echo json_encode(array('success' => false, 'mssg'=>'Nombre(s) invalide!'));
                die();
            }

            $_POST['mod_designation']=safe_input($_POST['mod_designation'],$bdd);
            $_POST['mod_code']=safe_input($_POST['mod_code'],$bdd);
            $int = array('options' => array('min_range' => 0));
			for($i=1;$i<=$_POST['i'];$i++)
			{
				if(empty($_POST['elem'.$i.'_designation'])
                    || filter_var($_POST['elem'.$i.'_cours'], FILTER_VALIDATE_INT, $int)===FALSE// !isset()
                    || filter_var($_POST['elem'.$i.'_td'], FILTER_VALIDATE_INT, $int)===FALSE//!isset($_POST['elem'.$i.'_td'])
                    || filter_var($_POST['elem'.$i.'_tp'], FILTER_VALIDATE_INT, $int)===FALSE//!isset($_POST['elem'.$i.'_tp'])
                    || filter_var($_POST['elem'.$i.'_dept'], FILTER_VALIDATE_INT, $int)===FALSE//!isset($_POST['elem'.$i.'_dept'])
                )
				{ ; $err=1 ;break;}
			}
			if($err === 1) {
                echo json_encode(array('success' => false));
                die();//return false;
            }
			else
			{
				$sql="SHOW TABLE STATUS LIKE 'module'";
				$res = $bdd->query($sql);
				if($res== TRUE)
				{
					$stat = $res->fetch_assoc();
					
					$sql= 'INSERT INTO `module`(`moduleID`, `code`, `designation`, `semestre`, `filiereID`, `annee`) VALUES ('.$stat['Auto_increment'].',"'.$_POST['mod_code'].'","'.$_POST['mod_designation'].'","'.$_POST['mod_sem'].'","'.$_POST['mod_filiere'].'", '.$curYear.')';
					$res=$bdd->query($sql);
					if($res==TRUE)
                    {
                        $sql='INSERT INTO `module_actif`(`module`, `annee`) VALUES ('.$stat['Auto_increment'].','.$curYear.')';
                        $bdd->query($sql);

                        for($i=1;$i<=$_POST['i'];$i++)
                        {
                            $sql="SHOW TABLE STATUS LIKE 'element_module'";
                            $res = $bdd->query($sql);
                            if($res== TRUE)
                            {
                                $EM_stat = $res->fetch_assoc();
                                $sql='INSERT INTO `element_module`(`element_ModuleID`,`code`, `designation`, `heures_cours`, `heures_td`, `heures_tp`, `departementID`, `moduleID`, `annee`) VALUES ('.$EM_stat['Auto_increment'].',"'.$_POST['mod_code'].'-'.$i.'","'.$_POST['elem'.$i.'_designation'].'","'.$_POST['elem'.$i.'_cours'].'","'.$_POST['elem'.$i.'_td'].'","'.$_POST['elem'.$i.'_tp'].'","'.$_POST['elem'.$i.'_dept'].'","'.$stat['Auto_increment'].'", '.$curYear.')';

                                $res=$bdd->query($sql);
                                if($res==false) echo 'error inserting elem '.$i;
                                else{
                                    $sql='INSERT INTO `element_module_actif`(`element_module`, `annee`) VALUES ('.$EM_stat['Auto_increment'].','.$curYear.')';
                                    $bdd->query($sql);
                                }
                            }

                        }
                        echo json_encode(array('success' => true));
                    }

				}
			}
		
		}else echo json_encode(array('success' => false));
		
	}
}

}
//echo get_cur_year("id",$bdd);
}

?>