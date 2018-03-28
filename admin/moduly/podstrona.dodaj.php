<?php
function dodaj_podstrone_form()
{
    global $skorka;
    global $theme_cfg;
    $lista_rodzic =    "<SELECT NAME=\"rodzic\" style=\"width: 80px;\">".opcje("podstrony", "Brak", 0)."</SELECT>";
    include('theme/'.$skorka.$theme_cfg['podstrona']['dodaj']);
}

function dodaj_podstrone_submit()
{
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
        $dostep_rangaId = $_POST['zal_dostepnosc'];
        $kategoria      = 'podstrony';
        polacz();
        $query = "INSERT INTO zalaczniki VALUES ('', '$autorId', CURRENT_TIMESTAMP, '$nazwa','$tytul', '$opis', '$rozszerzenie', '$rozmiar', '$dostep_rangaId','$sciezka','$kategoria')";
        $idquery = mysql_query($query) or Die ('Błąd zapytania SQL! <br /> Poinformuj administratora!');
        
        $query = "SELECT `id` FROM `zalaczniki` ORDER BY `id` DESC limit 1";
        $query = mysql_query($query);
        while($row=mysql_fetch_row($query)){$zalacznikId     = $row[0];}
        rozlacz();
        $zalacznik      = $zalacznikId.'#';
        
    }else{$zalacznik = null;}
    
    
    $rodzicId       = $_POST['rodzic'];
    $tytul          = $_POST['tytul'];
    $tresc          = $_POST['tresc'];
    $autorId        = $_SESSION['user']['id'];
    $widocznosc     = $_POST['widocznosc'];
    $komentarze     = $_POST['komentarze'];
    $pozycja        = 0;
    $menu           = $_POST['menu'];
    $link           = $_POST['link'];
    
    polacz();
    $query = "INSERT INTO podstrony VALUES ('', '$rodzicId', '$tytul', '$tresc', '$autorId', CURRENT_TIMESTAMP, '$widocznosc', '$komentarze', '$pozycja', '$menu', '$link', '$zalacznik')";
    $idquery = mysql_query($query) or Die ('Błąd zapytania SQL! <br /> Poinformuj administratora!');
    echo '<h1>Stronę dodano!</h1>';
    #echo "<script>setTimeout('document.location = \"index.php\"', 1000);</script>";
    rozlacz();
}

function dodaj_zalacznik($dir, $max_file_size,$zmiana_nazwy)
{
    $sciezka_do_pliku = $dir;
    $dir            = '../'.$dir.'/';
    $max_file_size  = $max_file_size;
    $change_name    = $zmiana_nazwy; 
    $name_length    = 10;
    $losowa = rand(1,999);
    
    if(!file_exists($dir)) exit('Katalog '.$dir.' nie istnieje!'); #sprawdzenie czy jest katalog $dir
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    
        $tmp_name   = $_FILES['userfile']['tmp_name'];
        $name       = $_FILES['userfile']['name'];
        $type       = $_FILES['userfile']['type'];
        $size       = $_FILES['userfile']['size'];
        $error      = $_FILES['userfile']['error'];
       
        $explode_name   = explode('.',$name);
        $extension      = @$explode_name[1];
       
        if($change_name) {
            $name       = $explode_name[0];
            $new_name   = substr($losowa.md5($name),0,$name_length).'.'.$extension;
            $name       = $new_name;
            $path       = $dir.$new_name;
            $sciezka_do_pliku = $sciezka_do_pliku.'/'.$new_name;
        }
        else {
            $path = $dir.$name;
            $sciezka_do_pliku = $sciezka_do_pliku.'/'.$name;
        }
       
        $dirname = dirname($_SERVER['SCRIPT_NAME']) == '/' || dirname($_SERVER['SCRIPT_NAME']) == '\\' ? null : dirname($_SERVER['SCRIPT_NAME']);
       
        $full_path = 'http://'.$_SERVER['HTTP_HOST'].$dirname.'/'.$path;
       
        if($error == UPLOAD_ERR_NO_FILE) {echo 'Wybierz plik.';}
        elseif($error == UPLOAD_ERR_PARTIAL) {echo 'Błąd! Plik został tylko częściowo załadowany.';}
        elseif($error == UPLOAD_ERR_NO_TMP_DIR) {echo 'Błąd! Brak folderu tymczasowego.';}
        elseif($error == UPLOAD_ERR_INI_SIZE) {echo 'Błąd! Plik jest za duży dla serwera.';}
        elseif($size > $max_file_size) {echo 'Za duży plik.';}
        else 
        {
            if(is_uploaded_file($tmp_name)) 
            {
                if(move_uploaded_file($tmp_name,$path)) 
                {
                    echo 'Plik został wysłany. <br /><a href="'.$full_path.'">'.$full_path.'</a><br><br>';
                    $zalacznik['nazwa']     = $name;
                    $zalacznik['rozmiar']   = $size;
                    $zalacznik['sciezka']   = $sciezka_do_pliku;
                    return $zalacznik;
                }else {echo 'Nie udało się wysłać pliku. Spróbuj później.';}
            }else {echo 'Co ty próbujesz.';}
        }   
    }
}
?>