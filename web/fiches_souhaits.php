<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {

    if(isset($_SESSION['admin']) && $_SESSION['admin']) header('location:index.php');

    // Add your protected page content here!
    $active='4.3';
    $script = array("datatable", "editable", "select2", "scripts", "switch", "script_fiches");
    include('include/header.php');
    $CurYear_des=get_cur_year("des",$bdd);
    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li><a href="#">Home</a> <span class="divider">/</span></li>
            <li class="active">Fiches</li>

        </ul>
    </div>
    <?php
    //functions get_cur_year & get_label -> fonctions.php
    $CurYid=get_cur_year("id",$bdd);

    if(!empty($_GET['filiere'])) $_GET['filiere']= sanitize($_GET['filiere'],false,true);
    $sql='select * from `fiche_souhait` where annee_universitaire ='.$CurYid;
    $res= $bdd->query($sql);
    $lancer=1;
    if($res==true && $res->num_rows ==1 && $fiche = $res->fetch_assoc())
    {
        $valid=($fiche['active'])?0:1;
        $formID=$fiche['id'];


    }else $lancer=0;
    ?>
    <div class="well">

        <div class="well well-small" >
            <h3 id="title" style="display:inline-block;">Fiche de souhaits</h3>
            <?php
            if($lancer==1)
            {
                $prof=get_prof_id($bdd);

                echo '<div id="fiche" data-id="'.$formID.'" style="display:inline-block;" class=" pull-right alert alert-info">
					   	    <span>La periode de pre-affection est du <i>'.$fiche['debut'].'</i> à <i>'.$fiche['fin'].'</i>
					   	    ';
                if($valid==1) echo '<b>( Terminée )</b>';
                else echo '<b>( En cours )</b>';
				echo'      </span></div>';


                $sql='select * from `fiche_souhait_valid` where fiche ='.$formID.' AND `enseignant`='.$prof;
                $res= $bdd->query($sql);

                $valid_prof=-1;
                if($res==true && $res->num_rows ==1)
                {
                    if($row=$res->fetch_assoc())
                    {
                        $valid_prof=$row['valid'];
                    }
                }

                if(isset($_POST['modifier']))
                {
                    $err="";
                    if($valid==1)
                        $err='Impossible: La période de pre-affectation est terminer!!';

                    else
                    {
                        if(!empty($_POST['valid']))
                            $v=1;
                        else $v=0;

                        if($valid_prof!=-1)
                        {
                            $sql='UPDATE `fiche_souhait_valid` SET `valid`='.$v.' WHERE `fiche`='.$formID.' AND `enseignant`='.$prof;

                            if($bdd->query($sql) == FALSE)
                                $err='Probleme interne!!';
                            else $valid_prof=$v;
                        }else{
                            $sql='INSERT INTO `fiche_souhait_valid`( `fiche`, `enseignant`, `valid`) VALUES ('.$formID.','.$prof.','.$v.')';
                            if($bdd->query($sql) == FALSE)
                                $err='Probleme interne!!';
                            else $valid_prof=$v;
                        }
                    }
                }

                if($valid_prof==1)$check='checked="checked"';
                else $check='';
                if($valid)$disable='disabled';
                else $disable='';
      echo '
        </div>

        <div class="well">
            <form  class="form-inline" style="margin: 0px;" method="POST" action="">
                <label style="padding-top: 5px;" class="span1" for="valid-switch">Valide?:</label>
                <div class="switch"  data-off-label="Non" data-on-label="Oui" style="vertical-align: middle;" >
                    <input id="valid-switch" type="checkbox" name="valid" '.$check.' '.$disable.' />
                </div>
                <input style="margin-left: 20px;" type="submit" id="modifier" name="modifier" value="Modifier" class="btn btn-primary " '.$disable.' />
            </form>';
        if(!empty($err))
            echo'
            <div style="margin: 0px;margin-top: 10px;" class=" alert alert-error">
            <span>'.$err.'</span>
            </div>
            ';
      echo '
        </div>';

            }
    if($lancer==1)
    {
            ?>

            <div class="well well-small" >
                <form style="display:inline;" METHOD="GET" action="">
                    <label style="display:inline;" for="sel_filiere">Filtrer:  </label>
                    <select name="filiere" id="sel_filiere" >
                        <option value=""></option>
                        <?php
                        $sql='SELECT `filiereID`,`designation`, (SELECT `actif` FROM `filiere_actif` where filiere=filiereID AND annee='.$CurYid.') AS actif FROM  `filiere` HAVING actif=1 ';
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


                      </span>
            </div>
        <?php
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
                        <th>Module</th>
                        <th>Elements</th>
                        <th title="Semestre">Sem.</th>
                        <th>Filière</th>
                        <th>Cours</th>
                        <th>TD</th>
                        <th>TP</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php
                     /* fonctions pour afficher les boutton */
                    // <div class="text-center"><button class="drop_wish btn btn-icon" data-periode="1" data-prof="'.$prof.'" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'"><i class="icon-minus-sign"></i></button>
                    function btn_minus($prof,$Elem_detail_id,$type,$periode,$valid,$low_bound){
                        if($valid!=1 && $low_bound!=0)
                        return '<button class="drop_wish btn btn-icon" data-periode="'.$periode.'" data-prof="'.$prof.'" data-type="'.$type.'" data-elemD="'.$Elem_detail_id.'"><i class="icon-minus-sign"></i></button>';
                        else return "";
                    }
                    function btn_plus($prof,$Elem_detail_id,$type,$periode,$valid,$low_bound){
                        if($valid!=1 && $low_bound!=0)
                            return '<button class="add_wish btn btn-icon" data-prof="'.$prof.'" data-periode="'.$periode.'" data-type="'.$type.'" data-elemD="'.$Elem_detail_id.'"><i class="icon-plus-sign"></i></button>';
                        else return "";
                    }


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
                                $sql="SELECT DISTINCT EMD.*, EM.designation, M.semestre, F.designation AS filiere,
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr1 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'cours' AND fiche_souhait_details.element_Module_DetailsID = EMD.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS cours,
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr2 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'TD' AND fiche_souhait_details.element_Module_DetailsID = EMD.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS TD,
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr3 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'TP' AND fiche_souhait_details.element_Module_DetailsID = EMD.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS TP
FROM element_module_details AS EMD
  INNER JOIN element_module AS EM ON EMD.element_ModuleID = EM.element_ModuleID
  LEFT JOIN fiche_souhait_details ON fiche_souhait_details.element_Module_DetailsID = EMD.element_Module_DetailsID
   LEFT JOIN module AS M ON M.moduleID = EM.element_ModuleID
   LEFT JOIN filiere AS F ON F.filiereID = M.filiereID
  where EMD.module_DetailsID=".$row['module_DetailsID'];

                              //  ECHO $sql;die();

                                $res1 = $bdd->query($sql);
                                if($res1 == TRUE)
                                {
                                    $NB=$res1->num_rows;

                                    echo '<td>';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>'.$row1['designation'].'</div>';
                                    }
                                    echo '</td>';
                                    //semestre
                                    echo '<td >';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>S'.$row1['semestre'].'</div>';

                                    }
                                    echo '</td>';
                                    //filiere
                                    echo '<td >';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>'.$row1['filiere'].'</div>';

                                    }
                                    echo '</td>';
                                    echo '<td>'; //cours
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        //<button class="drop_wish btn btn-icon" data-periode="1" data-prof="'.$prof.'" data-type="cours" data-elemD="'.$row1['element_Module_DetailsID'].'"><i class="icon-minus-sign"></i></button>
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"cours",1,$valid,$row1['grp_cours']).get_badge($row1['cours'],$row1['grp_cours']).btn_plus($prof,$row1['element_Module_DetailsID'],"cours",1,$valid,$row1['grp_cours']).'</div>';
                                    }
                                    echo '</td>';
                                    echo '<td>'; //TD
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"TD",1,$valid,$row1['grp_td']).get_badge($row1['TD'],$row1['grp_td']).btn_plus($prof,$row1['element_Module_DetailsID'],"TD",1,$valid,$row1['grp_td']).'</div>';
                                    }
                                    echo '</td>';
                                    echo '<td>'; //TP
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"TP",1,$valid,$row1['grp_tp']).get_badge($row1['TP'],$row1['grp_tp']).btn_plus($prof,$row1['element_Module_DetailsID'],"TP",1,$valid,$row1['grp_tp']).'</div>';
                                    }
                                    echo '</td>';

                                    /*while ($row1 = $res1->fetch_assoc())
                                    {
                                    }
                                    echo $row1['designation'].'  ( '.get_label($row1['cours'],1,"cours").', '.get_label($row1['TD'],$row1['grp_td'],"TD").', '.get_label($row1['TP'],$row1['grp_tp'],"TP").' )<br/>';
                                    */
                                   // echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td>';
                                    echo '</tr>';
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
                <h4>Semestre de printemps:</h4>
            </div> <!-- Fin well-small-->
            <div class="well well-small" >

                <table id="liste2" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered" >

                    <thead>
                    <tr>
                        <th>Module</th>
                        <th>Elements</th>
                        <th title="Semestre">Sem.</th>
                        <th>Filière</th>
                        <th>Cours</th>
                        <th>TD</th>
                        <th>TP</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php


                    $sql='SELECT   MD.*, M.designation FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` WHERE MD.`annee_UniversitaireID`='.$CurYid.' AND `periode`=2';
                    if(!empty($_GET['filiere'])) $sql=$sql." AND M.filiereID=".$_GET['filiere'];
                    $res = $bdd->query($sql);


                    if($res == TRUE)
                    {
                        while ($row = $res->fetch_assoc())
                        {
                            //ToDo: Filtrage des module choisit completement..
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
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr1 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'cours' AND fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS cours,
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr2 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'TD' AND fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS TD,
  (SELECT IFNULL(SUM(fiche_souhait_details.groups),0) AS expr3 FROM fiche_souhait_details WHERE fiche_souhait_details.fiche=".$formID." AND fiche_souhait_details.nature = 'TP' AND fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID AND fiche_souhait_details.enseignantID=".$prof.") AS TP
FROM element_module_details
  INNER JOIN element_module ON element_module_details.element_ModuleID = element_module.element_ModuleID
  LEFT JOIN fiche_souhait_details ON fiche_souhait_details.element_Module_DetailsID = element_module_details.element_Module_DetailsID
  LEFT JOIN module AS M ON M.moduleID = element_module.element_ModuleID
   LEFT JOIN filiere AS F ON F.filiereID = M.filiereID
  where element_module_details.module_DetailsID=".$row['module_DetailsID'];

                                $res1 = $bdd->query($sql);
                                if($res1 == TRUE)
                                {
                                    $NB=$res1->num_rows;

                                    echo '<td>';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>'.$row1['designation'].'</div>';
                                    }
                                    echo '</td>';
                                    //semestre
                                    echo '<td >';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>S'.$row1['semestre'].'</div>';

                                    }
                                    echo '</td>';
                                    //filiere
                                    echo '<td ">';
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div>'.$row1['filiere'].'</div>';

                                    }
                                    echo '</td>';
                                    echo '<td>'; //cours
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"cours",2,$valid,$row1['grp_cours']).get_badge($row1['cours'],$row1['grp_cours']).btn_plus($prof,$row1['element_Module_DetailsID'],"cours",2,$valid,$row1['grp_cours']).'</div>';
                                    }
                                    echo '</td>';
                                    echo '<td>'; //TD
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"TD",2,$valid,$row1['grp_td']).get_badge($row1['TD'],$row1['grp_td']).btn_plus($prof,$row1['element_Module_DetailsID'],"TD",2,$valid,$row1['grp_td']).'</div>';
                                    }
                                    echo '</td>';
                                    echo '<td>'; //TP
                                    for ($i=0;$i<$NB;$i++)
                                    {
                                        /* seek to i-th row */
                                        $res1->data_seek($i);
                                        /* fetch row */
                                        $row1 = $res1->fetch_assoc();
                                        echo '<div class="text-center">'.btn_minus($prof,$row1['element_Module_DetailsID'],"TP",2,$valid,$row1['grp_tp']).get_badge($row1['TP'],$row1['grp_tp']).btn_plus($prof,$row1['element_Module_DetailsID'],"TP",2,$valid,$row1['grp_tp']).'</div>';
                                        echo "\n";
                                    }
                                    echo '</td>';

                                    /*while ($row1 = $res1->fetch_assoc())
                                    {
                                    }
                                    echo $row1['designation'].'  ( '.get_label($row1['cours'],1,"cours").', '.get_label($row1['TD'],$row1['grp_td'],"TD").', '.get_label($row1['TP'],$row1['grp_tp'],"TP").' )<br/>';
                                    *///echo '<td class="center "><a class="bulk-add-btn btn btn-success" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-plus-sign icon-white"></i> </a><a class="bulk-del-btn btn btn-danger" data-id="'.$row['module_DetailsID'].'" href="#" ><i class=" icon-minus-sign icon-white"></i> </a></td>';
                                    echo '</tr>';
                                }
                            }
                        }
                    $res->close();
                    }
                    ?>

                    </tbody>
                </table>
            </div> <!-- Fin well-small-->
        </div  > <!-- Fin well printemp-->





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