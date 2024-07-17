<?php
    define("_VALID_PHP", true);
    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active=1.2;
    $script = array("datatable","editable","scripts");
    include('include/header.php');


    if($access != _ADMIN && $access != _DOYEN) header('location:index.php');
    $CurYear_des=get_cur_year("des",$bdd);


    ?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            </li>
            <li class="active" >
                Annèe universitaire
            </li>
        </ul>
    </div>

    <!-- -->

    <div class="well">

        <div class="well well-small" >
            <h3 id="title" style="display:inline-block;">Années Universitaire</h3>
            <a style="margin-top:8px;" class="btn btn-success btn-large pull-right" href="#annee" data-toggle="modal" ><i class=" icon-pencil icon-white"></i> Nouvelle </a>
        </div> <!-- Fin well-small-->





        <div class="well well-small" >

            <table id="listeAU" class="liste table table-bordered">
                <thead>
                    <tr>
                        <th width="20" > #</th>
                        <th> Annèe universitaire</th>
                        <th> VALIDE</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                if ($stmt = $bdd->prepare("SELECT `annee_UniversitaireID`,`annee_univ`,`valid` FROM `annee_universitaire` ORDER BY `annee_UniversitaireID` DESC")) {
                    //    $stmt->bind_param('i', $var);
                    // Execute the prepared query.
                    $stmt->execute();
                    $stmt->store_result();

                    if($stmt->num_rows > 0) {
                        $stmt->bind_result($au_id,$au_des,$au_valid); // get variables from result.
                        while($stmt->fetch())
                        {
                            $au_v=($au_valid==1)?"OUI":"NON";
                            echo "
                        <tr>
                            <td class=\"center\" >".$au_id."</td>
                            <td class=\"center\" ><span class=\"au_description\" data-name=\"au_description\" data-pk=\"".$au_id."\">".$au_des."</span></td>
                            <td class=\"center\" ><span class=\"au_valid label label-".label($au_v)."\" data-value=\"".$au_valid."\" data-name=\"au_valid\" data-pk=\"".$au_id."\">".$au_v."</span></td>
                        </tr>\n
                        ";
                        }


                    } else {
                        //   return false;
                    }
                }

                ?>
                </tbody>
            </table>

        </div>

    </div>


    <!-- Modal messages : Nouvelle Année Universitaire-->
    <div id="annee" class="modal hide fade">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Nouvelle Année Universitaire</h3>
        </div>
        <div class="modal-body">
            <form method="POST" action="process_gestion.php">
                <fieldset>
                    <input type="hidden" name="op" value="ajouter"/>
                    <input type="hidden" name="type" value="annee"/>
                    <label for="annee_A">ANNEE UNIVERSITAIRE</label>
                    <?php
                        $sql="SELECT `annee_univ` FROM `annee_universitaire` ORDER BY `annee_UniversitaireID` DESC LIMIT 1";
                        $res = $bdd->query($sql);
                        $last = date("Y");

                        if($res == true && $res->num_rows>0)
                        {
                            if($row = $res->fetch_assoc())
                            {
                                $last=(int)substr(strchr($row['annee_univ'], '-'),1);
                            }
                        }

                    ?>
                    <input type="text" name="annee" value="<?php echo $last.'-'.($last+1);?>" autofocus="autofocus" data-mask="9999-9999" id="annee_A" required>
                    <br/>
                    <input type="submit" value="Ajouter" class="btn btn-primary">

                </fieldset>
            </form>
        </div>

    </div>
    <!-- END of Modal messages : Nouvelle Année Universitaire -->


    <?php
            $AU_page=1;
            include('include/scripts.php');
    ?>
    <script src="scripts/bootstrap-inputmask.js"></script>
    <script>
        $('.au_description').editable({
            type: 'text',
            url: 'process_gestion.php',
            title: 'Modifier l\'annèe',
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
        $('.au_valid').editable({
            type: 'select',
            url: 'process_gestion.php',
            source: '[{value: 1, text: "OUI"}, {value: 0, text: "NON"}]',
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
        /* Table initialisation */
        $(document).ready(function() {


            if( typeof message1 === 'undefined' )
                message1="";
            if( typeof message2 === 'undefined' )
                message2="";
            $('#listeAU').dataTable( {
                "aaSorting": [[ 0, "desc" ]],
                "aoColumns": [
                    null,
                    null,
                    null

                ]
            } );

            /* mask annee universitaire */
            $('#annee_A').inputmask();
        } );

    </script>
    <?php

    include('include/footer.php');
} else {
    header('location:login.php');
}
?>