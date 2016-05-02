<?php
session_start();
include_once('cookie_connect.php');
try
{
	//Connexion base de données
	$bdd = new PDO('mysql:host=localhost;dbname=phplogin', 'root', '');
	include_once('cookie_connect.php');
	if(isset($_POST['connexion'])) 
	{
	   $pseudoconnect = htmlspecialchars($_POST['pseudoconnect']);
	   $passwordconnect = sha1($_POST['passwordconnect']);
	   
	   if(!empty($pseudoconnect) AND !empty($passwordconnect)) 
	   {
		  $connectmbr = $bdd->prepare("SELECT * FROM users WHERE pseudo = ? AND password = ?");
		  $connectmbr->execute(array($pseudoconnect, $passwordconnect));
		  $userexist = $connectmbr->rowCount();
		  if($userexist == 1) 
		  {
			if(isset($_POST['remember']))
			{
				setcookie('pseudo',$pseudoconnect,time()+365*24*3600,null,null,false,true);
				setcookie('password',$passxordoconnect,time()+365*24*3600,null,null,false,true);
			}
			 $userinfo = $connectmbr->fetch();
			 $_SESSION['id'] = $userinfo['id'];
			 $_SESSION['pseudo'] = $userinfo['pseudo'];
			 $_SESSION['date_naissance'] = $userinfo['date_naissance'];
			 $_SESSION['pays'] = $userinfo['pays'];
			 $_SESSION['email'] = $userinfo['email'];
			 $_SESSION['avatar'] = $userinfo['avatar'];
			 $_SESSION['rang'] = $userinfo['rang'];
			 if($userinfo['rang']==1)
			 {
				header("Location: admin.php?id=".$_SESSION['id']); 
			 }
			 elseif($userinfo['rang']==0)
			 {
				 header("Location: monfil.php?id=".$_SESSION['id']);
			 }
			 else
			 {
				 header("Location: home.php");
			 }
			 
		  } 
		  else  
		  {
			 $erreur = "Mauvais mail ou mot de passe !";
		  }
	   } 
	   else 
	   {
		  $erreur = "Tous les champs doivent être complétés !";
	   }
	}
	if (isset($_POST['inscription'])) 
	{
		
	   $pseudo = htmlspecialchars($_POST['pseudo']);
	   $date_naissance=htmlspecialchars(trim($_POST['date_naissance']));
	   $pays=htmlspecialchars(trim($_POST['pays']));
	   $email = htmlspecialchars($_POST['email']);
	   $confirme_mail = htmlspecialchars($_POST['confirme_mail']);
	   
	   //Cryptage mot de passe (sha1)
	   $password = sha1($_POST['password']);
	   $confirme_mdp = sha1($_POST['confirme_mdp']);
	   
	   if(!empty($_POST['pseudo']) AND !empty($_POST['date_naissance']) AND !empty($_POST['pays']) AND !empty($_POST['email']) AND !empty($_POST['confirme_mail']) AND !empty($_POST['password']) AND !empty($_POST['confirme_mdp'])) 
	   {
		  $pseudolength = strlen($pseudo);
		  if(strlen($password)>7) 
		  {
			 if($email == $confirme_mail) 
			 {
				if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
				{
				   $reqmail = $bdd->prepare("SELECT * FROM users WHERE email = ?");
				   $reqmail->execute(array($email));
				   $mailexist = $reqmail->rowCount();
				   
				   //Verifie que le mail entré n'est pas déjà dans la base de données
				   if($mailexist == 0) 
				   {
					  if($password == $confirme_mdp) 
					  {
						 $insertmbr = $bdd->prepare("INSERT INTO users(pseudo, date_naissance, pays, email, password, avatar,rang) VALUES(?, ?, ?, ?, ?, ?, ?)");
						 $insertmbr->execute(array($pseudo, $date_naissance, $pays, $email, $password, "default.jpg","0"));
						 
						 $compte = "Votre compte a bien été créé";
						 echo '<font color="blue">'.$compte."</font>";  
					  } 
					  else 
					  {
						 $erreur = "Veuillez saisir le même mot de passe";
					  }
				   } 
				   else 
				   {
					  $erreur = "Adresse mail déjà utilisée !";
				   }
				} 
				else 
				{
				   $erreur = "Votre adresse mail n'est pas valide !";
				}
			 } 
			 else 
			{
				$erreur = "Vos adresses mail ne correspondent pas !";
			}
		  } 
		  else 
			{
			 $erreur = "Votre pseudo doit comporter 7 caractères minimum !";
			}
	   } 
	   else 
		{
		  $erreur = "Tous les champs doivent être complétés !";
		}
	}
}
catch(Exception $e)
{
	//Message d'erreur et tue la page
	die('Erreur : '.$e->getMessage());
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title> AMSTRAGRAM </title>
		<meta charset="utf-8"/>
		<link rel="stylesheet"  href="home.css"/>
	</head>
	<body>
		<h1> AMSTRAMGRAM </h1>
		<h2> PIC ET PIC ET COLLEGRAM !</h2>
		<ul id="maliste">
			<li id="connexion" class="list">
				<h3>
					Connexion
				</h3>
				<ul>
					<form method="POST" action="">
						<li>
						<label for="pseudo">Votre pseudo</label>
						</br>
						<input type="text" name="pseudoconnect" value=""/>
						</br>	
						</li>
						<li>
						<label for="password">Votre password</label>
						</br>
						<input type="password" name="passwordconnect" value=""/>
						</br>	
						</li>
						<li>
						<label for="valider">Connexion</label>
						</br>
						<input type="submit" name="connexion" value="GO!"></input>
						<input type="reset" name="reset" id="Reinitialiser" value="Reinitialiser" ></code>
						</br>
						</br>
						<input type="checkbox" name="remember" id="remembercheckbox"/><label for="remembercheckbox">Se souvenir de moi</label>
						</br>	
						</li>
					</form>
				</ul>
			</li>
			
			<li id="inscription" class="list">
			<h3>
				Inscription
			</h3>
			<ul id="maliste2">
			<form method="POST" action="">
				<!-- je n'ai pas crypté le mdp mais je l'ai fait passer par un bon vieux post ! -->
				<li>
				<label for="pseudo">Votre pseudo</label>
				</br>
				<input type="text" value="" id="pseudo" name="pseudo" value="<?php if(isset($pseudo)) { echo $pseudo; } ?>"/>
				</br>	
				</br>
				</li>
				<li>
				<label for="password">Votre password</label>
				</br>
				<input type="password" value="" id="password" name="password"/>
				</br>	
				</br>
				</li>
				<li>
				<label for="confirme_mdp">Confirmation</label>
				</br>
				<input type="password" value="" id="confirme_mdp" name="confirme_mdp"/>
				</br>	
				</br>
				</li>
				<li>
				<label for="date_naissance">Votre date de naissance</label>
				</br>
				<input type="date" value="" id="date_naissance" name="date_naissance" value="<?php if(isset($date_naissance)) { echo $date_naissance; } ?>"/>
				</br>	
				</br>
				</li>
				<li>
				<label for="pays">Votre pays</label>
				</br>
				<select name="pays" id="pays">
					<option value="France">France</option>
					<option value="Espagne">Espagne</option>
					<option value="Italie">Italie</option>
					<option value="Royaume-Uni">Royaume-Uni</option>
					<option value="Canada">Canada</option>
					<option value="Etats-unis">Etats-Unis</option>
					<option value="Chine">Chine</option>
					<option value="Japon">Japon</option>
				</select>
				</br>	
				</br>
				</li>
				<li>
				<label for="email">Votre email</label>
				</br>
				<input type="email" value="" id="email"name="email" value="<?php if(isset($email)) { echo $email; } ?>" />
				</br>	
				</br>
				</li>
				<li>
				<label for="confirme_mail">Confirmation</label>
				</br>
				<input type="email" value="" id="confirme_mail" name="confirme_mail" value="<?php if(isset($confirme_mail)) { echo $confirme_mail; } ?>"/>
				</br>	
				</br>
				</li>
				<li>
				<label for="go">Inscription</label>
				</br>
				<input type="submit" name="inscription" value="GO!"/>
				<input type="reset" name="reset" id="Reinitialiser" value="Reinitialiser" ></code>
				</li>
			</form>
			</ul>	
		</li>
		</ul>
		<?php
		  if(isset($erreur)) 
		  {
				echo '<font color="red">'.$erreur."</font>";        
		  }
		?>
		</div>
		
		</div>
		
		<div id="particles-js"></div>

		<!-- scripts -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>
	</body>
</html>