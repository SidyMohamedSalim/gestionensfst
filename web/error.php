<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
<meta charset="utf-8">
<title>Database Error - Gestion des services</title>
<link href="../styles/bootstrap.css" rel="stylesheet" media="screen">
<style>
.error-box {
	color: #999;
	font-weight: 600;
	margin-top: 100px;
	text-align: center;
}
.error-box .message-small {
	font-size: 20px;
	line-height:24px;
}
.error-box .message-big {
	font-size: 80px;
	line-height: 100px;
	color:#CCC;
	text-shadow: 1px 1px 1px #666, 0 0 2px #333;
}
</style>
<script>
function goBack()
  {
  window.history.back()
  }
</script>
</head>

<body>
<div class="row-fluid">
<div class="span8 offset2">
  <div class="error-box">
    <div class="message-small">Une erreur de base de données s'est produite</div>
    <div class="clearfix"></div>
    <div class="message-big">ERREUR BD!</div>
    <div class="clearfix"></div>
    <div class="message-small">
        <p>Impossible de se connecter au serveur de la  base de données en utilisant les paramètres fournis.</p>
        <p>Erreur: <?php if(isset($ERR))echo $ERR;?></p><br>Première utilisation?</div>
    <div class="clearfix"></div>
    <a href="install/" class="btn btn-large btn-primary"> <i class="icon-cog icon-white"></i> Installer la base de donnée </a>
    <div style="margin-top: 50px">
        <a onClick="goBack()" class="btn btn-info"> <i class="icon-arrow-left icon-white"></i> Précedent </a>
    </div>
  </div>
</div>
</div>
				
</body>
</html>
