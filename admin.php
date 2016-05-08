<?php

//RECUPERATION DES VARIABLES SESSION 
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
		<link rel="stylesheet" href="admin.css"/>
	</head>

	<body>
	
		<h3> AMSTRAMGRAM </h3>
		<h6> Espace Administrateur</h6>
		
		<!--MENU-->
		<nav>
			<ul>
				<li>
					<a href="admin.php" id="here">informations</a>
				</li>
				<li>
					<a href="admin_users.php">utilisateurs</a>
				</li>
				<li>
					<a href="admin_photos.php">photos</a>
				</li>
				<li>
					<input type="text" placeholder="rechercher"/>
					<input type="submit" value="OK"/>
				</li>
				<li>
					<a href="deconnexion.php">deconnexion</a>
				</li>
			</ul>
		</nav>
		
		<!--PROFIL: INFORMATION PROPRE A L'ADMINISTRATEUR-->
		<ul id="mylist">
		</br>
			<?php 
			if(!empty($userinfo['avatar']))
			{
				?>
				<!--AVATAR ADMINISTRATEUR-->
				<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;"/>
				<?php
			}
			?>
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
			
			<!-- LA BALISE ARTICLE CONTIENT QUELQUES INFORMATIONS SUR LA BDD DU SITE-->
			<article class="pt">
				<h4>
					</br>
					<?php
						
						//SELECTION DU NOMBRE D'UTILISATEURS INSCRITS
						$sql1 = 'SELECT COUNT(*) AS nb_users FROM users';
						$result1 = $bdd->query($sql1);
						$columns1 = $result1->fetch();
						$nb_users = $columns1['nb_users'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE D'INSCRITS
						if($nb_users<=1)
						{
							echo 'Il y a '.$nb_users.' utilisateur inscrit.</br>';
						}
						else
						{
							echo 'Il y a '.$nb_users.' utilisateurs inscrits.</br>';
						}
						
						//SELECTION DU NOMBRE DE MEMBRES INSCRITS
						$sql2 = "SELECT COUNT(*) AS nb_mbrs FROM users WHERE rang='0'";
						$result2 = $bdd->query($sql2);
						$columns2 = $result2->fetch();
						$nb_mbrs = $columns2['nb_mbrs'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE D'UTILISATEURS INSCRITS
						if($nb_mbrs<=1)
						{
							echo '-'.$nb_mbrs.' membre inscrit.</br>';
						}
						else
						{
							echo '-'.$nb_mbrs.' membres inscrits.</br>';
						}
						
						//SELECTION DU NOMBRE D'ADMINISTRATEURS INSCRITS
						$sql3 = "SELECT COUNT(*) AS nb_admin FROM users WHERE rang='1' ";
						$result3 = $bdd->query($sql3);
						$columns3 = $result3->fetch();
						$nb_admin = $columns3['nb_admin'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE D'ADMINISTRATEURS INSCRITS
						if($nb_admin<=1)
						{
							echo '-'.$nb_admin.' administrateur inscrit.</br></br>';
						}
						else
						{
							echo '-'.$nb_admin.' administrateurs inscrits.</br></br>';
						}
						
						//SELECTION DU NOMBRE DE PHOTOS PUBLIEES
						$sql4 = 'SELECT COUNT(*) AS nb_photo FROM photo';
						$result4 = $bdd->query($sql4);
						$columns4 = $result4->fetch();
						$nb_photo = $columns4['nb_photo'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE DE PHOTOS PUBLIEES
						if($nb_photo<=1)
						{
							echo 'Il y a '.$nb_photo.' photo.</br>';
						}
						else
						{
							echo 'Il y a '.$nb_photo.' photos.</br>';
						}
						
						//SELECTION DU NOMBRE DE PHOTOS PRIVEES
						$sql5 = "SELECT COUNT(*) AS nb_privee FROM photo WHERE parametre='Privee'";
						$result5 = $bdd->query($sql5);
						$columns5 = $result5->fetch();
						$nb_privee = $columns5['nb_privee'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE DE PHOTOS PRIVEES 
						if($nb_privee<=1)
						{
							echo '-'.$nb_privee.' photo privee.</br>';
						}
						else
						{
							echo '-'.$nb_privee.' photos privees.</br>';
						}
						
						//SELECTION DU NOMBRE DE PHOTOS PRIVEES
						$sql6 = "SELECT COUNT(*) AS nb_public FROM photo WHERE parametre='Public'";
						$result6 = $bdd->query($sql6);
						$columns6 = $result6->fetch();
						$nb_public = $columns6['nb_public'];
						
						//TEST: VERIFICATION DES ACCORDS DES MOTS EN FONCTION DU RESULTAT DE LA REQUETE ET AFFICHAGE DU NOMBRE DE PHOTOS PUBLIQUES
						if($nb_public<=1)
						{
							echo '-'.$nb_public.' photo publique.</br></br>';
						}
						else
						{
							echo '-'.$nb_public.' photos publiques.</br></br>';
						}
					?>
				</h4>
			</article>
	</body>
</html>
<?php
}
else
{
header("Location: admin.php?id=".$_SESSION['id']);
}
?>