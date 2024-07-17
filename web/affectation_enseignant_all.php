<?php
    define("_VALID_PHP", true);

    include 'include/connexion_BD.php';
    include 'include/fonctions.php';

    sec_session_start();
    if(login_check($bdd) == true) {

        $active=3;
        $script = array("datatable","editable","scripts");

        include('include/header.php');

        if($access != _ADMIN) header('location:index.php');
        $CurYear_des=get_cur_year("des",$bdd);
        $CurYear=get_cur_year("id",$bdd);

        ?>
        <!--Body content-->
        <!-- horizontal nav -->

        <style>
            .overlay {
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                z-index:1000;

            }
        </style>
        <div>
            <ul class="breadcrumb">
                <li>
                    <a href="/index.php">Home</a> <span class="divider">/</span><a href="affectation_enseignant.php">affectation_enseignant</a> <span class="divider">/</span>
                </li>
                <li class="active">Tous</li>
            </ul>
        </div>

        <div class="">
        <div class="well well-small" >
            <h3 id="title" style="display:inline-block;">Details des Affectations</h3>
        </div> <!-- Fin well-small-->

        <div class="" >
        <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Vue par Tableau</a></li>
            <li><a href="#tab2" data-toggle="tab">Vue exhaustive</a></li>
        </ul>
        <div class="tab-content" id="tab-content">
        <div class="tab-pane active" id="tab1">

        <div class="well">
            <div class="well well-smalll">
                     <span class="pull-right">
                          <a class="toggle-vis" data-column="0"><span class="label label-info">Enseignant</span></a>
                          <a class="toggle-vis" data-column="1"><span class="label label-info">Module</span></a>
                          <a class="toggle-vis" data-column="2"><span class="label label-info">Element</span></a>
                          <a class="toggle-vis" data-column="3"><span class="label label-info">Filère</span></a>
                          <a class="toggle-vis" data-column="4"><span class="label label-info">Semestre</span></a>
                          <a class="toggle-vis" data-column="5"><span class="label label-info">Periode</span></a>
                          <a class="toggle-vis" data-column="6"><span class="label label-info">Affectations</span></a>
        </span>
            </div>
            <h4>Liste des enseignants:</h4>

            <div class="well">
                <?php

                $sql = 'SELECT enseignant.*, (select `actif` from `enseignant_actif` where enseignant_actif.`enseignant`=enseignant.`enseignantID` AND enseignant_actif.`annee`='.$CurYear.') AS actif  FROM `enseignant` WHERE enseignant.vacataire=0  HAVING actif=1 order by nom';

                $prof = $bdd->query($sql);

                echo '
						<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
						<thead>
							<tr>
								<th>Enseignant</th>
								<th>Module</th>
								<th>Element</th>
								<th>Filière</th>
								<th title="Semestre">Sem.</th>
								<th >Periode</th>
								<th>Affectations</th>
							</tr>
						</thead>

						<tbody>';


                if($prof == TRUE) // from above
                    while ($row = $prof->fetch_assoc())
                    {
                        $id=$row['enseignantID'];

                        $nom_pre=$row['nom'].' '.$row['prenom'];

                        $sql='SELECT `module_DetailsID`, `periode`, module.designation AS mod_des, module.semestre, F.designation AS filiere FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID LEFT JOIN filiere AS F ON F.filiereId=module.filiereID WHERE `annee_UniversitaireID`='.$CurYear;
                        //    echo $sql;die();
                        $res = $bdd->query($sql);
                        if($res == TRUE && $res->num_rows >0)
                            while($row = $res->fetch_assoc())
                            {

                                {

                                    // savoir les elements de module instancié..
                            //      $sql="SELECT DISTINCT `element_Module_DetailsID` FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `fiche_souhait_details` WHERE `fiche`=".$fiche['id']." AND fiche_souhait_details.`groups`!=0 AND `enseignantID`=".$id." ) ";
                                    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` LEFT JOIN `element_module` AS `EM` ON `EMD`.`element_ModuleID` = `EM`.`element_ModuleID` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE annee_UniversitaireID=$CurYear AND (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." )  ) ";

                                    //      echo $sql; die();
                                    $res1 = $bdd->query($sql);
                                    if($res1 == TRUE && $res1->num_rows >0)
                                    {

                                        echo '
                        <tr>
                                <td>'.$nom_pre.'</td>
								<td >'.$row['mod_des'].'</td>
								';


                                        $NB=$res1->num_rows;

                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>'.$row1['designation'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td >'.$row['filiere'].'</td>
                                          <td >S'.$row['semestre'].'</td>
                                          <td >'.get_periode_name($row['periode']).'</td>';
                                        echo '<td class="no-padding3">'; //cours
                                        for ($j=0;$j<$NB;$j++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($j);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();



                                            // cours
                                            $o=1;
                                            $sql="SELECT affectation.groups, affectation.nature, element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.nature=\"cours\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            //   echo $sql; die();

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {

                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //              if ($o++==1) echo '<td class="no-padding3" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                                }
                                            }

                                            //cours partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$id;

                                            // echo $sql;
                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {

                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                 //   if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                                }
                                            }

                                            //TD
                                            $sql="SELECT affectation.groups, affectation.nature,  element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //             if ($o++==1) echo '<td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                                }
                                            }
                                            //TD partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                  //  if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                                }
                                            }
                                            //TP
                                            $sql="SELECT affectation.groups, affectation.nature,  element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.nature=\"TP\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //            if ($o++==1) echo '<td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                                }
                                            }
                                            //TP partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$id;
                                            //   echo $sql;
                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                //    if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                                }
                                            }
                                            $o=0;






                                            if($j+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';

                                        echo '

							</tr>
							';

                                    }


                                }


                            }


                    }
                echo '
						</tbody>
						</table>

						';


                ?>

            </div> <!-- well -->
        </div> <!-- well -->

        <div class="well">
            <h4>Liste des vacataires:</h4>

            <div class="well">
                <?php

                $sql = 'SELECT enseignant.*, (select `actif` from `enseignant_actif` where enseignant_actif.`enseignant`=enseignant.`enseignantID` AND enseignant_actif.`annee`='.$CurYear.') AS actif  FROM `enseignant` WHERE enseignant.vacataire=1  HAVING actif=1 order by nom';

                $prof = $bdd->query($sql);

                echo '
						<table id="liste2" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
						<thead>
							<tr>
								<th>Enseignant</th>
								<th>Module</th>
								<th>Element</th>
								<th>Filière</th>
								<th title="Semestre">Sem.</th>
								<th >Periode</th>
								<th>Affectations</th>
							</tr>
						</thead>

						<tbody>';


                if($prof == TRUE) // from above
                    while ($row = $prof->fetch_assoc())
                    {
                        $id=$row['enseignantID'];

                        $nom_pre=$row['nom'].' '.$row['prenom'];

                        $sql='SELECT `module_DetailsID`, `periode`, module.designation AS mod_des, module.semestre, F.designation AS filiere FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID LEFT JOIN filiere AS F ON F.filiereId=module.filiereID WHERE  `annee_UniversitaireID`='.$CurYear;
                        //    echo $sql;die();
                        $res = $bdd->query($sql);
                        if($res == TRUE && $res->num_rows >0)
                            while($row = $res->fetch_assoc())
                            {

                                {


                                    // savoir les elements de module instancié..
                                    //      $sql="SELECT DISTINCT `element_Module_DetailsID` FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `fiche_souhait_details` WHERE `fiche`=".$fiche['id']." AND fiche_souhait_details.`groups`!=0 AND `enseignantID`=".$id." ) ";
                                    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` LEFT JOIN `element_module` AS `EM` ON `EMD`.`element_ModuleID` = `EM`.`element_ModuleID` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE annee_UniversitaireID=$CurYear AND (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." )  ) ";

                                    //      echo $sql; die();
                                    $res1 = $bdd->query($sql);
                                    if($res1 == TRUE && $res1->num_rows >0)
                                    {

                                        echo '
                        <tr>
                                <td>'.$nom_pre.'</td>
								<td >'.$row['mod_des'].'</td>
								';


                                        $NB=$res1->num_rows;

                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>'.$row1['designation'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td >'.$row['filiere'].'</td>
                                          <td >S'.$row['semestre'].'</td>
                                          <td >'.get_periode_name($row['periode']).'</td>';
                                        echo '<td class="no-padding3">'; //cours
                                        for ($j=0;$j<$NB;$j++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($j);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();



                                            // cours
                                            $o=1;
                                            $sql="SELECT affectation.groups, affectation.nature, element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.nature=\"cours\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            //   echo $sql; die();

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {

                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //              if ($o++==1) echo '<td class="no-padding3" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                                }
                                            }

                                            //cours partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$id;

                                            // echo $sql;
                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {

                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //   if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                                }
                                            }

                                            //TD
                                            $sql="SELECT affectation.groups, affectation.nature,  element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //             if ($o++==1) echo '<td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                                }
                                            }
                                            //TD partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //  if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                                }
                                            }
                                            //TP
                                            $sql="SELECT affectation.groups, affectation.nature,  element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.nature=\"TP\" AND affectation.`groups`!=0 AND affectation.`enseignantID`=".$id;

                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //            if ($o++==1) echo '<td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                                }
                                            }
                                            //TP partagé
                                            $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$id;
                                            //   echo $sql;
                                            $res2 = $bdd->query($sql);
                                            if($res2 == TRUE && $res2->num_rows >0)
                                            {
                                                if($row2 = $res2->fetch_assoc())
                                                {
                                                    //    if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].'</td><td>';
                                                    echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                                }
                                            }
                                            $o=0;






                                            if($j+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';

                                        echo '

							</tr>
							';

                                    }


                                }


                            }


                    }
                echo '
						</tbody>
						</table>

						';


                ?>

            </div> <!-- well -->

        </div> <!-- well -->
        </div> <!-- tab1 -->
        <div class="tab-pane" id="tab2">
            <a id="FS" href="#" class="btn btn-info">Imprimer</a>
            

            <div style="text-align: center;">
                <h3>UNIVERSITE SIDI MOHAMED BEN ABDELLAH</h3>
                <h3>FACULTE DES SCIENCES ET TECHNIQUES DE FES</h3>
                <h3>Service du département informatique <?php echo $CurYear_des;?></h3>
            </div>
            <?php

            $CurYear=get_cur_year("id",$bdd);
            $sql='SELECT E.*, G.* FROM `enseignant` AS E LEFT JOIN `grade` AS G ON E.grade=G.gradeID WHERE E.vacataire=0 AND E.`enseignantID` IN (select `enseignant` from `enseignant_actif` where `actif`=1 AND `annee`='.$CurYear.') order by E.nom';
            $profs = $bdd->query($sql);

            if($profs)
            {
                echo "<div id='DIV1'>";
                while ($prof = $profs->fetch_assoc())
                {

                    /*     echo '
                         <ul class="nav nav-tabs" id="myTab">
                             <li class="active"><a href="#Automne" data-toggle="tab">Automne</a></li>
                             <li><a href="#Printemps" data-toggle="tab">Printemps</a></li>
                         </ul>';
                    */
                    echo '<div class="box no-padding" style="page-break-after:always" >';

                    $msg["charge"]="";
                    $msg["automne"]="";
                    $msg["printemps"]="";
                    $msg = prof_stats($prof['enseignantID'],$bdd,0,true);//,$bdd,0,true

                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>'.$prof['nom'].' '.$prof['prenom'].'</strong>  ('.$msg["charge"].'Hrs)</div><hr>';

                    echo '<div class="box1 no-padding1" id="Automne">';
                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>Automne:</strong>   '.$msg["automne"].'</div><hr>';
                    //		$CurYear=get_cur_year("id",$bdd);
                    $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`=1 AND `annee_UniversitaireID`='.$CurYear;

                    $res = $bdd->query($sql);
                    if($res == TRUE && $res->num_rows >0)
                        while($row = $res->fetch_assoc())
                        {
                            // savoir les elements de module instancié..
                            $sql="SELECT DISTINCT `element_Module_DetailsID`,element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` WHERE `annee_UniversitaireID`=".$CurYear." AND `enseignantID`=".$prof['enseignantID']." ) ";
                            $sql="SELECT DISTINCT `element_Module_DetailsID`,element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE `annee_UniversitaireID`=".$CurYear." AND (affectation.`enseignantID`=".$prof['enseignantID']." OR affectation_partage.`enseignantID`=".$prof['enseignantID']." ) ) ";

                            //    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." ) ) ";

                            $res1 = $bdd->query($sql);
                            if($res1 == TRUE && $res1->num_rows >0)
                            {
                                echo '<div style="margin-bottom: 0px;margin-left:0px;margin-right:0px;" class="well well-small well1">Module: '.$row['mod_des'].'</div>';
                                echo '<div><table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                                while($row1 = $res1->fetch_assoc())
                                {
                                    $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
                                    // cours
                                    $i=1;
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$prof['enseignantID'];

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
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    // echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                        }
                                    }
                                    //TD
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                        }
                                    }
                                    //TD partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                        }
                                    }
                                    //TP
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                        }
                                    }
                                    //TP partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];
                                    //   echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                        }
                                    }

                                    $i=0;
                                    echo '</td></tr>';
                                }
                                echo '</table></div>';
                            }

                        }
                    echo '</div>';

                    echo '<div class="box1 no-padding1" id="Printemps">';
                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>Printemps:</strong> '.$msg["printemps"].'</div><hr>';
                    //		$cur_year=get_cur_year("id",$bdd);
                    $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`= 2 AND `annee_UniversitaireID`='.$CurYear;

                    $res = $bdd->query($sql);
                    if($res == TRUE && $res->num_rows >0)
                        while($row = $res->fetch_assoc())
                        {
                            // savoir les elements de module instancié..
                         //   $sql="SELECT DISTINCT `element_Module_DetailsID` FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` WHERE `annee_UniversitaireID`=".$CurYear." AND `enseignantID`=".$prof['enseignantID']." ) ";
                            $sql="SELECT DISTINCT `element_Module_DetailsID`,element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE `annee_UniversitaireID`=".$CurYear." AND (affectation.`enseignantID`=".$prof['enseignantID']." OR affectation_partage.`enseignantID`=".$prof['enseignantID']." ) ) ";

                            //    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." ) ) ";

                            $res1 = $bdd->query($sql);
                            if($res1 == TRUE && $res1->num_rows >0)
                            {
                                echo '<div style="margin-bottom: 0px;margin-left:0px;margin-right:0px;" class="well well-small well1">Module: '.$row['mod_des'].'</div>';
                                echo '<div><table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                                while($row1 = $res1->fetch_assoc())
                                {
                                    $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
                                    // cours
                                    $i=1;
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_cours, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                        }
                                    }
                                    //cours partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    // echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                        }
                                    }
                                    //TD
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                        }
                                    }
                                    //TD partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                        }
                                    }
                                    //TP
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                        }
                                    }
                                    //TP partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];
                                    //   echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                        }
                                    }

                                    $i=0;
                                    echo '</td></tr>';

                                }
                                echo '</table></div>';
                            }

                        }
                    echo '</div>';



                    echo '</div>';


                }
            }

            ?>

        </div>

        <br>
        <div class="well">
            <h4>Liste des vacataires:</h4>
        </div>
        <div class="" >



            <?php
            $sql='SELECT `enseignantID`,`nom`,`prenom`,`grade`, G.* FROM `vacataire` AS E LEFT JOIN `grade` AS G ON E.grade=G.gradeID WHERE E.`enseignantID` IN (select `enseignant` from `enseignant_actif` where `actif`=1 AND `annee`='.$CurYear.') order by E.nom';
            $profs = $bdd->query($sql);

            if($profs)
            {
                echo "<div>";
                while ($prof = $profs->fetch_assoc())
                {

                    /*     echo '
                         <ul class="nav nav-tabs" id="myTab">
                             <li class="active"><a href="#Automne" data-toggle="tab">Automne</a></li>
                             <li><a href="#Printemps" data-toggle="tab">Printemps</a></li>
                         </ul>';
                    */
                    echo '<div class="box no-padding" style="page-break-after:always">';

                    $msg["charge"]="";
                    $msg["automne"]="";
                    $msg["printemps"]="";
                    $msg = prof_stats($prof['enseignantID'],$bdd,0,true);//,$bdd,0,true


                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>'.$prof['nom'].' '.$prof['prenom'].'</strong>   ('.$msg['charge'].'Hrs)</div><hr>';




                    echo '<div class="box1 no-padding1" id="Automne">';
                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>Automne:</strong>   '.$msg['automne'].'</div><hr>';
                    //		$CurYear=get_cur_year("id",$bdd);
                    $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`=1 AND `annee_UniversitaireID`='.$CurYear;

                    $res = $bdd->query($sql);
                    if($res == TRUE && $res->num_rows >0)
                        while($row = $res->fetch_assoc())
                        {
                            // savoir les elements de module instancié..
                            $sql="SELECT DISTINCT `element_Module_DetailsID`,element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE `annee_UniversitaireID`=".$CurYear." AND (affectation.`enseignantID`=".$prof['enseignantID']." OR affectation_partage.`enseignantID`=".$prof['enseignantID']." ) ) ";

                            //    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." ) ) ";

                            $res1 = $bdd->query($sql);
                            if($res1 == TRUE && $res1->num_rows >0)
                            {
                                echo '<div style="margin-bottom: 0px;margin-left:0px;margin-right:0px;" class="well well-small well1">Module: '.$row['mod_des'].'</div>';
                                echo '<div><table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                                while($row1 = $res1->fetch_assoc())
                                {
                                    $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
                                    // cours
                                    $i=1;
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_cours, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                        }
                                    }
                                    //cours partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    // echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                        }
                                    }
                                    //TD
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                        }
                                    }
                                    //TD partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                        }
                                    }
                                    //TP
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                        }
                                    }
                                    //TP partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];
                                    //   echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                        }
                                    }

                                    $i=0;
                                    echo '</td></tr>';
                                }
                                echo '</table></div>';
                            }

                        }
                    echo '</div>';

                    echo '<div class="box1 no-padding1" id="Printemps">';
                    echo '<div class="well well-small" style="margin-bottom: 10px;margin-top: 0px;"><strong>Printemps:</strong> '.$msg['printemps'].'</div><hr>';
                    //		$cur_year=get_cur_year("id",$bdd);
                    $sql='SELECT `module_DetailsID`, module.designation AS mod_des FROM `module_details` INNER JOIN module ON module_details.moduleID = module.moduleID WHERE `periode`= 2 AND `annee_UniversitaireID`='.$CurYear;

                    $res = $bdd->query($sql);
                    if($res == TRUE && $res->num_rows >0)
                        while($row = $res->fetch_assoc())
                        {
                            // savoir les elements de module instancié..
                            $sql="SELECT DISTINCT `element_Module_DetailsID`,element_ModuleID FROM `element_module_details` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE `annee_UniversitaireID`=".$CurYear." AND (affectation.`enseignantID`=".$prof['enseignantID']." OR affectation_partage.`enseignantID`=".$prof['enseignantID']." ) ) ";

                            //    $sql="SELECT DISTINCT `element_Module_DetailsID`, EMD.`element_ModuleID`, `designation`  FROM `element_module_details` AS `EMD` WHERE `module_DetailsID`=".$row['module_DetailsID']." AND element_Module_DetailsID IN (SELECT `element_Module_DetailsID` FROM `affectation` LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID WHERE (affectation.`enseignantID`=".$id." OR affectation_partage.`enseignantID`=".$id." ) ) ";

                            $res1 = $bdd->query($sql);
                            if($res1 == TRUE && $res1->num_rows >0)
                            {
                                echo '<div style="margin-bottom: 0px;margin-left:0px;margin-right:0px;" class="well well-small well1">Module: '.$row['mod_des'].'</div>';
                                echo '<div><table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
                                while($row1 = $res1->fetch_assoc())
                                {
                                    $H= get_elem_mod_charge_all($row1['element_ModuleID'],$bdd);
                                    // cours
                                    $i=1;
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_cours, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"cours\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours").'    ';
                                        }
                                    }
                                    //cours partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp,element_module_details.grp_cours, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation  LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID   INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"cours\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    // echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {

                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_cours'],"Cours (Partagé)").'    ';
                                        }
                                    }
                                    //TD
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TD\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD").'    ';
                                        }
                                    }
                                    //TD partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']."  AND affectation.nature=\"TD\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_td'],"TD (Partagé)").'    ';
                                        }
                                    }
                                    //TP
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM (((affectation INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID) INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID) INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID)  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND affectation.`annee_UniversitaireID`=".$CurYear." AND affectation.nature=\"TP\" AND affectation.`enseignantID`=".$prof['enseignantID'];

                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP");
                                        }
                                    }
                                    //TP partagé
                                    $sql="SELECT affectation.groups, affectation.nature, affectation.affectationID, element_module_details.grp_tp, element_module_details.grp_td, element_module.designation, element_module.heures_cours, element_module.heures_td, element_module.heures_tp
							FROM affectation LEFT JOIN affectation_partage ON affectation_partage.affectationID=affectation.affectationID INNER JOIN element_module_details ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID INNER JOIN module_details ON element_module_details.module_DetailsID = module_details.module_DetailsID  WHERE affectation.`element_Module_DetailsID`=".$row1['element_Module_DetailsID']." AND  affectation.nature=\"TP\" AND affectation_partage.`enseignantID`=".$prof['enseignantID'];
                                    //   echo $sql;
                                    $res2 = $bdd->query($sql);
                                    if($res2 == TRUE && $res2->num_rows >0)
                                    {
                                        if($row2 = $res2->fetch_assoc())
                                        {
                                            if ($i++==1) echo '<tr><td style="width: 60%;" data-id="'.$row1['element_Module_DetailsID'].'">'.$row2['designation'].get_badge_text("($H)").'</td><td>';
                                            echo get_label($row2['groups'],$row2['grp_tp'],"TP (Partagé)");
                                        }
                                    }

                                    $i=0;
                                    echo '</td></tr>';
                                }
                                echo '</table></div>';
                            }

                        }
                    echo '</div>';



                    echo '</div>';


                }
            }

            ?>

        </div> <!-- tab2 -->
        </div> <!-- tab contents -->
        </div> <!-- tabbable -->

        </div>
        </div>
        <?php
        include('include/scripts.php');
        echo '
        <script>
        $("#FS").click(function(e){

            $("body > :not(#tab2)").hide();
            $("#tab2").addClass("overlay");
            $("#tab2").appendTo("body");
            $("#FS").hide();
            window.print();
            $(document).keyup(function(e) {
                if (e.keyCode == 27) {
                $(".cancel").click();
                $("#tab2").removeClass("overlay");
                $("body > :not(#tab2)").show();
                $("#tab2").appendTo("#tab-content");
                $("#FS").show();
                }
            });
         });
         </script>
        ';
        include('include/footer.php');
    } else {
        header('location:login.php');
    }
?>