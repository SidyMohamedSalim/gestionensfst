<?php

    define("_VALID_PHP", true);
    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active='M2';
    $script = array("editable", "scripts", "editor");
    include('include/header.php');
    /* ToDo: definir une methode isAdmin est l'inclure dans le test de login
          ToDo: redirection vers une page d'erreur avec message...

       */

    if($access != _ADMIN) header('location:index.php');

    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            <li class="active">email</li>
            </li>
        </ul>
    </div>

    <div class="well">

        <div class="well well-small" >
            <h3 style="display:inline-block;">Gestion des maquettes Email</h3>
        </div>
        <!--
                <div class="well" >


                    <label for="sujet">Sujet:</label>
                    <input type="text" name="sujet" id="sujet"/>
                    <label> Message:
                        <textarea style="display:block;width: 95%;" name="body" ></textarea>
                    </label>

                </div>
        -->
        <?PHP
        if(isset($_POST['modifier']))
        {
            if(!empty($_POST['sujet']) && !empty($_POST['corp']) && is_int((int)$_POST['id']))
            {
                include_once('include/htmLawed.php');


                $_POST['sujet']=sanitize($_POST['sujet']);
                $_POST['corp']=addslashes(htmLawed(stripcslashes($_POST['corp']),array('safe'=>1, 'css_expression'=>1)));

               if(isRowExistant($bdd,"email_template", $_POST['id']))
               {
                   $sql='UPDATE `email_template` SET `sujet`="'.$_POST['sujet'].'",`corp`="'.$_POST['corp'].'" WHERE `id`='.$_POST['id'];
           //        print_r($_POST);
                   if($bdd->query($sql))echo '<p class="alert alert-block alert-success" >Mis à jours avec succès!</p>';
                   else echo '<p class="alert alert-block alert-error" >Probleme lors de la mise à jour!!</p>';

               }else  echo '<p class="alert alert-block alert-error" >Id non valide!!</p>';
            }else{
                echo '<p class="alert alert-block alert-error" >Probleme dans les données!!</p>';
            }
        }

        $sql="SELECT `id`,`type` FROM `email_template`";
        $templates=$bdd->query($sql);

        ?>

        <div class="well" >

            <h3>Liste des maquettes</h3>
            <table cellpadding="0" cellspacing="0" border="0" class=" table table-bordered" >
                <thead>
                <tr>
                    <th width=20 >#</th>
                    <th>Nom de la maquette</th>
                    <th  width=25 >Edit</th>
                </tr>
                </thead>
                <tbody>
                <?php if($templates->num_rows==0):?>
                    <tr>
                        <td colspan="3"><?php echo '<div class="alert alert-error">Erreur!</br>aucune maquette disponible..</div>';?></td>
                    </tr>
                <?php else:?>
                    <?php


                    while ($row=$templates->fetch_assoc()):

                        ?>
                        <tr>
                            <td><?php echo $row['id'];?>.</td>
                            <td><?php echo $row['type'];?></td>
                            <td  width=25 ><a class="btn-details" href="#template" data-toggle="modal" data-id=<?php echo $row['id'];?> data-keyboard="true"><img src="img/glyphicons/glyphicons_150_edit.png" alt="" class="" title="Edit: <?php echo $row['type'];?>"/></a></td>
                        </tr>
                    <?php endwhile;?>
                    <?php unset($row);?>
                <?php endif;?>
                </tbody>
            </table>

        </div>
    </div>



    <!-- Modal messages : notifier-->

    <div id="template" class="medium-modal modal hide  " style="" tabindex="-1">
        <form method="post" action="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i></button>
                <button type="button" class="close fs" aria-hidden="true"><i class="icon-fullscreen"></i></button>

                <h3>Modification de la maquette Email:</h3>
            </div>

            <div class="modal-body" style="">

            </div>
            <div class="modal-footer">
                <input type="submit" id="envoyer" name="modifier" value="Modifier" class="btn btn-primary " />
                <a href="#" class="btn" data-dismiss="modal">Fermer</a>
            </div>
        </form>
    </div>

    <!-- END of Modal messages : notifier -->

    <script>
        $(".btn-details").click(function(e) {

            var template=this.getAttribute("data-id");
            var link="load.php?get=Template&id="+template;

            $("#template .modal-body").load(link);

        });

        $(".fs").click(function(e) {

            $(".modal").toggleClass("full-screen");
            $(".modal").toggleClass("medium-modal");
        });

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

    include('include/footer.php');
} else {
    header('location:login.php');
}
?>