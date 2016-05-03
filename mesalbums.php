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

<!--liste de droite; il s'agit de la séléciton de l'affichage des photos-->
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
	</ul>
	<ul id="collection">
		<div id="line1">
				<?php
					$query = $bdd->query("SELECT * FROM albums WHERE id_user='$userinfo[id]' OR id_user='0'");
					while($album_data=$query->fetch())
					{
						$album_id=$album_data['id'];
						$album_name=$album_data['Nom'];
						
						$photo = $bdd->query("SELECT * FROM photo WHERE proprio='$getid' AND album_id='$album_data[id]'");
						$photos_data=$photo->fetch();
						$cheminM = "photos/min/".$photos_data['img'];
						$cheminG = "photos/".$photos_data['img'];
				?>  
				
				<a class="zoombox zgallery2" href="<?php echo $cheminG;?>"> 
				<?php 
					if(!empty($photos_data['img']))
					{
				?>
				<div id="view_box">
					<img id="photos" src="<?php echo $cheminM ?>" style="width:250px;height:250px;"/>
					
				<?php
					}
				?>
					</br>
					<b><?php echo $album_name?></b> 
				</div>
				</a> 
				
				
				<?php
				}			
				?>		
		</div>
		
	</ul>
		
</body>
</html>
<?php
}
else
{
	header("Location: mesalbums.php?id=".$_SESSION['id']);
}
?>