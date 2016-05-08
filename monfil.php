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

	//REQUETE: SELECTIONNE DANS L'ORDRE DECROISSANT DES ID DE TOUTES LES PHOTOS AVEC UNE VISIBILITE PUBLIC
	$photos = $bdd->query("SELECT * FROM photo WHERE parametre='Public' ORDER BY id DESC");
	//(rowCount):FONCTION QUI RENVOIE LE NOMBRE DE PHOTOS TROUVE
	$nb_photos=$photos->rowCount();

	//REQUETE: SELECTIONNE TOUTES LES INFORMATIONS CONCERNANT LA TABLE LIKE
	$like = $bdd->query('SELECT * FROM aime');	
?>
<!DOCTYPE html>
<html>
	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="monfil.css"/>
		
		<!--SCRIPT ET CSS DE LA LIGHTBOX Zoombox-->
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
		<h3> AMSTRAMGRAM </h3>
		<h6> Bour et Bour et Ratatam !</h6>
		
		<!--MENU-->
		<nav>
			<ul>
				<li>
					<a href="ajouter.php">ajouter</a>
				</li>

				<li>
					<a href="monfil.php" id="here">mon fil</a>
				</li>

				<li>
					<a href="macollection.php">ma collection</a>
				</li>

				<li>
					<a href="parametres.php">mes parametres</a>
				</li>
				
				<li>
				<form method="POST" action="">
					<input type="text" name="q" id="search" placeholder="rechercher"/>
					<input type="submit" value="OK" id="search_button"/>
				</form>	
				</li>
				
				<li>
					<a href="deconnexion.php">deconnexion</a>
				</li>
			</ul>
		</nav>

		<!--PROFIL: INFORMATION PROPRE A L'UTILISATEUR-->
		<ul id="mylist">
			</br>
			<?php 
			if(!empty($userinfo['avatar']))
			{
				?>
				<!--AVATAR UTILISATEUR-->
				<img src="users/avatar/<?php echo $userinfo['avatar'];?>" style="width:150px;height:150px;" id="avatar"/>
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
		
				<?php  
				//FONCTION RECHERCHE
				if(isset($_POST['q']))
				{
					$motcle = $_POST['q'];
					//REQUETE: SELECTIONNE TOUTES LES PHOTOS PUBLIQUES QUI CONTIENNENT LE MOT TAPE DANS LA BARRE DE RECHERCHE DANS LES CHAMPS PSEUDO LIEU ET NOM IMAGE
					$resultat = $bdd->query("SELECT DISTINCT P.id, P.NomImage, P.img, P.description, P.lieu, P.date, P.parametre FROM photo P, users U WHERE P.parametre = 'Public' AND (P.NomImage LIKE '%$motcle%' OR (P.proprio = U.id AND U.pseudo LIKE '%$motcle%') OR (P.lieu LIKE '%$motcle%'))");
					$nb_resultat = $resultat->rowCount();
				}
				else 
				{
					//REQUETE: SELECTION DE TOUTES LES PHOTOS PUBLIQUES DANS L'ORDRE DECROISSANT
					$resultat = $bdd->query("SELECT * FROM photo WHERE parametre = 'Public' ORDER BY date DESC");
					$nb_resultat = $resultat->rowCount();
				}
				?> <br/><br/><br/></br></br> <?php
				//TEST: RENVOIE LE NOMBRE DE RESULTAT DE LA RECHERCHE
				//AUCUN RESULTAT
				if ($nb_resultat == 0) 
				{
					if(isset($_POST['q']))
					{
					?>
					<h2> Aucune photo ne correspond à votre recherche pour : <?php echo $motcle ?> </h2>
					<?php
					}
					else 
					{
						?>
					<h2> Nous n'avons pas de photos a vous montrer! </h2>
					<?php
					}
				}
				//N RESULTATS
				else if(isset($_POST['q']))
				{
				?>
					<h2> Il y a <?php echo $nb_resultat ?> résultat(s) correspondant a votre recherche : <?php echo $motcle ?></h2>
				<?php

				}

				//FONCTION LIKE
				if (isset($_POST['jaime']))
				{	
					$id_photo = $_POST['idphoto'];
					$id_user = $userinfo['id'];		
					//REQUETE: INSERTION DU LIKE DANS LA TABLE LIKE
					$result = $bdd->prepare('INSERT INTO aime (id_photo,id_user) VALUES(?,?)');
					$result->execute(array($id_photo, $id_user));
				}
				//FONCTION UNLIKE
				if (isset($_POST['unlike']))
				{
						$id_photo = $_POST['idphoto'];
						$id_user = $userinfo['id'];
						//REQUETE: SUPPRESSION DU LIKE LORSQUE L'ID USER CORRESPOND A L'UTILISATEUR CONNECTE ET QUE LA ID PHOTO CORRESPOND A LA PHOTO SELECTIONNE
						$result = $bdd->query("DELETE FROM aime WHERE id_photo = '$id_photo' AND id_user = '$id_user'");	
				}
				
				//FONCTION FOLLOW 
				if (isset($_POST['suivre']))
				{	
					$id_user1 = $getid;
					$id_user2 = $_POST['iduser2'];	
					//REQUETE: INSERTION DU FOLLOWER DANS LA TABLE FOLLOWER
					$result = $bdd->prepare('INSERT INTO follower (id_user1,id_user2) VALUES(?,?)');
					$result->execute(array($id_user1, $id_user2));
				}	
				//FONCTION UNFOLLOW
				if (isset($_POST['unfollow']))
				{
						$id_user1 = $getid;
						$id_user2 = $_POST['iduser2'];	
						//REQUETE: SUPPRESSION DU FOLLOWER LORSQUE L'ID USER1 CORRESPOND A L'UTILISATEUR CONNECTE ET QUE LA ID USER2 CORRESPOND A LA PERSONNE QUI A PUBLIE LA PHOTO
						$result = $bdd->query("DELETE FROM follower WHERE id_user1 = '$id_user1' AND id_user2 = '$id_user2'");	
				}
				
				//FONCTION COMMENTAIRES
				if(isset($_POST['commentaire']))
				{
						$id_user = $getid;
						$id_photo = $_POST['idphoto'];
						$message = $_POST['commentaire'];
						//REQUETE: INSERTION DES COMMENTAIRES DANS LA TABLE COMMENTS
						$result = $bdd->prepare('INSERT INTO comments (id_user, id_photo, contenu) VALUES (?, ?, ?)');
						$result->execute(array($id_user, $id_photo, $message));
				}
				
						// ON AFFICHE LES ENTREES UNE A UNE
						while ($photos_data = $photos->fetch())
						{	
							//ADRESSE DES PHOTOS MINIATURES
							$cheminM = "photos/min/".$photos_data['img'];
							//ADRESSE DES PHOTOS 
							$cheminG = "photos/".$photos_data['img'];
						?>
						
							<!-- LA BALISE ARTICLE CONTIENT LA PHOTO, LES DIFFERENTS BOUTONS, L'ESPACE COMMENTAIRE, ET LES INFOS DE LA PHOTO-->
							<article class="pt">
							
								<!--LIEN VERS LA LIGHTBOX Zoombox-->
								<a class="zoombox zgallery1" href="<?php echo $cheminG;?>"> 
								<?php 
									if(!empty($photos_data['img']))
									{
								?>
									<!--IMAGE PUBLIEE-->
									<img id="photos" src="<?php echo $cheminM ?>" style="width:350px;height:350px;"/>
								<?php						
									}
								?>
								</a> 
								<div id="text">
									<h4>
										@<?php
											//RECUPERATION ET AFFICHAGE DU PSEUDO DE LA PERSONNE QUI A PUBLIE LA PHOTO EN PASSANT PAR SON ID
											$req = $bdd->query("SELECT pseudo FROM users WHERE id = '$photos_data[proprio]'");
											$pseudo = $req->fetch();
											echo $pseudo['pseudo']; 
										?><br />
									</h4>
									<p class="com">
										<?php echo $photos_data['description']; ?><br />
									</p>
									<div id="statut">
										
										<form method="post" action ="">
											<!--(hidden): PERMET DE DIFFERENCIER NOS BOUTONS LES BOUTONS LIKE OU UNLIKE PRESENT SUR CHAQUE PHOTO, C'EST UN BOUTON CACHE QUI N'APPARAIT PAS POUR L'INTERNAUTE-->
											<input type="hidden"  name="idphoto"  value="<?php echo $photos_data['id'] ?>">
											<p id="infos">
											<?php
												//REQUETE:SELECTIONNE TOUS LES LIKES QUI CORRESPOND A L'UTILISATEUR CONNECTE ET A L'ID DE LA PHOTO
												$photolike = $bdd->query("SELECT * FROM aime WHERE id_photo = '$photos_data[id]'AND id_user = '$getid'");
												//NOMBRE DE RESULTAT CORRESPONDANT A LA REQUETE CI-DESSUS
												$photolikenum = $photolike->rowCount();
												
												//AFFICHAGE DU NOMBRE DE LIKES
												$like = $bdd->query("SELECT * FROM aime WHERE id_photo = '$photos_data[id]'");
												$nb_like = $like->rowCount();
												echo $nb_like." Likes";
											?>
											</p>
											<?php		
											//TEST: SI LA VARIABLE EST EGALE A 0 ON AFFICHE LE BOUTON LIKE SINON ON AFFICHE LE BOUTON UNLIKE
											if($photolikenum == 0)
											{
											?>										
												<input type="image" src="images/others/like.png" style="width:100px" name="jaime" id="Like" value="Like"/>
											<?php
											}
											else 
											{
											?>				
												<input type="image" src="images/others/liked.png" style="width:100px" name="unlike" id="UnLike" value="UnLike"/>
											<?php
											}
											?>
											
											<input type="hidden"  name="iduser2"  value="<?php echo $photos_data['proprio'] ?>">
											<?php
											//REQUETE: SELECTION DE TOUS LES FOLLOWER DE LA TABLE FOLLOWER OU LES UTILISATEURS 1 ET 2 CORRESPONDENT RESPECTIVEMENT A L'UTILISATEUR CONNECTE ET LE PROPRIETAIRE DE LA PHOTO
											$user_follow = $bdd->query("SELECT * FROM follower WHERE id_user1 = '$userinfo[id]' AND id_user2 = '$photos_data[proprio]'"); 
											//NOMBRE DE RESULTAT CORRESPONDANT A LA REQUETE CI-DESSUS
											$nb_user_follow = $user_follow->rowCount();
											//TEST: SI LA VARIABLE EST EGALE A 0 ON AFFICHE LE BOUTON FOLLOW SINON ON AFFICHE LE BOUTON UNFOLLOW
											if($nb_user_follow == 0)
											{
											?>
												<input type="image" src="images/others/follow.png" style="width:100px" name="suivre" id="follow" value="Follow">
											<?php
											}
											else 
											{
											?>
												<input type="image" src="images/others/followed.png" style="width:100px" name="unfollow" id="unfollow" value="UnFollow"><?php
											}
											?>
											</br>
											
											<!--BOUTONS EXIF-->
											<input type="hidden"  name="exif_photo"  value="<?php echo $photos_data['id'] ?>">
											
											<!--(onclick()):APPEL D'UNE FONCTION JavaSript QUI OUVRE UNE FENETRE POP-UP-->
											<input type="image" src="images/others/exif.png" style="width:40px" name="unlike" id="UnLike" value="UnLike" onclick="window.open('exif.php?nom=<?php echo $photos_data['img']?>','wclose','width=650,height=280,toolbar=no,status=no,left=400,top=250')"/>
											
										</form>
										
									</div>
										<div id="com">
											</br>
											<?php 
												//REQUETE: SELECTION DE TOUS LES COMMENTAIRES DE LA PHOTO
												$nb_comments = $bdd->query("SELECT * FROM comments WHERE id_photo= '$photos_data[id]'");
												//NOMBRE DE RESULTAT CORRESPONDANT A LA REQUETE CI-DESSUS
												$nb_result = $nb_comments->rowCount();
											?>
											<!--AFFICHAGE DU NB DE COMMENTAIRES-->
											<?php echo $nb_result." Commentaires" ?>
											<!--FORMULAIRE COMMENTAIRE-->
											<form method="POST" action ="">
												<input type="hidden"  name="idphoto"  value="<?php echo $photos_data['id']?>">
												<input type="textarea" name="commentaire" rows="4" cols="25" id="commentaires" placeholder="commentez...">
											</form>
											</br>

											<table>
											<?php 
												//AFFICHE LES UNS APRES LES AUTRES LE PSEUDO DE LA PERSONNE QUI PUBLIE LE COMMENTAIRE ET LE CONTENU DU COMMENTAIRE
												for($i =0; $i < $nb_result; $i++)
												{
													$comment = $nb_comments->fetch();
													$requete = $bdd->query("SELECT pseudo FROM users WHERE id = '$comment[1]'");
													$pseudo = $requete->fetch();
													?>
														<tr>	
															<td>
															<?php echo $pseudo['pseudo'] ?> :
															</td>
															<td>
															<?php echo $comment['contenu'] ?>
															</td>
														</tr>
													<?php
												}
											?>
											</table>
										</div>	
								</div>					
							</article>
					<?php
					}			
				?>		
	</body>
</html>
<?php
}
else
{
header("Location:monfil.php?id=".$_SESSION['id']);
}
?>