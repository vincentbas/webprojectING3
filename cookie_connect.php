<?php
if(!isset($_SESSION['id']) AND isset($_COOKIE['pseudo'],$_COOKIE['password']) AND !empty($_COOKIE['pseudo']) AND !empty($_COOKIE['password']))
{
  $connectmbr = $bdd->prepare("SELECT * FROM users WHERE pseudo = ? AND password = ?");
  $connectmbr->execute(array($_COOKIE['pseudo'], $_COOKIE['password']));
  $userexist = $connectmbr->rowCount();
      if($userexist == 1) 
	  {
         $userinfo = $connectmbr->fetch();
         $_SESSION['id'] = $userinfo['id'];
         $_SESSION['pseudo'] = $userinfo['pseudo'];
		 $_SESSION['date_naissance'] = $userinfo['date_naissance'];
		 $_SESSION['pays'] = $userinfo['pays'];
         $_SESSION['email'] = $userinfo['email'];
      } 
}
 ?> 