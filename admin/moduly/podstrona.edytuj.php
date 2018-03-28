<?php
function edytuj_podstrone_lista()
{   global $skorka;
    global $theme_cfg;
    $li = '';
     polacz();
     $zapytanie = "SELECT `id`, `link` FROM `podstrony` ORDER BY `pozycja` ASC";
	 $zapytanie = mysql_query($zapytanie);
	 if(!$zapytanie){echo '<li>Błąd przy tworzeniu listy</li>';}
	 $ile = mysql_num_rows($zapytanie);
	 if($ile == 0){$li = '<li>Brak podstron do edycji</li>';}
	 while($row=mysql_fetch_row($zapytanie))
	 {
		 $id     = $row[0];
		 $tytul  = $row[1];
         $li    .= '<li><a href="index.php?go=epf&id='.$id.'" title="'.$tytul.'">'.$tytul.'</a></li>';
     }
     rozlacz();
        include('theme/'.$skorka.$theme_cfg['podstrona']['lista']);
}

function edytuj_podstrone_form()
{
    global $skorka;
    global $theme_cfg;    
        $id = $_GET['id'];
        polacz();
        $query = mysql_query("select * from `podstrony` where `id`=$id");
        $rekord = mysql_fetch_array($query);
        rozlacz();   
        $link           =$rekord['link'];
        $tytul          =$rekord['tytul'];
        $tresc          =$rekord['tresc'];
        polacz();
            $rekord['zalacznikId']  = explode('#',$rekord['zalacznik']);
            $ile_zalacznikow    = count($rekord['zalacznikId']);
            $tr_zalacznik       = '';
            for ($i=0; $i <$ile_zalacznikow - 1; $i++)
            {
                $zalacznikId    = $rekord['zalacznikId'][$i];
                $query3         = mysql_query("select * from zalaczniki where `id`=$zalacznikId");
                $zalacznik[$i]  = mysql_fetch_array($query3);
                $tr_zalacznik   .= '<tr><td>'.$zalacznik[$i]['nazwa'].'</td><td><a href="id='.$zalacznik[$i]['id'].'">Usuń!</a></td><td><a href="id='.$zalacznik[$i]['id'].'">Zmień</a></td></tr>';
            }
        rozlacz(); 
        $autor          =$rekord['autorId'];
        polacz();
        $query = mysql_query("select `nick` from `uzytkownicy` where `id`=$autor");
        $autorNick = mysql_fetch_array($query);
        rozlacz();
        $widocznosc     =$rekord['widocznosc'];
        $komentarze     =$rekord['komentarze'];
        $pozycja        =$rekord['pozycja'];
        $menu           =$rekord['menu'];
        $rodzicId       =$rekord['rodzicId'];
        if($rodzicId!=0)
        {
            polacz();
            $query = mysql_query("select `link` from `podstrony` where `id`=$rodzicId");
            $rodzicName = mysql_fetch_array($query);
            rozlacz(); 
        }else{$rodzicName['link']='Brak'; $rodzicId=0;}
        
        $lista_rodzic =    "<SELECT NAME=\"rodzic\" style=\"width: 140px;\">".opcje("podstrony", $rodzicName['link'], $rodzicId)."</SELECT>";
        include('theme/'.$skorka.$theme_cfg['podstrona']['edytuj']);

        
            
}

function pytanie_edytuj($id)
{
    $zapytanie = "SELECT `tytul` FROM `podstrony` WHERE `id` = $id";
	 polacz();
	 $zapytanie = mysql_query($zapytanie);
	 if(!$zapytanie)
	 {
		 echo 'Czy na pewno chcesz zmienić podstronę?';
	 }
     else
     {
        $rekord = mysql_fetch_array($zapytanie);
        $tekst = "<h1>Czy na pewno chcesz zmienić podstronę \"$rekord[0]\"?</h1>";
     rozlacz();
        echo $tekst; 
        echo "<h2><a href=\"index.php?go=eps&pytanie=Tak&id=$id\">.::Tak::.</a>";
        echo "<a href=\"index.php?go=eps&pytanie=nie\">.::Nie::.</a></h2>";
               
     } 
}

function edytuj_podstrone_submit()
{
            $id         = $_POST['id'];
            $rodzicId   = $_POST['rodzic'];
            $link       = $_POST['link'];
            $tytulPS    = $_POST['tytul'];
            $tresc      = $_POST['tresc'];
            $autorId    = $_POST['autorId'];
            $widocznosc = $_POST['widocznosc'];
            $komentarze = $_POST['komentarze'];
            $menu       = $_POST['menu'];
            
    polacz();
    $query          = mysql_query("select `zalacznik` from `podstrony` where `id`=$id");
    $zalacznikiId   = mysql_fetch_array($query);
    $zalacznikiId   = $zalacznikiId[0];
    rozlacz();
            
    if(!empty($_POST['zalaczplik']))
    {
        if($_POST['zalaczplik'] == true){$zalacznik = dodaj_zalacznik('upload/podstrony',30000000,true);}  
    }
    if (!empty($zalacznik))
    {
        $explode        = explode('.',$zalacznik['nazwa']);
        $nazwa          = @$explode[0];
        $rozszerzenie   = @$explode[1];
        $autorId        = $_SESSION['user']['id'];
        $opis           = $_POST['zal_opis'];
        $tytul          = $_POST['zal_tytul'];
        $sciezka        = $zalacznik['sciezka'];
        $rozmiar        = $zalacznik['rozmiar'];
        $rozmiar        = ($rozmiar/1024)/1024;
        $rozmiar        = round($rozmiar, 2);
        $kategoria      = 'podstrony';
        $dostep_rangaId = $_POST['zal_dostepnosc'];
        polacz();
        $query = "INSERT INTO zalaczniki VALUES ('', '$autorId', CURRENT_TIMESTAMP, '$nazwa','$tytul', '$opis', '$rozszerzenie', '$rozmiar', '$dostep_rangaId','$sciezka','$kategoria')";
        $idquery = mysql_query($query) or Die ('Błąd zapytania SQL! <br /> Poinformuj administratora!');
        
        $query = "SELECT `id` FROM `zalaczniki` ORDER BY `id` DESC limit 1";
        $query = mysql_query($query);
        while($row=mysql_fetch_row($query)){$zalacznikId     = $row[0];}
        rozlacz();
        $zalacznikiId  .= $zalacznikId.'#';
        
    }else{$zalacznikiId .='';}
            polacz();
            mysql_query(
            "UPDATE `podstrony` SET 
            rodzicId    = '$rodzicId',
            tytul       = '$tytulPS',
            tresc       = '$tresc',
            widocznosc  = '$widocznosc',
            komentarze  = '$komentarze',
            pozycja     = '0',
            menu        = '$menu',
            link        = '$link',
            zalacznik   = '$zalacznikiId'
            WHERE `id`  = '$id'") or die('Błąd zapytania');
            rozlacz();
            print '<h1>Uaktualniono!</h1>';
            #echo "<script>setTimeout('document.location = \"index.php\"', 3000);</script>";    
}
?>
