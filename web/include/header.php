<?php
if (!defined("_VALID_PHP"))
    die('L\'accès directe a cette page est interdit!');
if(!isset($active))exit;

$theme=getValue('theme','configuration','user='.$_SESSION['user_id'],$bdd);
if(empty($theme)) $theme='classic';
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo getValue("valeur","configuration_globale","param='site_name'",$bdd);?></title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap -->


		<link id="bs-css" href="styles/bootstrap-<?php echo $theme;?>.min.css" rel="stylesheet">
		<link href="styles/bootstrap-responsive.min.css" rel="stylesheet">

        <?php if(in_array("datatable",$script)) echo '<link href="plugins/dataTables/media/css/jquery.dataTables.css" rel="stylesheet" >';?>

        <?php if(in_array("datatable",$script)) echo '<link href="plugins/dataTables/media/css/dataTables.bootstrap.css" rel="stylesheet" >';?>
        <?php if(in_array("datatable",$script)) echo '<link href="plugins/dataTables/media/css/datatables.responsive.css" rel="stylesheet" >';?>

        <?php if(in_array("datatable",$script)) echo '<link href="plugins/dataTables/extras/TableTools/media/css/TableTools.css" rel="stylesheet" >';?>

        <?php if(in_array("wizard",$script)) echo '<link href="styles/bootstrap.wizard.css" rel="stylesheet" >';?>

        <?php if(in_array("select2",$script)) echo '<link href="styles/select2.css" rel="stylesheet" >';?>
        <?php if(in_array("select2",$script)) echo '<link href="styles/select2-bootstrap.css" rel="stylesheet" >';?>

		<link href="styles/bootstrap.editable.css" rel="stylesheet">

        <?php if(in_array("switch",$script)) echo '<link href="styles/bootstrapSwitch.css" rel="stylesheet">';?>

        <?php if(in_array("date",$script)) echo '<link href="styles/datepicker.css" rel="stylesheet">';?>
		
		
		<link href="styles/style.css" rel="stylesheet" >
		
		<script src="scripts/jquery-1.11.1.min.js"></script>
		
		
    </head>
    <body>
		<header>
            <?php
            $access= get_user_access($_SESSION['user_id'],$bdd);
            ?>
		<!--  barre de navigation haute  -->
		    <div class="navbar">
				<div class="navbar-inner">
				<a class="brand" href="#">Gestion Services</a>
				<ul class="nav pull-right">
                    <?php
                    if($access==_ADMIN)
                    {
                    ?>
					<li class="dropdown <?php if($active=='M2')echo 'active';?>">
						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-cog"></i>
							<span class="hidden-phone">Configuration</span>
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
                            <li><a href="configUsers.php"><i class="icon-wrench"></i> Gestion d'utilisateurs</a></li>
							<li><a href="config.php"><i class="icon-cog"></i> Configuration avancée</a></li>
                            <li><a href="config_email.php"><i class="icon-envelope"></i> Maquettes email</a></li>

							<li class="divider"></li>
							<li><a href="javascript:;">Aide</a></li>
						</ul>
						
					</li>
                    <?php
			        };
                    ?>
					<li class="dropdown <?php if($active=='M1')echo 'active';?>">
						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user"></i>
                            <span class="hidden-phone">
							<?php echo $_SESSION['username'];?>
                            </span>
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
                            <?php
                            if($access==_DOYEN)
                            echo '<li><a href="profil_admin.php.php"><i class="icon-home"></i> Mon Profile</a></li>';
                            else
                            echo '<li><a href="profil.php"><i class="icon-home"></i> Mon Profile</a></li>';
                            ?>

							<li class="divider"></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
						
					</li>
                    <!-- theme selector starts -->
                    <div class="btn-group pull-right theme-container" >
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon-tint"></i><span class="hidden-phone"> Theme</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" id="themes">
                            <li><a data-value="amelia" href="#"><i class="icon-blank"></i> Amelia</a></li>
                            <li><a data-value="classic" href="#"><i class="icon-blank"></i> Classic</a></li>
                            <li><a data-value="cerulean" href="#"><i class="icon-blank"></i> Cerulean</a></li>
                            <li><a data-value="cosmo" href="#"><i class="icon-blank"></i> Cosmo</a></li>
                            <li><a data-value="cyborg" href="#"><i class="icon-blank"></i> Cyborg</a></li>
                            <li><a data-value="flatly" href="#"><i class="icon-blank"></i> Flatly</a></li>
                            <li><a data-value="journal" href="#"><i class="icon-blank"></i> Journal</a></li>
                            <li><a data-value="readable" href="#"><i class="icon-blank"></i> Readable</a></li>
                            <li><a data-value="superhero" href="#"><i class="icon-blank"></i> Superhero</a></li>
                            <li><a data-value="simplex" href="#"><i class="icon-blank"></i> Simplex</a></li>
                            <li><a data-value="slate" href="#"><i class="icon-blank"></i> Slate</a></li>
                            <li><a data-value="spacelab" href="#"><i class="icon-blank"></i> Spacelab</a></li>
                            <li><a data-value="united" href="#"><i class="icon-blank"></i> United</a></li>
                        </ul>
                    </div>
                    <!-- theme selector ends -->
				</ul>
			
		<!--		<form class="navbar-search pull-right">
					<input class="search-query" placeholder="Recherche" type="text">
				</form> -->
			<div >
					<span class="nav brand pull-right" style="font-size:15px;">Année Universitaire: 

						<span class="annee" data-pk="<?php echo get_cur_year("id",$bdd);?>" data-value="<?php echo get_cur_year("id",$bdd);?>" data-name="annee"><?php echo get_cur_year("des",$bdd);?></span>
                        <?php
                            if(isAUvalid($bdd))
                            echo '<span class="label label-info">Archive</span>';
                        ?>


					</span>
				</div>
				</div>
				
			</div>
			
		<!--  2eme barre de navigation  -->
		</header>
		
		<div id="main-container" class="container-fluid">

			<!--  2eme barre de navigation  -->
		 <div class="row-fluid">
			<div class="span2 main-menu-span"  > <!-- style="min-width:10%;" -->
			
			 <div class="well well-small sidebar-nav" style="padding: 9px 0;box-shadow: 0px 0px 10px #BDBDBD;padding: 0px;">
				<ul class="nav nav-tabs nav-stacked main-menu" >

					<li <?php if($active==1)echo 'class="active"';?>> <a href="index.php" title="index"><i class="icon-home"></i><span class="hidden-tablet1"> Dashboard</span></a></li>
                <?php
                    if($access==_ADMIN || $access == _DOYEN)
                    {

                        echo '<li class="divider"></li><li ';
                    if($active==1.2) echo 'class="active"';
                    echo '><a href="annee_univ.php" title="Annèes universitaire"><i class=" icon-calendar"></i><span class="hidden-tablet"> Annèes universitaire</span><span class="visible-tablet"> Annèes univ.</span></a></li>
					';
                    }
                    if($access==_ADMIN || $access==_COLLEGE )
                    {
                ?>
					<li class="nav-header hidden-tablet1">Preparation</li>
					<li <?php if($active==2)echo 'class="active"';?>> <a href="planification.php" title="Planification"><i class="icon-tasks"></i><span class="hidden-tablet1"> Planification</span></a></li>
					<li <?php if($active==2.1)echo 'class="active"';?>> <a href="fiches.php" title="Fiches"><i class="icon-file"></i><span class="hidden-tablet1"> Fiches</span></a></li>
                    <li <?php if($active==2.2)echo 'class="active"';?>><a href="conflit.php" title="Conflits"><i class="icon-flag"></i> <span class="hidden-tablet1">Conflits</span></a></li>

                    <?php
                    if($access==_ADMIN )
                    {
                    ?>
					<li class="nav-header hidden-tablet1">Affectation</li>
					 
					<li <?php if($active==4.1)echo 'class="active"';?>><a href="auto_affectation.php" title="Affectations automatique"><i class="icon-road"></i> <span class="hidden-tablet1">Auto-affect</span></a></li>
					<li <?php if($active==4.2)echo 'class="active"';?>><a href="affectation.php" title="Affectation des modules"><i class="icon-tag"></i> <span class="hidden-tablet1">Modules</span></a></li>
					<li <?php if($active==3)echo 'class="active"';?>><a href="affectation_enseignant.php" title="Affectation des enseignants"><i class="icon-tag"></i> <span class="hidden-tablet1">Enseignants</span></a></li>
                    <?php }?>
					<li class="nav-header hidden-tablet1">Configuration</li>

					<li <?php if($active==5.1)echo 'class="active"';?>><a href="configGrade.php" title="Grades"><i class="icon-list"></i><span class="hidden-tablet1"> Grades</span></a></li>
					<li <?php if($active==5)echo 'class="active"';?>><a href="enseignant.php" title="Enseignants"><i class="icon-list"></i><span class="hidden-tablet1"> Enseignants</span></a></li>
					<li <?php if($active==6)echo 'class="active"';?>><a href="module.php" title="Modules"><i class="icon-list"></i><span class="hidden-tablet1"> Modules</span></a></li>
					<li <?php if($active==7)echo 'class="active"';?>><a href="departement.php" title="Départements"><i class="icon-list"></i><span class="hidden-tablet1"> Departements</span></a></li>
					<li <?php if($active==8)echo 'class="active"';?>><a href="filiere.php" title="Filières"><i class="icon-list"></i><span class="hidden-tablet1"> Filières</span></a></li>
					<li <?php if($active==9)echo 'class="active"';?>><a href="cycle.php" title="Cycles"><i class="icon-list"></i><span class="hidden-tablet1"> Cycles</span></a></li>

                    <?php
                    if($access==_ADMIN )
                    {
                    ?>
					<li class="divider"></li>
                        <li <?php if($active==10)echo 'class="active"';?>>
                            <a href="statistiques.php" title="Statistiques">
                                <i class="icon-info-sign"></i>

                                <span class="hidden-tablet1"> Statistiques</span>
                            </a>
                    </li>
                    <?php }?>
                    <li class="divider"></li>
                    <?php
                    }
                    if($access!=_DOYEN)
                    {
					?>

                    <li class="nav-header hidden-tablet1"> <span class="hidden-tablet">Espace Enseignant</span></li>
                    <li <?php if($active==4.3)echo 'class="active"';?>><a href="fiches_souhaits.php" title="Fiches des souhaits"><i class="icon-list"></i><span class="hidden-tablet"> Fiche de souhaits</span><span class="visible-tablet"> Fiche_souhaits</span></a></li>
                    <li class="divider"></li>
					<?php
                    }
                    ?>

				</ul><!--/.nav-list-->
			</div> <!-- sidebar-nav -->
            <?php if(($access==_ADMIN) || ($access==_DOYEN)){

            ?>

                <?php
                }
                ?>
			</div> <!-- span2 -->
			
			
			
			<div class="span10">
			<!--Body content start from here-->