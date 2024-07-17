<?php

define("_VALID_PHP", true);
include_once 'include/connexion_BD.php';
include_once 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == false)
    header('location:login.php');

    // Add your protected page content here!
    $active=2;
    $script = array("datatable","editable","select2","scripts");
    include('include/header.php');

    $AUvalid=!isAUvalid($bdd);

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');
    $CurYear=get_cur_year("id",$bdd);
    $CurYear_des=get_cur_year("des",$bdd);


if( isset($_POST['annee']) )
{
    $options = array(
        'options' => array(
            'min_range' => 1
        )
    );

    if (filter_var($_POST['annee'], FILTER_VALIDATE_INT, $options)===FALSE)
    {
        echo "<div class='alert alert-danger'>problème!!</div>";
    }else
    {
        $sql="SELECT * FROM `module_details` WHERE `annee_UniversitaireID`=".$_POST['annee'];
        $res1 = $bdd->query($sql);

        while ($row1 = $res1->fetch_assoc())
        {
            //  $row1['moduleID'];
            //automne

            if(is_module_actif($row1['moduleID'],$CurYear,$bdd) && !is_module_instancied($row1['moduleID'],$row1['periode'],$CurYear,$bdd))
            {
                $sql="SHOW TABLE STATUS LIKE 'module_details'";
                $res = $bdd->query($sql);

                if($res == TRUE)
                {
                    $stat = $res->fetch_assoc();

                    $sql='SELECT `element_ModuleID`, (select `actif` from `element_module_actif` where element_module_actif.`element_module`=element_module.`element_moduleID` AND `annee`='.$CurYear.') AS actif  FROM `element_module` WHERE `moduleID`='.$row1['moduleID'].' HAVING actif=1';
                    $elem = $bdd->query($sql);

                    if($elem == TRUE && $elem->num_rows >0)
                    {
                        $sql="INSERT INTO `module_details`(`module_DetailsID`, `moduleID`, `periode`, `grp_cours`, `grp_td`, `grp_tp`, `annee_UniversitaireID` ) VALUES (".$stat['Auto_increment'].",".$row1['moduleID'].",".$row1['periode'].",".$row1['grp_cours'].",".$row1['grp_td'].",".$row1['grp_tp'].",".$CurYear.")";
                        $res= $bdd->query($sql);

                        if($res == TRUE)
                        {
                            $i=0;
                            $a=0;
                            while($row = $elem->fetch_assoc())
                            {
                                $sql="INSERT INTO `element_module_details`(`module_DetailsID`, `element_ModuleID` , `grp_cours`, `grp_td`, `grp_tp` ) VALUES (".$stat['Auto_increment'].",".$row['element_ModuleID'].",".$row1['grp_cours'].",".$row1['grp_td'].",".$row1['grp_tp'].")";
                                $res=$bdd->query($sql);
                                if($res == FALSE) $i++;
                                else $a++;

                            }

                        }

                    }
                }


            }


        }
        echo '<div class="alert alert-info"> Import terminé avec succès!</div>';
    }










}

?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">Planification (import)</li>
        </ul>
    </div>

    <!-- Table des enseignants -->

    <div class="well">
        <div class="well well-small" >
            <h3 id="title" style="display:inline-block;">Importer une ancienne planification</h3>

        </div> <!-- Fin well-small-->

        <div class="well well-small">

            <br>

            <form action="" method="post">
                <label for="annee">Choisir l'année:</label>
                <select name="annee" id="annee">
                    <?php
                    $sql = 'SELECT * FROM `annee_universitaire` WHERE annee_UniversitaireID<'.$CurYear.' ORDER BY `annee_UniversitaireID` DESC';

                    $res = $bdd->query($sql);
                    $ret = array();
                    while ($row = $res->fetch_assoc())
                        echo "<option value='".$row['annee_UniversitaireID']."'>".$row['annee_univ']."</option>";
                    ?>


                </select>
                <br>
                <input type="submit" value="Importer" class="btn" \>

            </form>

        </div>

    </div> <!-- Fin well -->




<?php

    include('include/scripts.php');
    include('include/footer.php');

?>