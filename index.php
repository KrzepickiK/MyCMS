<?php
ob_start();
session_start();
if (!isset($_SESSION['kontrola_sesji'])){
    session_regenerate_id();
    $_SESSION['kontrola_sesji'] = true;
    $_SESSION['adres_ip'] = $_SERVER['REMOTE_ADDR'];}
if($_SESSION['adres_ip'] !== $_SERVER['REMOTE_ADDR']){echo 'Błąd: Próba przejęciaa sesji!';exit;}
if(empty($_SESSION["zalogowany"])){$_SESSION["zalogowany"]=false;}
if(empty($_SESSION["user"])){$_SESSION["user"]=false;}

/*------------------------------------------------+
|   TENSE_MyCMS - system zarządzania treścią      |
+-------------------------------------------------+
|               Copyright  Krzysztof Krzepicki    |
|               Wszelkie prawa zastrzeżone        |
|                                            	    |
+------------------------------------------------*/
$db         = file('db.php');     //plik z konfiguracją bazy danych
include ('func.php');           //funkcje 
//banicja();
$theme      = 'theme/'.theme();
$szablon    = $theme.'/index.html';
include $szablon;       //wczytanie odpowiedniego szablonu
ob_end_flush();
?>
