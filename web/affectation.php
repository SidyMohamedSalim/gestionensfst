<?php

    define("_VALID_PHP", true);

include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active=4.2;
   $script = array("datatable","editable","select2","scripts", "wizard" ,"affectation");
include('include/header.php');

    $AUvalid=!isAUvalid($bdd);

    if($access != _ADMIN) header('location:index.php');
    $CurYid=get_cur_year("id",$bdd);
    $CurYear_des=get_cur_year("des",$bdd);
    if(!empty($_GET['filiere'])) $_GET['filiere']= sanitize($_GET['filiere'],false,true);


    $a = new affectation($bdd);

   // $a->init(1,1,1,"cours",1);
    //$a->insert();
   /* $a->getFromId(7);
    if($a->exists())echo 'succes';
    else
    echo 'nnnnn';
*/
   // $a->ajouter_partage(1);
   // $a->ajouter_partage(2);
   // $a->supprimer_partage(2);

  //  echo 'enseignant='.$a->partageID;

?>
	
		<!--Body content-->
			<!-- horizontal nav -->
			
			<div>
					<ul class="breadcrumb">
						<li>
							<a href="#">Home</a> <span class="divider">/</span>
						</li>
                        <li class="active">
                            Affectation
                        </li>
					</ul>
			</div>
			
			<!-- Table des enseignants -->
					
			<div class="well">
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Affectations</h3>
                    <a class="btn btn-primary" href="affectation_export.php"><i class="icon-download icon-white"></i></a>
                    <?php  if($AUvalid):?>
					<button style="margin-top:8px;" id="open-wizard" class="btn btn-success btn-large pull-right">Nouvelle Affectation</button>

                    <?php  endif;?>
                </div> <!-- Fin well-small-->
				

				
				<div class="well" >
					
				
				  <div class="well well-small" >
				    <h4>Semestre d'automne:</h4>   
				  </div> <!-- Fin well-small-->
				  <div class="well well-small" >
				  <div class="well well-small" >
				  
				  <form style="display:inline;" METHOD="GET" action="">
				  <label for="mod_view" style="display:inline;" >Module</label>
				  <select id="mod_view" style="margin-bottom:0px;width:auto;" name="module_view">
					<option value="all" <?PHP if(isset($_GET['module_view']) && $_GET['module_view'] =="all") echo 'selected';?>>Tous</option>
					<option value="affected" <?PHP if(isset($_GET['module_view']) && $_GET['module_view']=="affected")echo 'selected';?>>Affectés</option>
					<option value="non-affected" <?PHP if(isset($_GET['module_view']) && $_GET['module_view']=="non-affected")echo 'selected';?>>Non-Affectés</option>
				  </select>

				  <select name="filiere" id="sel_filiere" >
				   <option value=""></option>
					<?php
					$sql='SELECT `filiereID`,`designation`, (SELECT `actif` FROM `filiere_actif` where filiere=filiereID AND annee='.$CurYid.') AS actif FROM  `filiere` HAVING actif=1';
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
                          <a class="toggle-vis" data-column="0"><span class="label label-info">Module</span></a>
                          <a class="toggle-vis" data-column="1"><span class="label label-info">Elements</span></a>
                          <a class="toggle-vis" data-column="2"><span class="label label-info">Semestre</span></a>
                          <a class="toggle-vis" data-column="3"><span class="label label-info">Filière</span></a>
                          <a class="toggle-vis" data-column="7"><span class="label label-info">Action</span></a>

                      </span>
				  </div>

					<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >
					
						<thead>
							<tr>
								<th>Module</th>
								<th>Elements affectés</th>
                                <th title="Semestre">Sem.</th>
                                <th>Filière</th>
								<th>Cours</th>
								<th>TD</th>
								<th>TP</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						
						<?php
						//functions get_cur_year & get_label -> fonctions.php

					/*	$sql = 'SELECT  module.designation, module_details.`module_DetailsID` FROM module, module_details WHERE module.`moduleID` IN 
						(SELECT `moduleID` FROM module_details WHERE `periode`=1 AND `annee_UniversitaireID`='.$CurYid.' AND `module_DetailsID` IN 
						(SELECT `module_DetailsID` FROM `element_module_details` WHERE `element_Module_DetailsID` IN 
						(SELECT `element_Module_DetailsID` FROM `affectation` WHERE `annee_UniversitaireID`='.$CurYid.' ))) AND module_details.`moduleID`= module.`moduleID`';
					*/	
						$sql='SELECT   MD.*, M.designation FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` WHERE MD.`annee_UniversitaireID`='.$CurYid.' AND `periode`=1';
						if(!empty($_GET['filiere'])) $sql=$sql." AND M.filiereID=".$_GET['filiere'];
						$res = $bdd->query($sql);
						
						if($res == TRUE)
                        {
                            while ($row = $res->fetch_assoc())
                            {
                                $pass=1;
                                if(isset($_GET['module_view']))
                                {
                                    if($_GET['module_view']=="all") $pass=1;
                                    elseif(is_affected($row['module_DetailsID'],$bdd))
                                    {
                                        //	if($_GET['module_view']=="affected") $pass=1;
                                        //else
                                        if($_GET['module_view']=="non-affected") $pass=0;
                                        //else $pass=1;
                                    }else{
                                        if($_GET['module_view']=="affected") $pass=0;
                                        //else if($_GET['module_view']=="non-affected") $pass=1;

                                    }
                                }

                                if($pass==1)
                                {
                                    //if($i==0){$ordre='odd'; $i=1;}else{$ordre='even'; $i=0;}
                                    echo '<tr><td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';

                                    //	$sql="SELECT count * FROM `element_module_details` LEFT JOIN `element_module` ON  WHERE `element_module_details`.`module_DetailsID` =".$row['module_DetailsID'];
                                    $sql="SELECT DISTINCT element_module_details.*, element_module.designation, M.semestre, F.designation AS filiere,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS cours,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS TD,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS TP
									FROM element_module_details
									INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID
									LEFT JOIN affectation ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID
									LEFT JOIN module AS M ON M.moduleID = element_module.moduleID
									LEFT JOIN filiere AS F ON F.filiereID = M.filiereID
									where element_module_details.module_DetailsID=".$row['module_DetailsID'];

                                    $res1 = $bdd->query($sql);
                                    if($res1 == TRUE)
                                    {
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
                                        //semestre
                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>S'.$row1['semestre'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        //filiere
                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>'.$row1['filiere'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';

                                        echo '<td class="no-padding3">'; //cours
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['cours'],$row1['grp_cours']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td class="no-padding3">'; //TD
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="TD" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['TD'],$row1['grp_td']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td class="no-padding3">'; //TP
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="1" data-type="TP" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['TP'],$row1['grp_tp']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';

                                        /*while ($row1 = $res1->fetch_assoc())
                                        {
                                        }
                                        echo $row1['designation'].'  ( '.get_label($row1['cours'],1,"cours").', '.get_label($row1['TD'],$row1['grp_td'],"TD").', '.get_label($row1['TP'],$row1['grp_tp'],"TP").' )<br/>';
                                        */echo '<td class="center "><a class="affect-btn btn btn-info" data-id="'.$row['module_DetailsID'].'" href="#edit" data-toggle="modal" ><i class=" icon-eye-open icon-white"></i> Details </a></td></tr>';

                                    }
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
								<th>Module</th>
								<th>Elements affectés</th>
                                <th title="Semestre">Sem.</th>
								<th>Filière</th>
								<th>Cours</th>
								<th>TD</th>
								<th>TP</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						
						<?php
						//functions get_cur_year & get_label -> fonctions.php
						$CurYid=get_cur_year("id",$bdd);
					
						$sql='SELECT   MD.*, M.designation FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` WHERE MD.`annee_UniversitaireID`='.$CurYid.' AND `periode`=2';
						if(!empty($_GET['filiere'])) $sql=$sql." AND M.filiereID=".$_GET['filiere'];
						$res = $bdd->query($sql);
						
						if($res == TRUE)
                        {
                            while ($row = $res->fetch_assoc())
                            {
                                $pass=1;
                                if(isset($_GET['module_view']))
                                {
                                    if($_GET['module_view']=="all") $pass=1;
                                    elseif(is_affected($row['module_DetailsID'],$bdd))
                                    {
                                        //	if($_GET['module_view']=="affected") $pass=1;
                                        //else
                                        if($_GET['module_view']=="non-affected") $pass=0;
                                        //else $pass=1;
                                    }else{
                                        if($_GET['module_view']=="affected") $pass=0;
                                        //else if($_GET['module_view']=="non-affected") $pass=1;

                                    }
                                }

                                if($pass==1)
                                {
                                    //if($i==0){$ordre='odd'; $i=1;}else{$ordre='even'; $i=0;}
                                    echo '
                                    <tr>
                                        <td><span data-id="'.$row['module_DetailsID'].'">'.$row['designation'].'</span></td>';

                                    //	$sql="SELECT count * FROM `element_module_details` LEFT JOIN `element_module` ON  WHERE `element_module_details`.`module_DetailsID` =".$row['module_DetailsID'];
                                    $sql="SELECT DISTINCT element_module_details.*, element_module.designation, M.semestre, F.designation AS filiere,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr1 FROM affectation WHERE affectation.nature = 'cours' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS cours,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr2 FROM affectation WHERE affectation.nature = 'TD' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS TD,
									(SELECT IFNULL(SUM(affectation.groups),0) AS expr3 FROM affectation WHERE affectation.nature = 'TP' AND affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND affectation.annee_UniversitaireID=".$CurYid.") AS TP
									FROM element_module_details
									INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID
									LEFT JOIN affectation ON affectation.element_Module_DetailsID = element_module_details.element_Module_DetailsID
									LEFT JOIN module AS M ON M.moduleID = element_module.moduleID
									LEFT JOIN filiere AS F ON F.filiereID = M.filiereID
									where element_module_details.module_DetailsID=".$row['module_DetailsID'];
//echo $sql; die();
                                    $res1 = $bdd->query($sql);
                                    if($res1 == TRUE)
                                    {
                                        $NB=$res1->num_rows;
                                        //element module
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
                                        //semestre
                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>S'.$row1['semestre'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        //filiere
                                        echo '<td class="no-padding3">';
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div>'.$row1['filiere'].'</div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td class="no-padding3">'; //cours
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['cours'],$row1['grp_cours']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td class="no-padding3">'; //TD
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="TD" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['TD'],$row1['grp_td']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';
                                        echo '<td class="no-padding3">'; //TP
                                        for ($i=0;$i<$NB;$i++)
                                        {
                                            /* seek to i-th row */
                                            $res1->data_seek($i);
                                            /* fetch row */
                                            $row1 = $res1->fetch_assoc();
                                            echo '<div class="text-center"><a class="edit_aff" href="#edit_aff" data-toggle="modal" data-periode="2" data-type="TP" data-elemD="'.$row1['element_Module_DetailsID'].'">'.get_badge($row1['TP'],$row1['grp_tp']).'</a></div>';
                                            if($i+1<$NB) echo '<hr>';
                                        }
                                        echo '</td>';

                                        echo '<td class="center "><a class="affect-btn btn btn-info" data-id="'.$row['module_DetailsID'].'" href="#edit" data-toggle="modal" ><i class=" icon-eye-open icon-white"></i> Details </a></td></tr>';

                                    }

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
			</div> <!-- Fin well-->

    <?php  if($AUvalid):?>
			<!-- Wizard :  -->
			<div class="wizard" id="wizard_affect">

				<h1>Affectation d'un module</h1>

				<div class="wizard-card" data-cardname="card1">
					<h3>Choix de l'affectation:</h3>
						<input type="hidden" name="wizard_affect"/>
						<label for="semestre" style="display:inline;padding-right:10px;" >Semestre:  </label>
				 
						<select id="semestre" data-placeholder="Choisir un semestre..." style="width:200px;margin-bottom:0px;" name="periode"> 
							<option value="1">Automne</option>
							<option value="2">Printemps</option>
						</select> <br/><br/>
						<label for="sel_mod" style="display:inline;padding-right:25px" >Module: </label>
						<input type="hidden" name="module" id="sel_mod" style="width:300px" /><span id="mod"></span>
						<div id="mod_stat"></div>
						<br/><br/>
						<label for="sel_enseignant" style="display:inline;padding-right:0px;" >Enseignant: </label>
						<input type="hidden" name="enseignant" id="sel_enseignant" style="width:300px" /><span id="prof"></span>
						<span >vacataire?<input id="vacataire" class="inline" type="checkbox" value="0"/></span>
						<div id="prof_stat"></div>
				</div>

				<div class="wizard-card" data-cardname="card2">
					<h3 style="display: inline;">Details de l'affectation</h3>
						<div id="element" data-on="0" style="display: inline;">
						
						</div>
					<!--	<label for="nbr">Nombre d'étudiants:</label>
						<input type="number" name="nbr" id="nbr" onkeypress="return isNumberKey(event)" data-validate="not_empty"></input> -->
				</div>
				<div class="wizard-card" data-cardname="card3">
					<h3>Confirmation</h3>
						<div id="element3">
							
						</div>
				</div>
			    <div class="wizard-success">
					<div class="alert alert-success">
						Le module a été instancié <strong>avec succès</strong>
					</div>
		
					<a class="btn autre-instance">Instancier un autre module</a>
					<span style="padding:0 10px">ou</span>
					<a class="btn fini">Terminer</a>
				</div>

				<div class="wizard-error">
					<div class="alert alert-error">
						Erreurs...
					</div>
				</div>

				<div class="wizard-failure">
					<div class="alert alert-error">
					Problème lors de l'envois des information..
					</div>
				</div>
			</div>
			<!-- END Wizard :  -->
    <?php  endif;?>
			<!-- Modal messages : details-->
			
			    <div id="edit" class="medium-modal modal hide" tabindex="-1" >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
                        <button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>
						<h3>Affectation</h3>
					</div>
					
					<div class="modal-body" style="">
						
							
						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
						
					</div>
					
				</div>
			
			<!-- END of Modal messages : details -->
			
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
			
			
		<!-- Modal messages : Modifier Affectation professeur-->
			
			    <div id="edit-aff" class="modal hide fade" style="width: 650px;margin-left:-325px;margin-top: -239px;top: 50%;z-index: 1080;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>Modifier l'affectation</h3>
					</div>
					<form id="form-modf" method="POST" action="process_gestion.php" >
					<div class="modal-body" style="height: 300px;">
						
							
						
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
						<input type="submit" value="Modifier" class="btn btn-primary">
					</div>
					</form>
				</div>
			
			<!-- END of Modal messages : Modifier Affectation professeur -->
		



    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.' (Automne)"';?>;

        var message2=document.getElementById('title').innerHTML;
        message2+=<?php echo '"  '.$CurYear_des.' (Printemps)"';?>;

    </script>
    <?php include('include/scripts.php');?>

    <script>



    </script>

<?php

include('include/footer.php');
} else {
   header('location:login.php');
}
?>