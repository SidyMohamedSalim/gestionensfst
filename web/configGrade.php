<?php

    define("_VALID_PHP", true);
    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active=5.1;
    $script = array("editable", "scripts", "datatable");
    include('include/header.php');

    $AUvalid=!isAUvalid($bdd);

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');
    $CurYear_des=get_cur_year("des",$bdd);
    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            <li class="active"> Grades</li>

        </ul>
    </div>

    <div class="well">

        <div class="well well-small" >
            <h3 id="title" style="display:inline-block;">Liste des grades</h3>
            <?php  if($AUvalid):?>
            <a style="margin-top:8px;" class="btn btn-success btn-large pull-right" href="#ajout" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouveau </a>
            <?php  endif;?>
        </div>

        <div class="well" >
            <h4>Grade:</h4>
            <table id="liste1" cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                <thead>
                <tr>
                    <th data-hide="phone,tablet" >#</th>
                    <th>Code</th>
                    <th >Designation</th>
                    <th data-hide="phone">Permission</th>
                    <th>Charge Horaires</th>
                    <th>status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $CurYear=get_cur_year("id",$bdd);
                $err="";
                // Ajouter un grade
                if(!empty($_POST['ajouter_grade']))
                {
                    if(!empty($_POST['designation']) && !empty($_POST['code']) && !empty($_POST['charge']) )
                    {
                        if( !filter_var($_POST['charge'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))))
                            $err.='La charge doit être un nombre! \n';
                        else
                        {
                            $_POST['designation']=sanitize($_POST['designation']);
                            $_POST['code']=sanitize($_POST['code']);
                            if(!empty($_POST['cours']) ) $cours=1; else $cours=0;
                            if(!empty($_POST['TD']) ) $TD=1; else $TD=0;
                            if(!empty($_POST['TP']) ) $TP=1; else $TP=0;

                            $sql="SHOW TABLE STATUS LIKE 'grade'";
                            $res = $bdd->query($sql);
                            if($res == true)
                            {
                                if($stat = $res->fetch_assoc())
                                {
                                    $newID=$stat['Auto_increment'];

                                    $sql="INSERT INTO `grade`(`gradeID`,`code`, `designation`, `cours`, `TD`, `TP`, `annee`) VALUES (".$newID.",'".$_POST['code']."','".$_POST['designation']."','".$cours."','".$TD."','".$TP."',".$CurYear.")";

                            if($bdd->query($sql) == FALSE)
                                $err.="Probleme interne!! ";
                            else{
                                $sql="INSERT INTO `grade_actif`(`grade`, `annee`, `chargeHrs`, `actif`) VALUES (".$newID.",".$CurYear.",'".$_POST['charge']."',1)";
                                if(!$bdd->query($sql))
                                    $err.="Probleme interne!!!";

                            }
                                }
                            }
                        }

                    }else $err.="Il faut remplire tout les champs!!";
                }



                $sql="SELECT G.*, (select `actif` from `grade_actif` where `grade`=G.`gradeID` AND `annee`='".$CurYear."') AS actif, (select `chargeHrs` from `grade_actif` where `grade`=G.`gradeID` AND `annee`='".$CurYear."') As hrs FROM `grade` AS G WHERE G.annee<=".$CurYear;

                $res=$bdd->query($sql);
                if($res==TRUE && $res->num_rows >0)
                {
                    while($row=$res->fetch_assoc())
                    {
                        $id=$row['gradeID'];
                        $actif_grade=($row['actif']==1)?"actif":"inactif";
                        $hrs=($row['hrs'])?$row['hrs']:0;
                        echo '<tr>
                        <td>'.$row['gradeID'].'</td>
                        <td><span class="grade_code" data-type="text" data-name="grade_code" data-pk="'.$id.'">'.$row['code'].'</span></td>
                        <td><span class="grade_designation" data-name="grade_designation" data-pk="'.$id.'" data-type="text">'.$row['designation'].'</span></td>
                        ';

                        if(!$AUvalid)
                        {
                            echo '<td>';
                            if($row['cours']==1) echo '<span class="label label-info">Cours</span>';
                            if($row['TD']==1) echo ' <span class="label label-info">TD</span>';
                            if($row['TP']==1)  echo ' <span class="label label-info">TP</span>';
                            echo '</td>';
                        }
                        else{
                            echo '
                            <td><span class="grade_permission" data-name="grade_permission" data-type="checklist" data-pk="'.$id.'" data-value="';
                            if($row['cours']==1) echo '1';
                            if($row['TD']==1) echo ',2';
                            if($row['TP']==1) echo ',3';
                            echo '"></span></td>
                        ';
                        }


                        echo '
                        <td><span class="grade_charge" data-name="grade_charge" data-pk="'.$id.'" data-type="number">'.$hrs.'</span></td>
                        <td class="center"><span class="grade_status label label-'.label($actif_grade).'"  data-pk="'.$id.'" data-name="grade_status"  data-value="'.$actif_grade.'">'.$actif_grade.'</span></td>';

                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>


    </div>



    <?php  if($AUvalid):?>
    <!-- Modal messages : Nouveau-->

    <div id="ajout" class="modal hide fade">
        <form id="form-ajout" method="POST" action="" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Ajouter</h3>
            </div>
            <div class="modal-body">

                <fieldset>
                    <legend>Info</legend>
                    <label for="code_A">Code</label>
                    <input type="text" name="code" value="" id="code_A" required>
                    <label for="designation_A">Designation</label>
                    <input type="text" name="designation" value="" id="designation_A" required>

                    <label for="charge_A"> 	Charge Horaires</label>
                    <input type="number" name="charge" onkeypress="return isNumberKey(event)" value="" id="charge_A" required>

                    <fieldset>
                        <legend>Permission:</legend>
                        <label class="checkbox inline">
                            <input type="checkbox" name="cours" id="cours" value="1"/> cours
                        </label>
                        <label class="checkbox inline">
                            <input type="checkbox" name="TD" id="TD" value="1"/> TD
                        </label>
                        <label class="checkbox inline">
                            <input type="checkbox" name="TP" id="TP" value="1"/> TP
                        </label>
                        <!--	<label class="checkbox inline" for="TD">TD</label>
                            <input type="checkbox" name="TD" id="TD" value="0"/>
                            <label class="checkbox inline" for="TP">TP</label>
                            <input type="checkbox" name="TP" id="TP" value="0"/>
                            -->
                    </fieldset>

                </fieldset>

            </div>
            <div class="modal-footer">
                <input class="btn btn-primary" type="submit" name="ajouter_grade" value="Ajouter" />
                <a href="#" class="btn" data-dismiss="modal">Fermer</a>
            </div>
        </form>
    </div>

    <!-- END of Modal messages : Nouveau -->
    <?php  endif;?>
    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.'"';?>;

    </script>
    <?php
    if(!empty($err))
        echo '
                            <script>
                                alert("'.$err.'");
                            </script>
                            ';
    include('include/scripts.php');
    ?>
    <?php  if($AUvalid):?>
    <script>

     $('.grade_permission').editable({
         url: 'process_gestion.php?',

         source: [
             {value: 1, text: 'Cours'},
             {value: 2, text: 'TD'},
             {value: 3, text: 'TP'}
         ],
         title: 'Permission?',
         validate: function(value) {
             if($.trim(value) == '') {
                 return 'Il faut choisir un au moins !';
             }
         },
         ajaxOptions: {
             dataType: 'json' //assuming json response
         },
         success: function(response, newValue) {
             if(!response.succes) return response.mssg;
         }
         ,
         error: function(response, newValue) {
             if(response.status === 500)
                 return 'Service unavailable. Please try later.';
         }
     });

        $('.grade_code').editable({
            type: 'text',
            url: 'process_gestion.php',
            title: 'Entrer le code',
            validate: function(value) {
                if($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(response, newValue) {
                if(!response.succes) return response.mssg;
            }
            ,
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
        });
        $('.grade_designation').editable({
            type: 'text',
            url: 'process_gestion.php',
            title: 'Entrer la designation',
            inputclass : 'input-xlarge',
            validate: function(value) {
                if($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(response, newValue) {
                if(!response.succes) return response.mssg;
            }
            ,
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
        });
        $('.grade_charge').editable({
            type: 'number',
            url: 'process_gestion.php',
            title: 'Entrer la charge Annuelle',
            validate: function(value) {
                if($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            /*	ajaxOptions: {
             dataType: 'json' //assuming json response
             },
             success: function(response, newValue) {
             //	if(!response.success) return response.msg;
             } */
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(response, newValue) {
                if(!response.succes) return response.mssg;
            }
            ,
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
        });
     $('.grade_status').editable({
         type: 'select',
         url: 'process_gestion.php',
         source: 'load.php?type=status',
         inputclass : 'input-small',
         title: 'Status?',
         ajaxOptions: {
             dataType: 'json' //assuming json response
         },
         success: function(response, newValue) {

             if(!response.succes) return response.mssg;
             else location.reload();
         }
         ,
         error: function(response, newValue) {
             //    alert(JSON.stringify(response, null, 4));
             if(response.status === 500) {
                 return 'Service unavailable. Please try later.';
             }
         }
     });
    </script>
    <?php  endif;?>
<?php
    include('include/footer.php');
} else {
    header('location:login.php');
}
?>