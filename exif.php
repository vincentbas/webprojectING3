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
    <link rel="stylesheet" href="monfil.css"/>
</head>

<body>

	<!-- barre du haut, curieusement il n'y a que sur cette page que le hover fonctionne. Je vais regler ca -->
	<h3> AMSTRAMGRAM </h3>
	<h6> Bour et Bour et Ratatam !</h6>
	<div class="listphotos">
	<div id="text1">
		<h4>
		<?php
				$photos = $bdd->query("SELECT * FROM photo ORDER BY id DESC");
				// On affiche chaque entrée une à une
				
				while ($photos_data = $photos->fetch())
				{	
					$cheminM = "photos/min/".$photos_data['img'];
					$cheminG = "photos/".$photos_data['img'];
				?>
				</br>
				<?php
					$exif = exif_read_data($cheminG, 0, true);
					foreach ($exif as $key => $section) 
					{
						foreach ($section as $name => $val) 
						{
							echo "$key.$name: $val<br />\n";
						}
					}
				}
		?>	

		</h4>				
	</div>
	</div>
</body>
</html>
<?php
}
else
{
	header("Location: exif.php?id=".$_SESSION['id']);
}
?>