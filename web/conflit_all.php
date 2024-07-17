<?php
    /**
     * User: DC
     * Date: 24/01/14
     * Time: 18:08
     *
     */

    define("_VALID_PHP", true);
    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active='2.2';
    $script = array("datatable", "editable", "select2", "scripts", "switch", "script_conflit");
    include('include/header.php');

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');
    $CurYear_des=get_cur_year("des",$bdd);
    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li><a href="#">Home</a> <span class="divider">/</span></li>
            <li class="active">Conflits</li>

        </ul>
    </div>
    <?php
    //functions get_cur_year & get_label -> fonctions.php
    $CurYid=get_cur_year("id",$bdd);

    $sql='select * from `fiche_souhait` where annee_universitaire ='.$CurYid;
    $res= $bdd->query($sql);
    $lancer=1;
    if($res==true && $res->num_rows ==1 && $fiche = $res->fetch_assoc())
    {
        $fiche_active=$fiche['active'];
        $formID=$fiche['id'];


    }else $lancer=0;
    ?>
    <div class="well">

    <div class="well well-small" >
    <h3 id="title" style="display:inline-block;">Conflits dans la fiche de souhaits</h3>
    <?php
    if($lancer==1)
    {

        echo '<div id="fiche" data-id="'.$formID.'" style="display:inline-block;" class=" pull-right alert alert-info">
					   	    <span>La periode de pre-affection est du <i>'.$fiche['debut'].'</i> à <i>'.$fiche['fin'].'</i>
					   	    ';
        if($fiche_active==0) echo '<b>( Terminée )</b>';
        else echo '<b>( En cours )</b>';
        echo'      </span></div>';


    }

    ?>
    <div class="well" >

    <?php
    // La fiche existe
    if($lancer==1)
    {


    ?>

    <div class="well well-small" >
        <h4>Semestre d'automne:</h4>
    </div> <!-- Fin well-small-->

    <div class="well well-small" >

    <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >

    <thead>
    <tr>
        <th>Enseignants</th>
        <th>Module</th>
        <th>Elements</th>
        <th>Nature</th>
        <th>Affectation</th>

    </tr>
    </thead>
    <tbody>

    <?php

  //  $sql='SELECT   MD.*, M.designation FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` WHERE MD.`annee_UniversitaireID`='.$CurYid.' AND `periode`=1';
    $sql = 'SELECT enseignant.*, (select `actif` from `enseignant_actif` where enseignant_actif.`enseignant`=enseignant.`enseignantID` AND enseignant_actif.`annee`='.$CurYid.') AS actif  FROM `enseignant` WHERE enseignant.vacataire=0 HAVING actif=1';

  //  $prof = $bdd->query($sql);


    $res = $bdd->query($sql);

    if($res == TRUE)
        while ($row = $res->fetch_assoc())
        {


            $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`=1 AND `annee_UniversitaireID`='.$CurYid;
            $res1 = $bdd->query($sql);
            if($res1 == TRUE)
            {
                //check whether there is a conflict
                while($row1 = $res1->fetch_assoc())
                {
                    //cas du cours
                    $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"cours\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                    $res2 = $bdd->query($sql);
                    if($res2 == TRUE && $res2->num_rows >1)
                    {
                        echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                        echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';

                        $NB=$res2->num_rows;
                        echo '<td class="no-padding3">';
                        for ($i=0;$i<$NB;$i++)
                        {
                            /* seek to i-th row */
                            $res2->data_seek($i);
                            /* fetch row */
                            $row2 = $res2->fetch_assoc();
                            echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                            if($i+1<$NB) echo '<hr>';
                        }
                        echo '</td>';

                        echo '<td class="no-padding3">';
                        for ($i=0;$i<$NB;$i++)
                        {
                            /* seek to i-th row */
                            $res2->data_seek($i);
                            /* fetch row */
                            $row2 = $res2->fetch_assoc();
                            echo '<div ><span >Cours: '.get_badge($row2['groups'],1).'</span></div>';
                            if($i+1<$NB) echo '<hr>';
                        }
                        echo '</td>';

                        //        echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                        $sql="SELECT IFNULL(SUM(affectation.groups),0) AS cours FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                        $res2 = $bdd->query($sql);
                        if($res2 == TRUE && $res2->num_rows >0)
                        {
                            $row2 = $res2->fetch_assoc();
                            echo "<td>";
                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['cours'],1).'</a></div>';
                            echo "</td>";
                        }

                        echo '</tr>';
                    }

                    //cas du TD
                    $sql="SELECT IFNULL(sum(`groups`),0)AS grp FROM `fiche_souhait_details`  WHERE `fiche`=".$formID." AND `nature`=\"TD\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                    //                   echo $sql; die();
                    $res2 = $bdd->query($sql);
                    if($res2 == TRUE && $res2->num_rows >0 && $grp=$res2->fetch_assoc())
                    {
                        if($grp['grp']>$row1['grp_td'])
                        {
                            $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof, PR.`enseignantID` FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"TD\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];
                            $res2 = $bdd->query($sql);

                            echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                            echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';
                            $NB=$res2->num_rows;
                            echo '<td class="no-padding3">';
                            for ($i=0;$i<$NB;$i++)
                            {
                                /* seek to i-th row */
                                $res2->data_seek($i);
                                /* fetch row */
                                $row2 = $res2->fetch_assoc();
                                echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                                if($i+1<$NB) echo '<hr>';
                            }
                            echo '</td>';

                            echo '<td class="no-padding3">';
                            for ($i=0;$i<$NB;$i++)
                            {
                                /* seek to i-th row */
                                $res2->data_seek($i);
                                /* fetch row */
                                $row2 = $res2->fetch_assoc();
                                echo '<div ><span >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span></div>';
                                if($i+1<$NB) echo '<hr>';
                            }
                            echo '</td>';

                            //            echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                            $sql="SELECT IFNULL(SUM(affectation.groups),0) AS TD FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0)
                            {
                                $row2 = $res2->fetch_assoc();
                                echo "<td>";
                                echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="TD" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['TD'],$row1['grp_td']).'</a></div>';
                                echo "</td>";
                            }

                            echo '</tr>';
                        }
                    }

                    //cas du TP
                    $sql="SELECT IFNULL(sum(`groups`),0)AS grp FROM `fiche_souhait_details`  WHERE `fiche`=".$formID." AND `nature`=\"TP\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                    $res2 = $bdd->query($sql);
                    if($res2 == TRUE && $res2->num_rows >0 && $grp=$res2->fetch_assoc())
                    {
                        if($grp['grp']>$row1['grp_tp'])
                        {
                            $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof, PR.`enseignantID` FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"TP\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];
                            $res2 = $bdd->query($sql);

                            echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                            echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';
                            $NB=$res2->num_rows;
                            echo '<td class="no-padding3">';
                            for ($i=0;$i<$NB;$i++)
                            {
                                /* seek to i-th row */
                                $res2->data_seek($i);
                                /* fetch row */
                                $row2 = $res2->fetch_assoc();
                                echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                                if($i+1<$NB) echo '<hr>';
                            }
                            echo '</td>';

                            echo '<td class="no-padding3">';
                            for ($i=0;$i<$NB;$i++)
                            {
                                /* seek to i-th row */
                                $res2->data_seek($i);
                                /* fetch row */
                                $row2 = $res2->fetch_assoc();
                                echo '<div ><span >TP: '.get_badge($row2['groups'],$row1['grp_tp']).'</span></div>';
                                if($i+1<$NB) echo '<hr>';
                            }
                            echo '</td>';

                            //   echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                            $sql="SELECT IFNULL(SUM(affectation.groups),0) AS TP FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0)
                            {
                                $row2 = $res2->fetch_assoc();
                                echo "<td>";
                                echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="TP" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['TP'],$row1['grp_tp']).'</a></div>';
                                echo "</td>";
                            }

                            echo '</tr>';
                        }
                    }
                }

            }


        }
    $res->close();
    ?>

    </tbody>
    </table>
    </div> <!-- Fin div automne-->
    </div> <!-- FIN DIV WELL -->

        <div class="well">
        <div class="well well-small" >
            <h4>Semestre de printemps:</h4>
        </div> <!-- Fin well-small-->

        <div class="well well-small" >

        <table id="liste2" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >

            <thead>
            <tr>
                <th>Module</th>
                <th>Elements</th>
                <th>Enseignants</th>
                <th>Nature</th>
                <th>Affectation</th>
                <?php /*  <th>Actions</th>  */ ?>
            </tr>
            </thead>
            <tbody>

            <?php

            $sql='SELECT   MD.*, M.designation FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` WHERE MD.`annee_UniversitaireID`='.$CurYid.' AND `periode`=2';
            //        if(!empty($_GET['filiere'])) $sql=$sql." AND M.filiereID=".$_GET['filiere'];
            $res = $bdd->query($sql);

            if($res == TRUE)
                while ($row = $res->fetch_assoc())
                {


                    $sql="SELECT EMD.*, EM.* FROM `element_module_details` AS EMD LEFT JOIN `element_module` AS EM ON EM.`element_ModuleID`=EMD.`element_ModuleID` WHERE EMD.`module_DetailsID`=".$row['module_DetailsID'];
                    $res1 = $bdd->query($sql);
                    if($res1 == TRUE)
                    {
                        //check whether there is a conflict
                        while($row1 = $res1->fetch_assoc())
                        {
                            //cas du cours
                            $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"cours\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >1)
                            {
                                echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                                echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';

                                $NB=$res2->num_rows;
                                echo '<td class="no-padding3">';
                                for ($i=0;$i<$NB;$i++)
                                {
                                    /* seek to i-th row */
                                    $res2->data_seek($i);
                                    /* fetch row */
                                    $row2 = $res2->fetch_assoc();
                                    echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                                    if($i+1<$NB) echo '<hr>';
                                }
                                echo '</td>';

                                echo '<td class="no-padding3">';
                                for ($i=0;$i<$NB;$i++)
                                {
                                    /* seek to i-th row */
                                    $res2->data_seek($i);
                                    /* fetch row */
                                    $row2 = $res2->fetch_assoc();
                                    echo '<div ><span >Cours: '.get_badge($row2['groups'],$row1['grp_td']).'</span></div>';
                                    if($i+1<$NB) echo '<hr>';
                                }
                                echo '</td>';

                                //   echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                                $sql="SELECT IFNULL(SUM(affectation.groups),0) AS cours FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                                $res2 = $bdd->query($sql);
                                if($res2 == TRUE && $res2->num_rows >0)
                                {
                                    $row2 = $res2->fetch_assoc();
                                    echo "<td>";
                                    echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['cours'],1).'</a></div>';
                                    echo "</td>";
                                }
                                echo '</tr>';
                            }

                            //cas du TD
                            $sql="SELECT IFNULL(sum(`groups`),0)AS grp FROM `fiche_souhait_details`  WHERE `fiche`=".$formID." AND `nature`=\"TD\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                            //                   echo $sql; die();
                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0 && $grp=$res2->fetch_assoc())
                            {
                                if($grp['grp']>$row1['grp_td'])
                                {
                                    $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof, PR.`enseignantID` FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"TD\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];
                                    $res2 = $bdd->query($sql);

                                    echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                                    echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';
                                    $NB=$res2->num_rows;
                                    echo '<td class="no-padding3">';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res2->data_seek($i);
                                        /* fetch row */
                                        $row2 = $res2->fetch_assoc();
                                        echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                                        if($i+1<$NB) echo '<hr>';
                                    }
                                    echo '</td>';

                                    echo '<td class="no-padding3">';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res2->data_seek($i);
                                        /* fetch row */
                                        $row2 = $res2->fetch_assoc();
                                        echo '<div ><span >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span></div>';
                                        if($i+1<$NB) echo '<hr>';
                                    }
                                    echo '</td>';

                                    //   echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                                    $sql="SELECT IFNULL(SUM(affectation.groups),0) AS TD FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        $row2 = $res2->fetch_assoc();
                                        echo "<td>";
                                        echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="TD" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['TD'],$row1['grp_td']).'</a></div>';
                                        echo "</td>";
                                    }
                                    echo '</tr>';
                                }
                            }

                            //cas du TP
                            $sql="SELECT IFNULL(sum(`groups`),0)AS grp FROM `fiche_souhait_details`  WHERE `fiche`=".$formID." AND `nature`=\"TP\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];

                            $res2 = $bdd->query($sql);
                            if($res2 == TRUE && $res2->num_rows >0 && $grp=$res2->fetch_assoc())
                            {
                                if($grp['grp']>$row1['grp_tp'])
                                {
                                    $sql="SELECT FD.*,CONCAT(PR.`nom`, ' ',PR.`prenom` ) AS prof, PR.`enseignantID` FROM `fiche_souhait_details` AS FD LEFT JOIN `enseignant` AS PR ON PR.`enseignantID`=FD.`enseignantID`  WHERE `fiche`=".$formID." AND `nature`=\"TP\" AND `element_Module_DetailsID`= ".$row1['element_Module_DetailsID'];
                                    $res2 = $bdd->query($sql);

                                    echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';
                                    echo '<td><span data-id="'.$row1['element_Module_DetailsID'].'">'.$row1['designation'].'</span></td>';
                                    $NB=$res2->num_rows;
                                    echo '<td class="no-padding3">';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res2->data_seek($i);
                                        /* fetch row */
                                        $row2 = $res2->fetch_assoc();
                                        echo '<div data-prof="'.$row2['enseignantID'].'"><span>'.$row2['prof'].'</span></div>'; //<span class="pull-right" >TD: '.get_badge($row2['groups'],$row1['grp_td']).'</span>
                                        if($i+1<$NB) echo '<hr>';
                                    }
                                    echo '</td>';

                                    echo '<td class="no-padding3">';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res2->data_seek($i);
                                        /* fetch row */
                                        $row2 = $res2->fetch_assoc();
                                        echo '<div ><span >TP: '.get_badge($row2['groups'],$row1['grp_tp']).'</span></div>';
                                        if($i+1<$NB) echo '<hr>';
                                    }
                                    echo '</td>';

                                    //   echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td></tr>';

                                    $sql="SELECT IFNULL(SUM(affectation.groups),0) AS TP FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = ".$row1['element_Module_DetailsID']." AND affectation.annee_UniversitaireID=".$CurYid;

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        $row2 = $res2->fetch_assoc();
                                        echo "<td>";
                                        echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="TP" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row2['TP'],$row1['grp_tp']).'</a></div>';
                                        echo "</td>";
                                    }
                                    echo '</tr>';
                                }
                            }
                        }

                    }


                }
            $res->close();
            ?>

            </tbody>
        </table>
        </div> <!-- Fin well printemp-->
        </div> <!-- Fin well-->

    <!-- Modal messages : details-->

        <div id="edit_aff" class="modal medium-modal hide" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
                <button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>
                <h3>Affectation</h3>
            </div>

            <div class="modal-body" style="height: 300px;">



            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Fermer</a>

            </div>

        </div>

        <!-- END of Modal messages : details -->



    <?php
    }else{
        echo '
						<div class="alert alert-info" style="margin-bottom: 0px;">
							<span>La periode de pre-affection n\'est pas encore démarrée..</span>
						</div>
					';
    }

    ?>


    </div> <!-- Fin well-->


    </div>







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