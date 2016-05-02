<?php
session_start();

$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
include_once('cookie_connect.php');
if(isset($_SESSION['id'])) 
{
   $requser = $bdd->prepare("SELECT * FROM users WHERE id = ?");
   $requser->execute(array($_SESSION['id']));
   $user = $requser->fetch();
   
   if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['pseudo']) 
   {
      $newpseudo = htmlspecialchars($_POST['newpseudo']);
      $insertpseudo = $bdd->prepare("UPDATE users SET pseudo = ? WHERE id = ?");
      $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
      header('Location: monfil.php?id='.$_SESSION['id']);
   }
   if(isset($_POST['new_naissance']) AND !empty($_POST['new_naissance']) AND $_POST['new_naissance'] != $user['date_naissance']) 
   {
      $new_naissance = htmlspecialchars($_POST['new_naissance']);
      $insertnaissance = $bdd->prepare("UPDATE users SET date_naissance = ? WHERE id = ?");
      $insertnaissance->execute(array($new_naissance, $_SESSION['id']));
      header('Location: monfil.php?id='.$_SESSION['id']);
   }
   if(isset($_POST['new_pays']) AND !empty($_POST['new_pays']) AND $_POST['new_pays'] != $user['pays']) 
   {
      $new_pays = htmlspecialchars($_POST['new_pays']);
      $insertpays = $bdd->prepare("UPDATE users SET pays = ? WHERE id = ?");
      $insertpays->execute(array($new_pays, $_SESSION['id']));
      header('Location: monfil.php?id='.$_SESSION['id']);
   }
   if(isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['email']) 
   {
      $newmail = htmlspecialchars($_POST['newmail']);
      $insertmail = $bdd->prepare("UPDATE users SET email = ? WHERE id = ?");
      $insertmail->execute(array($newmail, $_SESSION['id']));
      header('Location: monfil.php?id='.$_SESSION['id']);
   }
   if(isset($_POST['newmdp1']) AND !empty($_POST['newmdp1']) AND isset($_POST['newmdp2']) AND !empty($_POST['newmdp2'])) 
   {
      $mdp1 = sha1($_POST['newmdp1']);
      $mdp2 = sha1($_POST['newmdp2']);
      if($mdp1 == $mdp2) 
	  {
         $insertmdp = $bdd->prepare("UPDATE users SET password = ? WHERE id = ?");
         $insertmdp->execute(array($mdp1, $_SESSION['id']));
         header('Location: monfil.php?id='.$_SESSION['id']);
      } 
	  else 
	  {
         $msg = "Vos deux mdp ne correspondent pas !";
      }
   }
	if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name']))
	{
		//Limite 2Mo
		$taillemax=2097152;
		$extensionsValides=array('jpg','jpeg','gif','png');
		if($_FILES['avatar']['size']<= $taillemax)
		{
			$extensionUpload=strtolower(substr(strrchr($_FILES['avatar']['name'],'.'),1));
			if(in_array($extensionUpload,$extensionsValides))
			{
				$chemin="users/avatar/".$_SESSION['id'].".".$extensionUpload;
				$resultat=move_uploaded_file($_FILES['avatar']['tmp_name'],$chemin);
				if($resultat)
				{
					$updateavatar=$bdd->prepare('UPDATE users SET avatar=:avatar WHERE id=:id');
					$updateavatar->execute(array('avatar'=>$_SESSION['id'].".".$extensionUpload,'id'=>$_SESSION['id']));
					header('Location: monfil.php?id='.$_SESSION['id']);
				}
				else
				{
					$msg="Erreur durant l'importation de la photo";
				}
			}
			else
			{
				$msg="Votre photo de profil n'est pas au bon format";
			}
		}
		else
		{
			$msg="Votre photo de profil est trop volumineuse";
		}	
	} 
?>

<!DOCTYPE html>
<html>

<head>
    <title> AMSTRAGRAM </title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="parametres.css"/>
</head>


<body>
	<!-- barre du haut-->
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


<!-- liste de parametres, je dois rajouter la photo, et améliorer la mise en forme-->
	<ul id="mylist">
	<form method="post" action="" enctype="multipart/form-data">
		<li>changez votre avatar</li>
		<?php 
		if(!empty($user['avatar']))
		{
			?>
			<img src="users/avatar/<?php echo $user['avatar'];?>" style="width:150px;height:150px;border-radius:5px;" border="3white"/>
			<?php
		}
		?>
		<input type="file" name="avatar" id="avatar"/>
		</br>
		</br>
		<li>changez votre pseudo</li>
		
		<input type="text" id="pseudo" name="newpseudo" value="" />
		</br>
		</br>
		<li>changez votre mot de passe</li>
		
		<input type="password" id="password" name="newmdp1"/>
		</br>
		</br>
		<li>confirmez votre mot de passe</li>
		
		<input type="password" name="newmdp2" id="password2"/>
		</br>
		</br>
		<li>changez votre email</li>
		
		<input type="email" id="email" name="newmail"  value=""/>
		</br>
		</br>
		<li>changez votre date de naissance</li>
		
		<input type="date" id="date_naissance" name="new_naissance"/>
		</br>
		</br>
		<li>changez votre pays</li>
		
		<select name="new_pays" id="pays">
			<option value="France">France</option>
			<option value="Espagne">Espagne</option>
			<option value="Italie">Italie</option>
			<option value="Royaume-Uni">Royaume-Uni</option>
			<option value="Canada">Canada</option>
			<option value="Etats-unis">États-Unis</option>
			<option value="Chine">Chine</option>
			<option value="Japon">Japon</option>
		</select>
		</br>
		</br>	
		
		<input type="submit" value="GO!"></input>
		</br>
	</ul>
	</ul>
	</form>
	<?php if(isset($msg)) { echo $msg; } ?>
</body>
</html>
<?php   
}
else 
{
   header("Location: home.php");
}
?>