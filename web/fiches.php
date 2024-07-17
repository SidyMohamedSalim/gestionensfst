<?php
    define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if(login_check($bdd) == true) {
 
   // Add your protected page content here!
   $active='2.1';
    $script = array("datatable", "editable", "scripts", "switch" , "date", "editor");
include('include/header.php');
    $CurYear_des=get_cur_year("des",$bdd);

    $AUvalid=isAUvalid($bdd);

    if($access != _ADMIN && $access != _COLLEGE) header('location:index.php');

    $dept=get_prof_dept($bdd);
    $CurYear=get_cur_year("id",$bdd);
    $sql = 'SELECT * FROM `fiche_souhait` WHERE `annee_universitaire`='.$CurYear.' AND `departement`='.$dept;
    $res = $bdd->query($sql);

    $fiche_active=-1;

    if($res==true && $res->num_rows ==1)
    {
        if($fiche=$res->fetch_assoc())
        {
            $fiche_active=$fiche['active'];
        }
        $res->close();
    }

    if($fiche_active!=-1 && !empty($_POST['modifier']))
    {
        $err="";
        if(empty($_POST['valid'])) $v=1;
        else $v=0;

        if ( !empty($_POST['fin']) && !empty($_POST['fiche_id']))
        {
            $test_arr1  = explode('-', $_POST['fin']);
            //bool checkdate ( int $month , int $day , int $year )
            if (!checkdate($test_arr1[1], $test_arr1[2], $test_arr1[0]))
            $err.='Le format de la date est invalide!!\n';

            else{
            $sql='UPDATE `fiche_souhait` SET `fin`="'.$_POST['fin'].'",`active`='.$v.' WHERE `id`='.$fiche['id']; //$_POST['fiche_id'];
            if($bdd->query($sql) == FALSE)
            $err.='Probleme interne!!\n';
            else
            {
                $fiche['fin']=$_POST['fin'];
                $fiche_active=$v;
            }

        }
        }else{
            $err.='Données vides!\n';
        }

        if(!empty($err))
            echo '
            <script>
            alert("'.$err.'");
            </script>
            ';

    }
    if($AUvalid && $fiche_active==1) $fiche_active=0;
    ?>

		<!--Body content-->
			<!-- horizontal nav -->
			
			<div>
					<ul class="breadcrumb">
						<li>
							<a href="#">Home</a> <span class="divider">/</span>
						</li>
                        <li class="active">
                            Fiches <span class="divider">/</span>
                        </li>
                        <li><a href="fiches_all.php">Tous</a></li>
					</ul>
			</div>
				
			<div class="well">
			
				<div class="well well-small" >
					<h3 id="title" style="display:inline-block;">Fiche de souhaits</h3>
                    <?php
                    if($fiche_active!=-1)
                    {
                        echo '<div id="fiche" data-id="'.$fiche['id'].'" style="display:inline-block;" class=" pull-right alert alert-info">
					   	    <span>La periode de pre-affection est du <i>'.$fiche['debut'].'</i> à <i>'.$fiche['fin'].'</i>
					   	    ';
                        if($fiche_active==0) echo '<b>( Terminée )</b>';
                        else echo '<b>( En cours )</b>';
                    if(!$AUvalid)
                        echo'      </span><a class="btn btn-info" data-id="4" href="#edit_fich" data-toggle="modal"><i class=" icon-cog icon-white"></i></a>';

                        echo '</div>';

                    }
                    ?>
				</div>
                <?php
                if($fiche_active!=-1)
                {
                $disable='';
                if($fiche_active==0) $disable='disabled';
                echo '
                <div class="well well-small" >
                    <span>Notifier par email: <button id="notifier_btn" class="btn btn-info" href="#notifier" '.$disable.' data-toggle="modal" data-keyboard="true" data-id="5"><i class="icon-white icon-envelope"></i></button></span>
                    ';

                    $sql = 'SELECT enseignant.*, (select `actif` from `enseignant_actif` where enseignant_actif.`enseignant`=enseignant.`enseignantID` AND enseignant_actif.`annee`='.$CurYear.') AS actif  FROM `enseignant` WHERE enseignant.vacataire=0 AND departementID='.$dept.' HAVING actif=1';

            //    echo $sql;
                    $prof = $bdd->query($sql);

                    if(isset($_POST['notifier'])){
                        //todo: htmlpurifier or htmLawed
                        $err='';
                        if($fiche_active==0) $err='Problème: La periode de pre-affectation est terminer!';

                        elseif(!empty($_POST['sujet']) && !empty($_POST['corp']) && !empty($_POST['to']))
                        {
                            if($prof==true ) // $prof->num_rows >0
                            {
                                $prof_total_send=0;
                                $prof_send=0;

                                while($row=$prof->fetch_assoc())
                                {
                                    $prof_total_send++;

                                    if($_POST['to']!="all")
                                    {
                                        $valid_prof=0;
                                        $sql = 'SELECT *  FROM `fiche_souhait_valid` WHERE `fiche`='. $fiche['id'].' AND `enseignant`='.$row['enseignantID'];
                                        $res2=$bdd->query($sql);
                                        //  echo $sql;
                                        if($res2==true && $res2->num_rows >0)
                                        {
                                            if($fiche_prof=$res2->fetch_assoc())
                                            {
                                                $valid_prof=$fiche_prof['valid'];
                                            }
                                        }
                                    }

                                    if(!isset($valid_prof) || $valid_prof!=1)
                                    {
                                        $message['adresse']=$row['email'];
                                        $message['nom']=$row['nom'].' '.$row['prenom'];
                                        $message['sujet']=$_POST['sujet'];
                                        $message['corp']=$_POST['corp'];

                                        $message['corp'] = str_replace(array('[nom]', '[prenom]'),
                                            array($row['nom'], $row['prenom']), $message['corp']);

                                        if(envoi_mail($message,$bdd))
                                        $prof_send++;
                                    }

                                }
                                $prof->data_seek(0);
                                echo '<div class="alert alert-info" id="alert"><strong>'.$prof_send.' messages envoyé sur '.$prof_total_send.'</strong>	</div>';
                            }else $err='Probleme interne!!';
                        }else $err='Il faut remplir tous les champs!!';

                            if (!empty($err)) {
                                echo '<div class="alert alert-error" id="alert"><strong>'.$err.'</strong>	</div>';
                            }


                    }
                    ?>

                </div>
                <?php
                }
                ?>
				<div class="well" >
					
			<?php
            if($fiche_active==-1)
            {
				$lancer_err= "";
				if(isset($_POST['lancer']) && !$AUvalid)
				{
					if(empty($_POST['debut']) || empty($_POST['fin']))
					$lancer_err.="<p>Il faut remplir les champs!!</p>";
					else
					{
						$test_arr1  = explode('-', $_POST['debut']);
						$test_arr2  = explode('-', $_POST['fin']);
						//bool checkdate ( int $month , int $day , int $year )
						if (!checkdate($test_arr1[1], $test_arr1[2], $test_arr1[0]) || !checkdate($test_arr2[1], $test_arr2[2], $test_arr2[0])) 
						$lancer_err.="<p>Le format de la date est invalide!!</p>";
						
						else{
						$sql='INSERT INTO `fiche_souhait`(`annee_universitaire`, `departement`, `debut`, `fin`) VALUES ('.$CurYear.','.$dept.',"'.$_POST['debut'].'","'.$_POST['fin'].'")';
						if($bdd->query($sql) == FALSE)
						$lancer_err.="<p>Probleme interne!!</p>";

						}
					}

                    if(empty($lancer_err))
                    {
                        echo '
						<div class="alert alert-success">
							<p>La periode de pre-affection a été démarrée avec succès! (Rechargement de la page..)</p>
						</div>
						<script>
						setTimeout(function() { window.location = window.location.href; }, 1000);
						</script>
					';
                        die();
                    }
				}

				if(!$AUvalid && (isset($_POST['demarrer']) || !empty($lancer_err)))
				{
				
				if(!empty($lancer_err))
				echo '<div class="alert alert-error" id="alert"><strong>'.$lancer_err.'
				</strong>	</div>';
					
					echo '
					
					<form method="POST" action="">
						
						<div class="input-append date" id="date1" data-date-format="yyyy-mm-dd">
							<label for="debut">Date de début:</label>
							<input id="debut" type="text" name="debut" required />
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
					
						<br/>
						
						<div class="input-append date" id="date2" data-date-format="yyyy-mm-dd">
							<label for="fin">Date de fin:</label>
							<input id="fin" type="text" name="fin" required />
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						<br/>
						<input type="submit" id="lancer" name="lancer" value="Lancer" class="btn btn-primary " />
						
					</form>
					
					';
				}
                else{
                    if(!$AUvalid)
                    echo '
						<div class="alert alert-info">
							<p>La periode de pre-affection n\'est pas encore démarrée..</p>
							<form method="POST" action="">
								<input type="submit" class="btn btn-info btn-block" name="demarrer" value="Démarrer?"/>
							</form>
						</div>
					';
                    else
                        echo '
						<div class="alert alert-info">
							<p>La periode de pre-affection n\'a pas été démarrée cette année..</p>

						</div>';

                }
            }
			else{
									
					echo '
						<table id="liste1" cellpadding="0" cellspacing="0" border="0" class="liste table table-bordered">
						<thead>
							<tr>
								<th>Enseignant</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						
						<tbody>';

                        $prof_total=0;
                        $prof_valid=0;
                        $prof_non_valid=0;
						if($prof == TRUE) // from above
						while ($row = $prof->fetch_assoc())
						{
                            $prof_total++;
                            $id=$row['enseignantID'];

                            $valid_prof=0;
                            $img='';

                            $sql = 'SELECT *  FROM `fiche_souhait_valid` WHERE `fiche`='. $fiche['id'].' AND `enseignant`='.$id;;
                            $res2=$bdd->query($sql);
                          //  echo $sql;
                            if($res2==true && $res2->num_rows >0)
                            {
                                if($fiche_prof=$res2->fetch_assoc())
                                {
                                    $valid_prof=$fiche_prof['valid'];

                                    if($valid_prof==1)
                                    {
                                        $prof_valid++;
                                        $img='<img  src="img/glyphicons/glyphicons_152_check.png" alt="Validée" title="Fiche Validée" />'; //<span class="pull-right"><a><i class="icon icon-envelope icon-white" ></i></a></span>
                                    }

                                    else $img='<img src="img/glyphicons/glyphicons_150_edit.png" alt="NON Validée (Remplie)" title="Fiche modifiée (NON Validée)" />';
                                }

                            }else{

                                $sql = 'SELECT 1 FROM `fiche_souhait_details` WHERE `fiche`='. $fiche['id'].' AND `enseignantID`='.$id.' LIMIT 1';
                                $res2=$bdd->query($sql);
                                if($res2==true && $res2->num_rows >0)
                                {
                                    $valid_prof=0; //Le prof a édité la fiche mais ne l'a pas encore validé..
                                    $img='<img  src="img/glyphicons/glyphicons_150_edit.png" alt="NON Validée (remplie)" title="Fiche modifiée (NON Validée)" />';
                                }else{
                                    $valid_prof=-1;
                                    $img='<img src="img/glyphicons/glyphicons_153_unchecked.png" alt="NON Validée" title="Fiche NON Validée!" />';
                                }

                            }

                            $disable='';
                            if($valid_prof!=1)$prof_non_valid++;
                            if($valid_prof==-1) $disable='disabled';




							echo '
							<tr>
								<td>'.$row['nom'].' '.$row['prenom'].'</td>
								<td style="text-align: center;">'.$img.'</td>
								<td><center><button class="btn btn-info btn-details" data-id="'.$id.'" href="#details" '.$disable.'  data-toggle="modal"><i class=" icon-eye-open icon-white"></i> Details </button></center></td>
							</tr>
							';
						}
						echo '
						</tbody>
						</table>
						
						';


				}
				?>	
				
				</div>

			</div>








    <?php include('include/scripts.php');?>

    <?php

    if($fiche_active!=-1)
    {
        if($fiche_active==0){
            $check='checked="checked"';
            $disable="readonly";
        }
        else {
            $check='';
            $disable='';
        }
    if(!$AUvalid)
    echo '
    <!-- Modal messages : edit_fich-->

        <div id="edit_fich" class="modal hide" tabindex="-1">
                    <form method="POST" id="form-fiche" action="" style="margin:0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
						<button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>
						<h3>Modifier la fiche</h3>
			</div>
					<div class="modal-body">

                            <fieldset>
                            <div class="input-append date" id="date1" data-date-format="yyyy-mm-dd">
                                <label for="debut">Date de début:</label>
                                <input id="debut" type="text" name="debut" value="'.$fiche['debut'].'" disabled required />
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>

                            <br/>

                            <div class="input-append date" id="date2" data-date-format="yyyy-mm-dd">
                                <label for="fin">Date de fin:</label>
                                <input id="fin" type="text" name="fin" value="'.$fiche['fin'].'" '.$disable.' required />
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                            <br/>

                            <label for="valid-switch">Terminer?:</label>
                            <div class="switch"  data-off-label="Non" data-on-label="Oui" >
                                <input id="valid-switch" type="checkbox" name="valid" '.$check.' />
                            </div>
                            <br/>

                            <input type="hidden" name="fiche_id" value="'.$fiche['id'].'" required/>
                            </fieldset>


					</div>
			<div class="modal-footer">
                <input type="submit" id="modifier" name="modifier" value="Modifier" class="btn btn-primary " />
				<a href="#" class="btn" data-dismiss="modal">Fermer</a>
			</div>
                    </form>
		</div>

    <!-- END of Modal messages : edit_fich -->
    ';
        echo '
        <!-- Modal messages : details-->

			    <div id="details" class="modal hide medium-modal" tabindex="-1">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
						<button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>
						<h3 style="display:inline-block;">La fiche des souhaits:</h3>
						<span class="pull-right" ><span id="prof_name"></span><span>('.get_cur_year("annee",$bdd).')</span></span>
					</div>

					<div class="modal-body">



					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
					</div>
				</div>

			<!-- END of Modal messages : details -->


        ';

        if(!$AUvalid)
        echo '
        <!-- Modal messages : notifier-->

			    <div id="notifier" class="medium-modal modal hide  " style="" tabindex="-1">
			        <form method="post">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
						<button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>

						<h3>Notification via Email:</h3>
					</div>

					<div class="modal-body" style="">

                    <div id="template">

                    </div>
                    <label style="margin-top: 15px">DESTINATAIRES:
                        <select name="to">
                            <option value="all">Tous ('.$prof_total.')</option>
                            <option value="non_valid" selected>Non validé ('.$prof_non_valid.')</option>
                        </select>
                    </label>
					</div>
					<div class="modal-footer">
					    <input type="submit" id="envoyer" name="notifier" value="Envoyer" class="btn btn-primary " />
						<a href="#" class="btn" data-dismiss="modal">Fermer</a>
					</div>
					</form>
				</div>

			<!-- END of Modal messages : notifier -->

			<script>


            $("#notifier_btn").click(function(e) {

            var template=this.getAttribute("data-id");
            var link="load.php?get=Template&id="+template;

            $("#template").load(link);

            });
            </script>

			';
        echo '
        <script>
            $(".btn-details").click(function(e) {

                var fiche=$("#fiche").attr("data-id");
                var prof=this.getAttribute("data-id");
                 var link="load.php?get=Fiche_details&fiche="+fiche+"&prof="+prof;
                $("#details .modal-body").load(link,function(e) {

                    link="load.php?get=prof_name&id="+prof;
                    $.get( link, function( data ) {
                        $( "#prof_name" ).html( data );
                    });
                });

            });

            $(".fs").click(function(e) {

           $(".modal").toggleClass("full-screen");
           $(".modal").toggleClass("medium-modal");
            });
         </script>
        ';
    }

?>

    <script>
        var message1=document.getElementById('title').innerHTML;
        message1+=<?php echo '"  '.$CurYear_des.'"';?>;

    </script>

<?php

    if($fiche_active==-1  )
    {

?>
<script>

$(function(){

var startDate = new Date();
var endDate = new Date() ;
var d1=0;
var d2=0;
			$('#fin').attr("disabled", true);
			$("#lancer").attr("disabled", true);
			
			$('#date1').datepicker({autoclose: true, language: 'fr', todayBtn: 'linked',startDate: startDate})
			.on('changeDate', function(ev){
					d1=1;
					startDate = new Date(ev.date);
					if(d1 && d2) $("#lancer").removeAttr("disabled");
					
				$('#date2').datepicker({language: 'fr', startDate: startDate, autoclose: true})
				.on('changeDate', function(ev){
					d2=1;
					endDate = new Date(ev.date);
					if(d1 && d2) $("#lancer").removeAttr("disabled");
					$('#date1').datepicker('setEndDate', endDate);
					
				});
					$('#date2').datepicker('setStartDate', startDate);
					$("#fin").removeAttr("disabled");
					$('#fin')[0].focus();
				});
});
</script >
    <?php
    }else{
if($fiche_active==1)
{
    ?>

    <script>

        $(function(){

            var startDate = $("#debut").val();
            var d1=0;
            var d2=0;
        //    $("#lancer").attr("disabled", true);

            $('#date2').datepicker({language: 'fr', startDate: startDate, autoclose: true});
           /*     .on('changeDate', function(ev){
                    d2=1;
                    endDate = new Date(ev.date);
                    if(d1 && d2) $("#lancer").removeAttr("disabled");
                    $('#date1').datepicker('setEndDate', endDate);

                });*/
        });
    </script>

    <script type="text/javascript">
        // Prevent jQuery UI dialog from blocking focusin
        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });
    function loadeditor() {

            tinymce.init({
            selector: "textarea",
            language : 'fr_FR',
            dialog_type : "modal",
            plugins: [
                "advlist autolink lists link image charmap preview ",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime table contextmenu paste"
            ],
            convert_fonts_to_spans : false,
            toolbar: "undo redo | styleselect fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code"

        });
        $(".editorloader").remove();
    }


    </script>

<?php
}
    }
    include('include/footer.php');
} else {
    header('location:login.php');
}
?>