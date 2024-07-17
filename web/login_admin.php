<?php
define("_VALID_PHP", true);
include_once 'include/connexion_BD.php';
include_once 'include/fonctions.php';

sec_session_start();
if(login_check($bdd)) {
    header('Location: ./');
}else session_destroy();

?>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Gestion des Service</title>
    <link rel="stylesheet" href="styles/login.css"/>
    <script type="text/javascript" src="scripts/sha512.js"></script>
    <script>
        function validateForm(form)
        {
            form.p.value=hex_sha512(form.password.value);
            form.password.value = "";
            return true;
        }
    </script>
</head>

<body>
<p class="switch"><a href="login.php" title="Enseignants"> Espace Enseignants</a></p>
<div class="container">
    <?php
    $errors = '';
    if(isset($_POST['lostPass'])):
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            /*	include 'connexion_BD.php';
                include 'fonctions.php';
        */
            $sql='SELECT * FROM `user` WHERE `adminID` IS NOT NULL AND `email`="'.$_POST['email'].'"';

            $res = $bdd->query($sql);
            if($res == true && $res->num_rows ==0)
            {
                $sql='SELECT * FROM `admin` WHERE `email`="'.$_POST['email'].'"';
                $res = $bdd->query($sql);
                if($res == true && $res->num_rows ==0)
                    $errors .="<p>Il n'existe aucun compte relié à cette adresse!!</p>";
                else
                    $errors .="<p>Vous n'avez pas de compte encore!</br> Merci de notifier le responsable..</p>";
            }
            else{
                //cas de l'echec de la premiere requete
                if($res == FALSE){
                    $errors .="<p>Erreur interne!!</p>";
                    goto form;
                }
                //etre sure que l'utilisateur est unique
                if($res->num_rows !=1){
                    $errors .="<p>Probleme interne: utilisateur non unique!!</p>";
                    goto form;
                }

                $user = $res->fetch_assoc();

                include('include/pwgen.class.php');
                $pwgen = new PWGen();
                $pass = $pwgen->generate();

                $password = hash('sha512', $pass);
                $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
                // Create salted password (Careful not to over season)
                $password = hash('sha512', $password.$random_salt);

                if ($insert_stmt = $bdd->prepare("UPDATE `user` SET `pass`= ? , `salt`= ? WHERE `id`= ? ")) {
                    $insert_stmt->bind_param('sss', $password, $random_salt, $user['id']);
                    // Execute the prepared query.
                    $insert_stmt->execute();

                    $message['adresse']=sanitize($user['email']);
                    $message['nom']=sanitize($user['login']);
              //      $message['sujet']="Nouveau mot de passe (GS_FSTF)";
              //     $message['corp']="<ul><li>login: ".$user['login']." </li><li>Mot de passe: ".$pass."</li></ul>";

                    $row = getRowById($bdd, "email_template", 4);

                    if($row!=false)
                    {
                        $temp=$row->fetch_assoc();
                        $message['sujet'] = sanitize((string) $temp['sujet']);
                        $message['corp']= ($temp['corp']); //strip_tags

                        $message['corp'] = str_replace(array('[login]', '[pass]'),
                            array($user['login'], $pass), $message['corp']);
                    }

                    if(envoi_mail($message,$bdd)) $errors .="<p>Un email contenant vos nouveaux crédentiels vient d'être envoyer à votre adresse.. </p>";
                    else $errors .="<p>Probleme lors de l'envoi de l'email!!</p>";

                }else $errors .="<p>Probleme interne!!!</p>";
            }

        }else $errors .="<p>Cette adresse email est invalide!!</p>";

        form:
        ?>
        <div class="login">
            <h1>Mot de passe oublié: </h1>
            <?php
            //if(empty($errors)) echo '<p>Un email contenant vos nouveaux crédentiels vient d\'être envoyer à votre adresse.. </p>';
            echo '<center>'.$errors.'</br></center>';
            ?>
        </div>

        <div class="login-help">
            <p>Pour retourner sur la page de connexion? <a href="login_admin.php">Cliquez ici</a>.</p>
        </div>

    <?php
    elseif(isset($_GET['lostPass'])):
        ?>
        <div class="login">
            <h1>Mot de passe oublié: </h1>
            <form method="post" action="" >
                <p><input type="email" name="email" value="" autofocus="autofocus" placeholder="votre email" required></p>

                <input type="hidden" name="p" value="" >
                <p class="submit"><input type="submit" name="lostPass" value="Envoyer"></p>
            </form>
        </div>

        <div class="login-help">
            <p>Pour retourner sur la page de connexion? <a href="login_admin.php">Cliquez ici</a>.</p>
        </div>

    <?php
    else:
        if(isset($_GET['error'])) {
            echo '<div class="error">Problème lors de l\'authentification: </br>';
            if($_GET['error']==1) echo ' (5 essais erronés! votre compte est bloqué pour 15 min)';
            elseif($_GET['error']==2) echo '(Mot de passe incorrecte)';
            elseif($_GET['error']==3) echo ' (Utilisateur inexistant)';
            elseif($_GET['error']==4) echo ' (Problème dans les données)';
            //   elseif($_GET['error']==5) echo ' ()';
            elseif($_GET['error']==0) echo ' (Il faut remplir les champs!)';
            // else
            echo '</div>';

        }
        ?>
        <div class="login">
            <h1>Connexion: Gestion des services (Administrateur)</h1>
            <form method="post" action="process_login.php" onsubmit="return validateForm(this)">
                <p><input type="text" name="login" value="" autofocus="autofocus" placeholder="Login" required></p>
                <p><input type="password" name="password" value="" placeholder="Mot de Passe" required></p>
                <p><input type="hidden" name="admin" value=""></p>
                <p class="remember_me">
                    <label>
                        <input type="checkbox" name="remember_me" id="remember_me">
                        Se souvenir de moi
                    </label>
                </p>
                <input type="hidden" name="p" value="" >
                <?php

                if(isset($_GET['location'])) {
                    echo '<input type="hidden" name="location" value="';
                    echo htmlspecialchars($_GET['location']);
                    echo '" />';
                }

                ?>
                <p class="submit"><input type="submit" name="commit" value="Login"></p>
            </form>
        </div>

        <div class="login-help">
            <p>Mot de passe oublié? <a href="login_admin.php?lostPass">Cliquez ici pour le réinitialiser</a>.</p>
        </div>


    <?php
    endif
    ?>

</div>

<noscript>
    <div id="noscript-warning">Javascript est essentiel pour le bon fonctionnement du site!!</div>
</noscript>
</body>
</html>