<script src="scripts/bootstrap.min.js"></script>
<?php
        if(in_array("switch",$script)) echo '<script src="scripts/bootstrapSwitch.js"></script>'."\n";

        if(in_array("datatable",$script)) echo '<script src="plugins/dataTables/media/js/jquery.dataTables.min.js"></script>'."\n";

        if(in_array("datatable",$script)) echo '<script src="plugins/dataTables/media/js/dataTables.bootstrap.js"></script>'."\n";

        if(in_array("datatable",$script)) echo '<script src="plugins/dataTables/extras/TableTools/media/js/ZeroClipboard.js"></script>'."\n";

        if(in_array("datatable",$script)) echo '<script src="plugins/dataTables/extras/TableTools/media/js/TableTools.js"></script>'."\n";
        if(in_array("datatable",$script)) echo '<script src="plugins/dataTables/media/js/datatables.responsive.js"></script>'."\n";

        if(in_array("editable",$script) ) echo '<script src="scripts/bootstrap-editable.min.js"></script>'."\n";

        if(in_array("wizard",$script)) echo '<script src="scripts/bootstrap-wizard.js"></script>'."\n";

        if(in_array("select2",$script)) echo '<script src="scripts/select2.min.js"></script>'."\n";
        if(in_array("select2",$script)) echo '<script src="scripts/select2_locale_fr.js"></script>'."\n";

        if(in_array("scripts",$script)) echo '<script src="scripts/scripts.js"></script>'."\n";

        if(in_array("preparation",$script)) echo '<script src="scripts/script_preparation.js"></script>'."\n";

        if(in_array("affectation",$script)) echo '<script src="scripts/script_affectation.js"></script>'."\n";

        if(in_array("affectation_prof",$script)) echo '<script src="scripts/script_affectation_prof.js"></script>'."\n";

        if(in_array("script_module",$script)) echo '<script src="scripts/script_module.js"></script>'."\n";

        if(in_array("script_enseignant",$script)) echo '<script src="scripts/script_enseignant.js"></script>'."\n";

        if(in_array("script_fiches",$script)) echo '<script src="scripts/script_fiches.js"></script>'."\n";

        if(in_array("script_conflit",$script)) echo '<script src="scripts/script_conflit.js"></script>'."\n";

        if(in_array("date",$script)) echo '<script src="scripts/bootstrap-datepicker.js"></script>'."\n";

        if(in_array("date",$script)) echo '<script src="scripts/bootstrap-datepicker.fr.js" charset="UTF-8"></script>'."\n";

        if(in_array("editor",$script)) echo '<script src="plugins/tinymce/tinymce.min.js" charset="UTF-8"></script>'."\n";

/*
        $sql="SELECT `valid` FROM `annee_universitaire` WHERE `annee_UniversitaireID`=".get_cur_year("id",$bdd);
        $res=$bdd->query($sql);
        if($res==TRUE)
        {
            if($row = $res->fetch_assoc())
            {
                if($row['valid']==1)
                    echo "<script>  $('.editable').editable('toggleDisabled');</script>";
            }
        }
*/
        $length=getValue('elementParPage','configuration','user='.$_SESSION['user_id'],$bdd);


        if(!empty($length))
            echo '
            <script>
                var length = '.$length.';
            </script>
            ';

        if(!empty($theme))
            echo '
            <script>
                var current_theme = "'.$theme.'";
            </script>
            ';
        if(isAUvalid($bdd) && !isset($AU_page))
            echo '
            <script>
                $(document).ready(function() {
                    $(".editable").not(".annee").editable("toggleDisabled");
                });

            </script>
            ';
?>

