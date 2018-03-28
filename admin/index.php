<?php
ob_start();
session_start();
if (!isset($_SESSION['kontrola_sesji'])){
    session_regenerate_id();
    $_SESSION['kontrola_sesji']=true;
    $_SESSION['adres_ip']=$_SERVER['REMOTE_ADDR'];
}
if($_SESSION['adres_ip']!==$_SERVER['REMOTE_ADDR']){echo 'Błąd: Próba przejęciaa sesji!';exit;}
if(empty($_SESSION["zalogowany"])){$_SESSION["zalogowany"]=false;}
if(empty($_SESSION["user"])){$_SESSION["user"]=false;}

/*------------------------------------------------+
|    Universal_CMS - System Zarządzania Treścią   |
+-------------------------------------------------+
|               Copyright  Krzysztof Krzepicki   |
|               Wszelkie prawa zastrzeżone        |
|               http://universalcms               |
+------------------------------------------------*/
$db     = file('../db.php'); //plik z konfiguracją bazy danych
include ('../func.php');

if(!empty($_GET['go'])){$go = $_GET['go'];}else{$go='admin';}
            switch ($go)
        	{
                case 'admin':
                    default:
                    if($_SESSION["zalogowany"] != true)
                    {
                    print '<form method="POST" action="index.php?go=loguj" style="padding-left:100px;">
                    <table cellpadding="0" cellspacing="0" width="180">
                    <tr><td><br></td></tr>
                    <tr><td width="50">Login:</td><td><input type="text" name="login" maxlength="32"></td></tr>
                    <tr><td width="50">Hasło:</td><td><input type="password" name="haslo" maxlength="32"></td></tr>
                    <tr><td align="center" colspan="2"><input type="submit" value="Zaloguj"><br></td></tr>
                    </table>
                    </form>';
                    }
                    else
                    {
                    $szablon = ktory_szablon();
                    $skorka  = $szablon;
                    $szablon = 'theme/'.$szablon.'/index.html';
                    include $szablon;
                    }
                break;
                
                case 'loguj':
                    pokaz(login());
                break;
                
                case 'wyloguj':
                    pokaz(wyloguj());       
                break;
            }
ob_end_flush();
?>
