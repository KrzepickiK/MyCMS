<?php
function dodaj_newsa_form()
{
    global $skorka;
    global $theme_cfg;
    $lista_rodzic =    "<SELECT NAME=\"rodzic\" style=\"width: 80px;\">".opcje("podstrony", "Brak", 0)."</SELECT>";
    include('theme/'.$skorka.$theme_cfg['news']['dodaj']);
}

function dodaj_newsa_submit()
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
        $kategoria      = 'newsy';
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
    $okladka        = $_POST['okladka'];
    
    polacz();
    $query = "INSERT INTO newsy VALUES ('', '$tytul', '$tresc', '$okladka', '$widocznosc', '$autorId', CURRENT_TIMESTAMP, '$komentarze', '$zalacznik')";
    $idquery = mysql_query($query) or Die ('Błąd zapytania SQL! <br /> Poinformuj administratora!');
    echo '<h1>Stronę dodano!</h1>';
    #echo "<script>setTimeout('document.location = \"index.php\"', 1000);</script>";
    rozlacz();
}


function generateThumbnail($filename, $extension, $sciezka,$config)
{
    // sprawdzenie, czy plik o podanej nazwie już nie istnieje
    if ( file_exists($sciezka . $filename) ){unlink($config['path_thumbnails'].$filename);}
    // stworzenie nowej grafiki wg typu
    switch ( $extension )
    {
        case 'gif':
        $ic = imagecreatefromgif($config['path_images'].$filename);
        break;
        case 'png':
        $ic = imagecreatefrompng($config['path_images'].$filename);
        break;
        default:
        $ic = imagecreatefromjpeg($config['path_images'].$filename);
        break;
    }
    if ( $ic === false )
    {
    return 'Miniaturka nie stworzona!';
    }
    $is = getimagesize($config['path_images'] . $filename); // [0] - szeroko¶ć, [1] - wysoko¶ć
    switch ( $config['thumbnail_scale'] )
    {
        case false: // zmniejszanie bezpo¶rednio do 180x160px
        $nts = imagecreatetruecolor($config['thumbnail_width'], $config['thumbnail_height']);
        imagecopyresized($nts, $ic, 0, 0, 0, 0, $config['thumbnail_width'], $config['thumbnail_height'], $is[0], $is[1]); 
        break;
        default: // zmniejszanie z zachowaniem skali, aż do osi±gnięcia co najmniej 180x160px
        $width = $is[0];
        $height = $is[1];
        $ratio = $is[0] / $config['thumbnail_width'];
        if ( $ratio > 1 )
        {
        $width = $config['thumbnail_width'];
        $height = intval($is[1] / $ratio);
        }
        $ratio = $height / $config['thumbnail_height'];
        if ( $ratio > 1 )
        {
        $width = intval($width / $ratio);
        $height = $config['thumbnail_height'];
        }
        $nts = imagecreatetruecolor($width, $height); // utworzenie obrazka o podanych rozmiarach z czarnym tłem
        imagecopyresized($nts, $ic, 0, 0, 0, 0, $width, $height, $is[0], $is[1]);                  
        break;
    }
    switch ( $extension ) // zapis do pliku
    {
        case 'gif':
        imagegif($nts, $config['path_thumbnails'].$filename); 
        break;
        case 'png':
        imagepng($nts, $config['path_thumbnails'].$filename); 
        break;
        default:
        imagejpeg($nts, $config['path_thumbnails'].$filename); 
        break;
    }
    imagedestroy($nts); // zniszczenie obrazka
    return $config['path_thumbnails'].$filename;
    }
    
    
function dodaj_obrazek($folder)
{
    ini_set('memory_limit', '64M'); // limit pamięci dla wykonywanego skryptu, w megabajtach
    ini_set('post_max_size', '32M'); // upload_max_filesize + dane z wszelkich innych pól formularza, w megabajtach
    ini_set('upload_max_filesize', '32M'); // wielko¶ć przesyłanych plików, w megabajtach
    ini_set('session.gc_maxlifetime', '5400'); // czas wykonywania skryptu w sekundach, 90 minut
    ini_set("max_execution_time","1036800");
 
    $folder_zdjecia = '../pliki/'.$folder.'/img/';
    $folder_miniatury = '../pliki/'.$folder.'/thumb/';
    
    $config = array();
    $config['thumbnail_width']      = 120; // maksymalna szeroko¶ć miniatury w pikselach
    $config['thumbnail_height']     = 200; // maksymalna wysoko¶ć miniatury w pikselach
    $config['thumbnail_scale']      = true; // czy przy minimalizowaniu wielko¶ci zachowywać skalę?
    $config['path_images']          = "$folder_zdjecia"; // ¶cieżka do katalogu grafik
    $config['path_thumbnails']      = "$folder_miniatury"; // ¶cieżka do katalogu miniatur
    $config['max_file_size']        = 1048576; // maksymalna wielko¶ć pliku w bajtach, 1MB
    $config['accepted_extensions']  = array('jpg', 'jpeg', 'png', 'gif'); // dozwolone rozszerzenia
    $config['accepted_mimes']       = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'); // dozwolone typy MIME
    
    if ( isset($_FILES['upload_image']) && isset($_POST['upload']) )
    {
        if ( !file_exists($config['path_images']) || !is_dir($config['path_images']) ) {
        if ( !mkdir($config['path_images']) ) { // utworzenie katalogu wraz
        exit('Folder obrazków niemożliwy do utworzenia!');
        }
        chmod($config['path_images'], 0777); // nadanie praw
        }
        if ( !file_exists($config['path_thumbnails']) || !is_dir($config['path_thumbnails']) ) {
        if ( !mkdir($config['path_thumbnails']) ) { // utworzenie katalogu
        exit('Folder miniaturek niemożliwy do utworzenia!');
        }
        chmod($config['path_thumbnails'], 0777); // nadanie praw
        }
        if ( !extension_loaded('gd') ) { // sprawdzenie, czy GD jest załadowane
        if ( !dl('gd') ) { // próba wymuszenia załadowania
        exit('Biblioteka GD nie została załadowana!');
        }
        }
        
            if ( $_FILES['upload_image']['error'] != UPLOAD_ERR_OK ){continue;}// bł±d wysyłania pliku
            if ( $_FILES['upload_image']['size'] > $config['max_file_size'] ){continue;}// plik jest za duży
            if ( file_exists($config['path_images'] . $_FILES['upload_image']['name']) ){continue;}// poinformowanie użytkownika o fakcie, że plik o takiej nazwie już istnieje
            $extension = explode('.', $_FILES['upload_image']['name']);
            if ( in_array(strtolower($extension[count($extension)-1]), $config['accepted_extensions']) === false || in_array($_FILES['upload_image']['type'], $config['accepted_mimes']) === false ){continue;}// niepoprawne rozszerzenie pliku
            $image = getimagesize($_FILES['upload_image']['tmp_name']);
            if ( !is_array($image) || $image[0] < 1 ){continue;}// plik graficzny jest spreparowany
            if ( !is_uploaded_file($_FILES['upload_image']['tmp_name']) ){continue;}// plik nie został wysłany
            if ( !move_uploaded_file($_FILES['upload_image']['tmp_name'], $config['path_images'] . $_FILES['upload_image']['name']) ){continue;}// wysyłanie nie może zostać zakończone poprawnie
            generateThumbnail($_FILES['upload_image']['name'], $extension[count($extension)-1], $config['path_thumbnails'],$config); // funkcja tworz±ca miniaturkę
    }
}

?>