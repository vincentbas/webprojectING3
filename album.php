<?php

session_start();
include_once('cookie_connect.php');
//Connexion base de données
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');


if(isset($_GET['id']) AND $_GET['id'] > 0) 
{
$getid = intval($_GET['id']);
$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
$requser->execute(array($getid));
$userinfo = $requser->fetch();	

?>
<html>
<head>
	<title>Album System</title>
	<meta charset="utf-8"/>
	<link rel='stylesheet' href='ajouter.css'>
</head>
<body>
<div id="ajout">
	<h3> AMSTRAMGRAM </h3>
	<h6> Bour et Bour et Ratatam !</h6>
	<nav>
		<ul>
		<li>
			<a href="ajouter.php">ajouter</a>
		</li>
		
		<li>
			<a href="album.php">album</a>
		</li>
		
		<li>
			<a href="monfil.php">mon fil</a>
		</li>

		<li>
			<a href="macollection.php">ma collection</a>
		</li>
		
		<li>
			<a href="mesalbums.php">mes albums</a>
		</li>

		<li>
			<a href="parametres.php">mes parametres</a>
		</li>
		
		<li>
			<a href="deconnexion.php">deconnexion</a>
		</li>
	</ul>
</nav>
	<h4> Création Album </h4>
	<form method="POST" action="" enctype="multipart/form-data">
	<?php
		if(isset($_POST['create']))
		{
			$name=htmlspecialchars(trim($_POST['NomAlbum']));
			$user_album=$getid;
			if(!empty($name))
			{
				$album=$bdd->prepare('INSERT INTO albums (Nom,id_user)VALUES(?,?)');
				$album->execute(array($name, $getid));
				
				if($album)
				{
					header('Location: monfil.php?id='.$_SESSION['id']);
					echo "Album crée";
					exit;
				}
			}
			else
			{
				echo "Veuillez remplir tous les champs";
			}
		}
	
	?>
		<li>Nom Album</li>
		<input type="text" name="NomAlbum" id="Nom_Album"/>
		</br>
		</br>
		<input type="submit" name="create" id="create" value="GO"></code>
	</form>	

</body>
</html>
<?php
}
else
{
header("Location:album.php?id=".$_SESSION['id']);
}
?>