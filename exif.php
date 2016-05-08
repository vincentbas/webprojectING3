<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();

//CONNEXION BDD
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
include_once('cookie_connect.php');

//TEST: VERIFICATION QUE LA VARIABLE ID EXISTE ET SUPERIEUR A 0
if(isset($_GET['id']) AND $_GET['id'] > 0)
{
	//SECURISER LA VARIABLE CONVERSION DE CE MET L'UTILISATEUR EN NOMBRE
	$getid = intval($_GET['id']);
	
	//REQUETE: SELECTIONNE LES INFORMATIONS DE L'UTILISATEUR CONNECTE
	$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$getid'");
	$requser->execute(array($getid));
	
	//(fetch()): FONCTION QUI RECUPERE LES INFOS PROPPRE A L'UTILISATEUR CONNECTE
	$userinfo = $requser->fetch();	

?>

<!DOCTYPE html>
<html>
	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="exif.css"/>
	</head>

	<body>
		<div id="text">
			<h4>
			<?php
					//REQUETE: SELECTION DE TOUTES LES PHOTOS
					$photos = $bdd->query("SELECT * FROM photo ORDER BY id DESC");
					
					// ON AFFICHE LES ENTREES UNE A UNE
					while ($photos_data = $photos->fetch())
					{	
						//ADRESSE DES PHOTOS MINIATURES
						$cheminM = "photos/min/".$photos_data['img'];
						//ADRESSE DES PHOTOS 
						$cheminG = "photos/".$photos_data['img'];
					?>
					</br>
					<?php
						//(exif_read_data): Lit les en-têtes EXIF dans les images JPEG ou TIFF
						$exif = exif_read_data($cheminG, 0, true);
						// (foreach): PERMET DE PARCOURIR UN TABLE AU SIMPLEMENT
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
	</body>
</html>
<?php
}
else
{
	header("Location: exif.php?id=".$_SESSION['id']);
}
?>