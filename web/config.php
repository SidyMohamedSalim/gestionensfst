<?php

define("_VALID_PHP", true);
include 'include/connexion_BD.php';
include 'include/fonctions.php';
// Include database connection and functions here.
sec_session_start();
if (login_check($bdd) == true) {

    // Add your protected page content here!
    $active = 'M2';
    $script = array("editable", "scripts");
    include('include/header.php');
    /* ToDo: definir une methode isAdmin est l'inclure dans le test de login
          ToDo: redirection vers une page d'erreur avec message...

       */

    if ($access != _ADMIN) header('location:index.php');


    if (isset($_POST['Enregistrer'])) {
        if (!empty($_POST['site_name']) && !empty($_POST['smtp_host']) && !empty($_POST['smtp_user']) && !empty($_POST['smtp_pass']) && !empty($_POST['smtp_port'])) {
            $sql = "UPDATE `configuration_globale` SET `valeur`='" . sanitize($_POST['site_name']) . "' WHERE `param`='site_name'";
            $bdd->query($sql);
            $sql = "UPDATE `configuration_globale` SET `valeur`='" . sanitize($_POST['smtp_host']) . "' WHERE `param`='smtp_host'";
            $bdd->query($sql);
            $sql = "UPDATE `configuration_globale` SET `valeur`='" . sanitize($_POST['smtp_user']) . "' WHERE `param`='smtp_user'";
            $bdd->query($sql);
            $sql = "UPDATE `configuration_globale` SET `valeur`='" . sanitize($_POST['smtp_pass']) . "' WHERE `param`='smtp_pass'";
            $bdd->query($sql);
            $sql = "UPDATE `configuration_globale` SET `valeur`='" . sanitize($_POST['smtp_port']) . "' WHERE `param`='smtp_port'";
            $bdd->query($sql);
        } else echo '<p class="alert alert-block alert-error" >Probleme dans les données!!</p>';
    }

?>

    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            <li class="active">Cofiguration</li>
            </li>
        </ul>
    </div>

    <div class="well">

        <div class="well well-small">
            <h3 style="display:inline-block;">CONFIGURATION:</h3>
        </div>

        <div class="well">
            <h4>Variables:</h4><br />
            <form method="POST" action="">
                <fieldset>

                    <label>Titre du site: <input type="text" name="site_name" value="<?php echo getValue("valeur", "configuration_globale", "param='site_name'", $bdd); ?>" /></label>
                    <label>SMTP HSOT: <input type="text" name="smtp_host" value="<?php echo getValue("valeur", "configuration_globale", "param='smtp_host'", $bdd); ?>" /></label>
                    <label>SMTP USER: <input type="text" name="smtp_user" value="<?php echo getValue("valeur", "configuration_globale", "param='smtp_user'", $bdd); ?>" /></label>
                    <label>SMTP PASS: <input type="password" name="smtp_pass" value="<?php echo getValue("valeur", "configuration_globale", "param='smtp_pass'", $bdd); ?>" /></label>
                    <label>SMTP PORT: <input type="number" name="smtp_port" value="<?php echo getValue("valeur", "configuration_globale", "param='smtp_port'", $bdd); ?>" /></label>

                    <br />
                    <input type="submit" class="btn" name="Enregistrer" value="Enregistrer" />
                </fieldset>

            </form>

        </div>


    </div>










    <?php include('include/scripts.php'); ?>

    <script>
        $(function() {
            $('.grade_permission').editable({
                url: 'process_gestion.php?edit=grade',

                source: [{
                        value: 1,
                        text: 'Cours'
                    },
                    {
                        value: 2,
                        text: 'TD'
                    },
                    {
                        value: 3,
                        text: 'TP'
                    }
                ],
                title: 'Permission?',
                validate: function(value) {
                    if ($.trim(value) == '') {
                        return 'Il faut choisir un au moins !';
                    }
                },
                success: function(response) {
                    if (response) return response;
                }
            });
        });
        $('.grade_code').editable({
            type: 'text',
            url: 'process_gestion.php',
            title: 'Entrer le code',
            validate: function(value) {
                if ($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            success: function(response) {
                if (response) return response;
            }
        });
        $('.grade_designation').editable({
            type: 'text',
            url: 'process_gestion.php',
            title: 'Entrer la designation',
            inputclass: 'input-xlarge',
            validate: function(value) {
                if ($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            success: function(response) {
                if (response) return response;
            }
        });
        $('.grade_charge').editable({
            type: 'number',
            url: 'process_gestion.php',
            title: 'Entrer la charge Annuelle',
            validate: function(value) {
                if ($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            },
            /*	ajaxOptions: {
            		dataType: 'json' //assuming json response
            		},
            	success: function(response, newValue) {
            //	if(!response.success) return response.msg;
            		} */
            success: function(response) {
                if (response) return response;
            }
        });
    </script>
<?php

    include('include/footer.php');
} else {
    header('location:login.php');
}
?>