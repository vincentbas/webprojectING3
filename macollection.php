<?php

session_start();
include_once('cookie_connect.php');
//Connexion base de donn�es
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');	
if(isset($_GET['id']) AND $_GET['id'] > 0) 
{
$getid = intval($_GET['id']);
$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
$requser->execute(array($getid));
$userinfo = $requser->fetch();	
$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid'  ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>

<head>
    <title> AMSTRAGRAM </title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="macollection.css"/>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
	<script type="text/javascript" src="zoombox/zoombox.js"></script>
	<link href="zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
  

	<script type="text/javascript">
	jQuery(function($)
	{
		$('a.zoombox').zoombox();
	});
	</script> 
</head>


<body>

	<!--barre du haut-->
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
			<input type="search" name="q" placeholder="rechercher"/>
			<input type="submit" value="OK"/>
		</li>

		<li>
			<a href="home.php">deconnexion</a>
		</li>
	</ul>
</nav>

<!--liste de droite; il s'agit de la s�l�citon de l'affichage des photos-->
	<ul id="mylist">
		<?php 
		if(!empty($userinfo['avatar']))
		{
			?>
			<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;"/>
			<?php
		}
		?>
		<li>
			Espace membre
		</li>
		<li>
			<?php echo $userinfo['pseudo'];?>
		</li>
		<li>
			<?php echo $userinfo['date_naissance'];?>
		</li>
		<li>
			<?php echo $userinfo['pays'];?>
		</li>
		</br>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="radio" name="bouton" value="Public"> Public
			</br>
			<input type="radio" name="bouton" value="Privee"> Privee
			</br>
			<input type="radio" name="bouton" value="Combo" checked="check"> Combo
			</br>
			<input type="submit" name="envoi" id="Importer" value="GO"></code>
		</form>
	</ul>
	
	<ul id="collection">
		<div id="line1">
			<?php
			if (isset($_POST['envoi']))
			{
			$bouton_statut=$_POST['bouton'];
			if($bouton_statut=='Privee'OR $bouton_statut=='Public')
			{
				$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid' and parametre='$bouton_statut' ORDER BY id DESC");
				$bouton_statut='Combo';
			}
			else
			{
				$photos = $bdd->query("SELECT * FROM photo WHERE proprio='$getid'  ORDER BY id DESC");
				$bouton_statut='Combo';
			}
			
			// On affiche chaque entr�e une � une
			while ($photos_data = $photos->fetch())
			{
				$cheminM = "photos/min/".$photos_data['img'];
				$cheminG = "photos/".$photos_data['img'];
			?>
				
				<a class="zoombox zgallery1" href="<?php echo $cheminG;?>"> 
				<?php 
					if(!empty($photos_data['img']))
					{
				?>
				<div id="view_box">
					<img id="photos" src="<?php echo $cheminM ?>" style="width:250px;height:250px;"/>
					<a href="supprime_photos.php?id=<?= $photos_data['id']?>">Supprimer</a>
					<a href="">Modifier</a>
				<div id="view_box">
				<?php
					}
				?>
				</a> 
			<?php
			}
			?>
						
		</div>
	</ul>
	<?php
		}					
			?>
</body>
</html>
<?php
}
else
{
	header("Location: macollection.php?id=".$_SESSION['id']);
}
?>