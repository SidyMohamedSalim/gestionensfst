<?php
/**
 * User: DC
 * Date: 20/11/13
*/

    define("_VALID_PHP", true);

    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active='4.1';
    $script = array("datatable", "editable", "scripts", "switch" );
    include('include/header.php');
    /* ToDo: definir une methode isAdmin est l'inclure dans le test de login
          ToDo: redirection vers une page d'erreur avec message...

       */

    if($access != _ADMIN) header('location:index.php');


    $sql = 'SELECT * FROM `fiche_souhait` WHERE `annee_universitaire`='.get_cur_year("id",$bdd);
    $res = $bdd->query($sql);

    $valid=0;
    $fiche_active=-1;

    if($res==true && $res->num_rows ==1)
    {
        if($fiche=$res->fetch_assoc())
        {
            $valid=$fiche['valid'];
            $fiche_active=$fiche['active'];
        }
        $res->close();
    }



    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            <li class="active">Auto-Affect</li>

        </ul>
    </div>

    <div class="well">

        <div class="well well-small" >
            <h3 style="display:inline-block;">Auto-Affectation</h3>
            <?php
            if($fiche_active!=-1)
            {
                echo '<div id="fiche" data-id="'.$fiche['id'].'" style="display:inline-block;" class=" pull-right alert alert-info">
					   	    <span>La periode de pre-affection est du <i>'.$fiche['debut'].'</i> à <i>'.$fiche['fin'].'</i>
					   	    ';
                if($fiche_active==0) echo '<b>( Terminée )</b>';
                else echo '<b>( En cours )</b>';
                echo'      </span></div>';

            }
            ?>

        </div>
        <?php
 //       if($fiche_active!=-1)
        {
            if( $valid==1)
            {

                echo '
                <div class="well well-small" >
                    <div class="alert alert-info">
                        <span>L\'affectation est validée pour cette année universitaire.. </span>
                    </div>
                </div>
                    ';
            }

        elseif($fiche_active!=-1)
        {
            echo '<div class="well well-small" >';

            if($valid==-1)
                echo '
                    <div class="alert alert-info">
                        <span>Une affectation automatique a été déjà éfectuée.. </span>
                        <br/><span>Il est possible de relancer l\'affectation, mais toutes les affectations automatiques pécédentes seront supprimées..</span>
                    </div>';

        $disable='';
        if($fiche_active==1) $disable='disabled';
        echo '

                    <div class="alert alert-info">
                        <span>Affectation Automatique: </span>
                        <form class="form-inline" method="post" action="">
                            <input type="submit" class="btn btn-info btn-block" name="auto_affecter" '.$disable.' value="Lancer l\'affectation automatique" />
                        </form>
                    </div>
                    ';
        }

        if(isset($_POST['auto_affecter']) && $valid!=1){

            $err='';
            if($fiche_active==1) $err='<span>Problème: La periode de pre-affectation n\'est pas terminée!</span><br/>';
            else
            {

                $year=get_cur_year("id",$bdd);

                $sql="DELETE FROM `affectation` WHERE `auto`=1 AND `annee_UniversitaireID`=".$year;
                if($bdd->query($sql)== FALSE) $err.="<span>Erreur interne1..</span>";

                for($periode=1;$periode<=2;$periode++)
                {

                $sql="SELECT DISTINCT EMD.*,EM.*,(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = EMD.element_Module_DetailsID AND affectation.annee_UniversitaireID=$year) AS cours,
								(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = EMD.element_Module_DetailsID AND affectation.annee_UniversitaireID=$year) AS TD,
								(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = EMD.element_Module_DetailsID AND affectation.annee_UniversitaireID=$year) AS TP FROM `element_module_details` AS EMD LEFT JOIN `fiche_souhait_details`AS FSD ON FSD.`element_Module_DetailsID`=EMD.`element_Module_DetailsID` LEFT JOIN `element_module` AS EM ON EM.`element_ModuleID`=EMD.`element_ModuleID` WHERE EMD.`module_DetailsID` in (SELECT `module_DetailsID` FROM `module_details` WHERE `periode` = $periode ) AND FSD.fiche=".$fiche['id'];

                $El_MOD_D=$bdd->query($sql);

                if($El_MOD_D==true ) // $prof->num_rows >0
                {
                    $xmi="";
                    $count=0;
                    $total=0;
                    $conflit_fiche=0;
                    $conflit_affectation=0;
                    while($Emd=$El_MOD_D->fetch_assoc())
                    {
                        //cas du cours:
                        $sql="SELECT * FROM `fiche_souhait_details` WHERE `nature`='cours' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                        $res=$bdd->query($sql);
                        if($res==true && $res->num_rows >0)
                        {
                            if($res->num_rows ==1)
                            {
                                if($choix=$res->fetch_assoc())
                                {
                                    if($choix['groups']==1)
                                    {
                                        if($Emd['cours']==1)
                                        {
                                            //todo conflicts avec pre-affectation
                                            $conflit_affectation++;
                                        }else{
                                            $total++;
                                            $xmi=1;
                                            $sql="INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`,`auto`) VALUES ('".$choix['enseignantID']."','".$choix['element_Module_DetailsID']."','".$year."','cours','".$choix['groups']."',1)";

                                            if($bdd->query($sql) == TRUE)$count++;

                                        }
                                    }
                                }
                            }else{
                                //cas de conflit
                                $conflit_fiche++;

                         //       while($choix=$res->fetch_assoc())
                                {
                                    //todo conflit

                                }
                            }
                        }

                        //cas du TD:
                        $sql="SELECT  count( * ) AS nbr, IFNULL( SUM( groups ) , 0 ) AS grps FROM `fiche_souhait_details` WHERE `nature`='TD' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                        $res=$bdd->query($sql);
                        if($res==true)
                        {
                            $stat=$res->fetch_assoc();
                            if($stat['nbr']!="0" && $stat['grps']!="0")
                            {
                                if($stat['grps']<=($Emd['grp_td']-$Emd['TD']))
                                {
                                    $sql="SELECT * FROM `fiche_souhait_details` WHERE `nature`='TD' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];

                                    $res=$bdd->query($sql);

                                    if($res==true && $res->num_rows >0)
                                    {

                                        while($choix=$res->fetch_assoc())
                                        {
                                            if($choix['groups']!="0")
                                            {
                                            $total++;
                                            $xmi=2;
                                            $sql="INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`, `auto`) VALUES ('".$choix['enseignantID']."','".$choix['element_Module_DetailsID']."','".$year."','TD','".$choix['groups']."',1)";

                                            if($bdd->query($sql) == TRUE)$count++;
                                            }
                                        }

                                    }
                                }
                                else{
                                    if($stat['grps']>($Emd['grp_td']))
                                    $conflit_fiche++;
                                    else
                                    $conflit_affectation++;
                                    //cas de conflit
         /*                         $sql="SELECT * FROM `fiche_souhait_details` WHERE `nature`='TD' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                                    $res=$bdd->query($sql);

                                    if($res==true && $res->num_rows >0)
                                    while($choix=$res->fetch_assoc())
                                    {
                                        //todo conflit
                                    }
                                */
                                }
                            }
                        }

                        //cas du TP:
                        $sql="SELECT  count( * ) AS nbr, IFNULL( SUM( groups ) , 0 ) AS grps FROM `fiche_souhait_details` WHERE `nature`='TP' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                        $res=$bdd->query($sql);
                        if($res==true)
                        {
                            $stat=$res->fetch_assoc();
                            if($stat['nbr']!="0" && $stat['grps']!="0")
                            {

                                if($stat['grps']<=($Emd['grp_tp']-$Emd['TP']))
                                {
                                    $sql="SELECT * FROM `fiche_souhait_details` WHERE `nature`='TP' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                                    $res=$bdd->query($sql);

                                    if($res==true && $res->num_rows >0)
                                    {

                                        while($choix=$res->fetch_assoc())
                                        {
                                            if($choix['groups']!="0")
                                            {
                                            $total++;
                                            $xmi=3;
                                            $sql="INSERT INTO `affectation`(`enseignantID`, `element_Module_DetailsID`, `annee_UniversitaireID`, `nature`, `groups`, `auto`) VALUES ('".$choix['enseignantID']."','".$choix['element_Module_DetailsID']."','".$year."','TP','".$choix['groups']."',1)";

                                            if($bdd->query($sql) == TRUE)$count++;
                                            }
                                        }

                                    }
                                }
                                else{
                                    //cas de conflit
                                    if($stat['grps']>($Emd['grp_tp']))
                                        $conflit_fiche++;
                                    else
                                        $conflit_affectation++;

                                    /*
                                    $sql="SELECT * FROM `fiche_souhait_details` WHERE `nature`='TP' AND `element_Module_DetailsID`=".$Emd['element_Module_DetailsID'];
                                    $res=$bdd->query($sql);

                                    if($res==true && $res->num_rows >0)
                                        while($choix=$res->fetch_assoc())
                                        {
                                            //todo conflit
                                        }
                                */
                                }
                            }
                        }

                    }


                    echo '<div class="alert alert-info" id="alert"><strong>Semestre: '.get_periode_name($periode).'<br/>AUTO AFFECTATION EFECTUEE!  <br/><span>('.$count.' affectations, '.$conflit_fiche.' conflits dans la fiche et '.$conflit_affectation.' conflits avec les affectations )</span></strong></div>';
                }else $err='Probleme interne!!';

                }//fin de for

                $sql="UPDATE `fiche_souhait` SET `valid`=-1 WHERE `id`=".$fiche['id'];
                if($bdd->query($sql)== FALSE) $err.="<br><span>Erreur interne2..</span>";

            if (!empty($err)) {
                echo '<div class="alert alert-error" id="alert"><strong>'.$err.'</strong>	</div>';
            }


        }
    }
        ?>

    </div>
<?php


        if($fiche_active==-1)
        {

                echo '
						<div class="alert alert-info">
							<p>La periode de pre-affection n\'est pas encore démarrée..</p>
							<p>Veuillez tout d\'abord la démarrer dans la page <a href="fiches.php">des fiches</a></p>

						</div>
					';

        }
        elseif($fiche_active==1){

            echo '
						<div class="alert alert-info">
							<p>La periode de pre-affection est en cours..</p>
							<p>Veuillez tout d\'abord la términer dans la page <a href="fiches.php">des fiches</a></p>

						</div>
					';
        }else{

        }
        ?>

</div>







    <?php

    include('include/scripts.php');
    include('include/footer.php');

        }
} else {
    header('location:login.php');
}

?>