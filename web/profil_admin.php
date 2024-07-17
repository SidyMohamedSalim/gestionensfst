<?php
/**
 * User: DC
 * Date: 22/11/13
 * Time: 01:00
 */
    define("_VALID_PHP", true);
    include_once 'include/connexion_BD.php';
    include_once 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
if(login_check($bdd) == true) {

    // Add your protected page content here!
    $active='M1';
    $script = array("editable", "scripts");
    include('include/header.php');
    if($access != _DOYEN) header('location:index.php');
    ?>
    <!-- afin d'envoyée le mot de passe hashé... -->
    <script type="text/javascript" src="scripts/sha512.js"></script>
    <script>
        function validateForm(form)
        {
            form.oldPass.value=hex_sha512(form.ancienPass.value);
            form.newPass.value=hex_sha512(form.nouvPass.value);
            form.newPass1.value=hex_sha512(form.nouvPass1.value);
            form.ancienPass.value = "";
            form.nouvPass.value = "";
            form.nouvPass1.value = "";

            return true;
        }
    </script>
    <!--Body content-->
    <!-- horizontal nav -->

    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">Profil</li>
        </ul>
    </div>

    <?PHP



    $errors = array();

    $sql='SELECT `adminID` FROM `user` WHERE `id`='.$_SESSION['user_id'];
    $res = $bdd->query($sql);
    if($res == TRUE && $res->num_rows >0)
    {
        $row=$res->fetch_assoc();
        $admin_id=$row['adminID'];
    }
    else array_push($errors, "id");


    if(isset($_POST['action']))
    {


        if($_POST['action']=="edit_info")
        {
            $success_info=0;
            if(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['username']) && isset($_POST['email']) )
            {
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                    array_push($errors, "email");

                if(empty($_POST['nom'])) array_push($errors, "nom");
                if(empty($_POST['prenom'])) array_push($errors, "prenom");
                if(empty($_POST['username'])) array_push($errors, "username_empty");
                else{
                    $sql='SELECT id FROM `user` WHERE `login`="'.$_POST['username'].'" AND NOT id='.$_SESSION['user_id'];
                    //
                    $res = $bdd->query($sql);
                    if($res== FALSE || $res->num_rows >0) array_push($errors, "username_exist");
                }
                $sql='SELECT login FROM `user` WHERE  id='.$_SESSION['user_id'];
                //
                $res = $bdd->query($sql);
                if($res== TRUE)
                {
                    $row=$res->fetch_assoc();
                    if($_POST['username']!=$row['login'])
                        if(!substr_compare($_POST['username'], "user", 0, 3)) array_push($errors, "user");
                }
                if(empty($errors))
                {
                    $sql='UPDATE `user` SET `login`="'.$_POST['username'].'" ,`email`="'.$_POST['email'].'" WHERE `id`='.$_SESSION['user_id'];

                    $res = $bdd->query($sql);
                    if($res == TRUE )
                    {
                        $sql='UPDATE `admin` SET `email`="'.$_POST['email'].'",`nom`="'.$_POST['nom'].'",`prenom`="'.$_POST['prenom'].'" WHERE `id`='.$admin_id;
                        $res = $bdd->query($sql);
                        if($res==false) array_push($errors, "bdd");
                        else $success_info=1;
                    }else array_push($errors, "bdd");

                }
            }else array_push($errors, "data");
        }
        elseif($_POST['action']=="edit_pass")
        {
            $errors_pass = array();
            $success_pass = array("no");
            if(!empty($_POST['oldPass']) && !empty($_POST['newPass']) && !empty($_POST['newPass1']) )
            {
                if($_POST['newPass']==$_POST['newPass1'])
                {

                    if ($stmt = $bdd->prepare("SELECT id, login, pass, salt FROM user WHERE id = ? LIMIT 1"))
                    {
                        $password = $_POST['oldPass'];
                        $stmt->bind_param('s',$_SESSION['user_id'] ); // Bind "login" to parameter.
                        $stmt->execute(); // Execute the prepared query.
                        $stmt->store_result();
                        $stmt->bind_result($user_id, $username, $db_password, $salt); // get variables from result.
                        $stmt->fetch();

                        $password = hash('sha512', $password.$salt); // hash the password with the unique salt.

                        if($db_password == $password) // Check if the password in the database matches the password the user submitted.
                        {

                            $password = $_POST['newPass'];
                            // Create a random salt
                            $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
                            // Create salted password (Careful not to over season)
                            $password = hash('sha512', $password.$random_salt);

                            // Add your insert to database script here.
                            // Make sure you use prepared statements!
                            if ($insert_stmt = $bdd->prepare('UPDATE `user` SET pass = ?, salt= ? WHERE id= ?'))
                            {
                                $insert_stmt->bind_param('sss', $password, $random_salt, $_SESSION['user_id'] );
                                // Execute the prepared query.
                                $insert_stmt->execute();

                                array_push($success_pass, "yes");

                                //change session
                                $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

                                $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
                                $_SESSION['user_id'] = $user_id;
                                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
                                $_SESSION['username'] = $username;
                                $_SESSION['login_string'] = hash('sha512', $password.$user_browser);


                            }else array_push($errors_pass, "bdd");

                        }else array_push($errors_pass, "oldPass");

                    }else array_push($errors_pass, "bdd5");

                }else array_push($errors_pass, "newPass");

            }else array_push($errors_pass, "pass_post");
        }
    }
    // get user details
    if(!in_array("id",$errors))
    {
        $sql='SELECT `nom`, `prenom` FROM `admin` WHERE `id`='.$admin_id;
        $res = $bdd->query($sql);
        if($res== TRUE || $res->num_rows >0)
        {
            $prof_details=$row=$res->fetch_assoc();
        }else array_push($errors, "prof_details");

        $sql='SELECT `login`,`email` FROM `user` WHERE `id`='.$_SESSION['user_id'];
        $res = $bdd->query($sql);
        if($res== TRUE || $res->num_rows >0)
        {
            $user_details=$row=$res->fetch_assoc();
        }else array_push($errors, "user_details");

        $res->free();

        //	print_r($errors);
    }

    /*
    // The hashed password from the form
    ;
    $_POST['value']*/
    ?>


    <div class="well">



        <div class="row-fluid">
            <div class="span12">
                <div class="well well-small">
                    <h3><i style="vertical-align: middle;margin-bottom: 5px;" class="icon icon-user"></i> Mon Profile</h3>
                </div>

                <div >
                    <ul class="nav nav-tabs">
                        <li class="<?PHP if(empty($success_pass)) echo 'active';?>"><a href="#profile" data-toggle="tab">Profile</a></li>
                        <li <?PHP if(!empty($success_pass)) echo 'class="active"';?>><a href="#password" data-toggle="tab">Mot de passe</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?PHP if(empty($success_pass)) echo 'active';?>" id="profile"><!-- Profile -->
                            <div class="row-fluid">
                                <div class="span10">
                                    <?php
                                    if(!empty($success_info) && $success_info==1)
                                        echo '<div class="alert alert-success">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													Infos mises à jour avec <strong>Succès!</strong>
												</div>';
                                    if(!empty($errors) || (!empty($success_info) && $success_info==0)){
                                        echo '<div class="alert alert-error">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													<strong>ERREUR!</strong>';
                                        if(in_array("nom",$errors)) echo ' nom vide..<br/>';
                                        if(in_array("prenom",$errors)) echo ' prenom vide..</br>';
                                        if(in_array("username_empty",$errors)) echo ' nom d\'utilisateur vide..</br>';
                                        if(in_array("username_exist",$errors)) echo ' ce nom d\'utilisateur ('.$_POST['username'].') existe..';
                                        if(in_array("bdd",$errors)) echo ' Problème dans les requêtes..';
                                        if(in_array("data",$errors)) echo ' Problème dans l\'envoie des données..';
                                        if(in_array("email",$errors)) echo ' email non valide..';
                                        echo '</div>';
                                    }

                                    ?>
                                    <form class="form-horizontal" method="post" action="">
                                        <input type="hidden" name="action" value="edit_info"/>
                                        <div class="control-group">
                                            <label class="control-label" for="nom">Nom</label>
                                            <div class="controls">
                                                <input class="input-xlarge"  name="nom" value="<?PHP if(!in_array("prof_details",$errors)) echo $prof_details['nom'];?>" id="nom" type="text" REQUIRED>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="prenom">Prenom</label>
                                            <div class="controls">
                                                <input class="input-xlarge"  name="prenom" value="<?PHP if(!in_array("prof_details",$errors)) echo $prof_details['prenom'];?>" id="prenom" type="text" REQUIRED>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="username">Nom d'utilisateur</label>
                                            <div class="controls">
                                                <input class="input-xlarge" name="username" value="<?PHP if(!in_array("user_details",$errors)) echo $user_details['login'];?>" id="username" type="text" REQUIRED>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label" for="email">Email</label>
                                            <div class="controls">
                                                <input class="input-xlarge" value="<?PHP if(!in_array("prof_details",$errors)) echo $user_details['email'];?>" name="email" id="email" type="email" REQUIRED>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="controls">
                                                <input name="submit" value="Enregistrer" class="btn" type="submit">
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div> <!-- profle -->

                        <div class="tab-pane <?PHP if(!empty($success_pass)) echo 'active';?>" id="password"><!-- Password -->
                            <?php
                            if(!empty($success_pass) && in_array("yes",$success_pass))
                                echo '<div class="alert alert-success">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													Mot de passe changé avec <strong>Succès!</strong>
												</div>';
                            if(!empty($errors_pass))
                            {
                                echo '<div class="alert alert-error">
													<button type="button" class="close" data-dismiss="alert">&times;</button>
													<strong>ERREUR!</strong>';
                                if(in_array("bdd",$errors_pass)) echo ' Problème dans les requêtes..';
                                if(in_array("pass_post",$errors_pass)) echo ' Problème dans données envoyées..';
                                if(in_array("newPass",$errors_pass)) echo ' Confirmation du nouveau mot de passe: non identique..';
                                if(in_array("oldPass",$errors_pass)) echo ' L\'ancien mot de passe est incorrecte..';
                                echo '</div>';
                            }

                            ?>
                            <form class="form-horizontal" method="post" action="" onsubmit="return validateForm(this)">
                                <input type="hidden" name="action" value="edit_pass"/>
                                <div class="control-group">
                                    <label class="control-label" for="ancienPass">Mot de passe ancien</label>
                                    <div class="controls">
                                        <input class="input-xlarge" type="password" id="ancienPass" name="ancienPass" REQUIRED>
                                        <input type="hidden" name="oldPass" value="" >
                                    </div>
                                </div>
                                <div class="control-group" >
                                    <label class="control-label" for="nouvPass">Nouveau Mot de passe</label>
                                    <div class="controls">
                                        <input class="input-xlarge" type="password" id="nouvPass" name="nouvPass" REQUIRED>
                                        <input type="hidden" name="newPass" value="" >
                                    </div>
                                </div>
                                <div class="control-group" >
                                    <label class="control-label" title="Retaper le nouveau mot de passe" for="nouvPass1">Retapper</label>
                                    <div class="controls">
                                        <input class="input-xlarge" type="password" id="nouvPass1" name="nouvPass1" REQUIRED>
                                        <input type="hidden" name="newPass1" value="" >
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <input name="submit" value="Enregistrer" class="btn" type="submit">
                                </div>
                            </form>
                        </div> <!-- password -->
                    </div><!-- tab-content-->
                </div> <!-- end tabbable -->

            </div>
        </div> <!-- end row -->






    </div>	 <!-- fin well -->






    <?php

    include('include/scripts.php');
    include('include/footer.php');
    ?>
<?php
} else {
    header('location:login.php');
}
?>