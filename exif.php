<?php
//RECUPERATION DES VARIABLES DE SESSION
session_start();

//CONNEXION BDD
$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
include_once('cookie_connect.php');

if (isset($_SESSION['id'])) 
{
    //REQUETE: SELECTIONNE LES INFORMATIONS DE L'UTILISATEUR CONNECTE
	$requser = $bdd->prepare("SELECT * FROM users WHERE id ='$_SESSION[id]'");
	$requser->execute(array($_SESSION['id']));
	
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
		<div id="text" align="center">
			<h4>
			<?php
			if (isset ($_GET['nom'])) 
			{ 
				$path = "photos/".$_GET['nom'];
				$exif = exif_read_data($path, 0, true);
				echo $_GET['nom'];
				?>
				</br></br>
				<?php
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
?>