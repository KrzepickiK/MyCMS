<?php
function polacz(){
    include "db.php"; //plik konfiguracji dostepu do bazy

    $connection = @mysqli_connect($host, $user, $haslo, $db);
    $result = @$connection -> query('SELECT * FROM config');
	
		if (!mysqli_set_charset($connection, "utf8")) {
			printf("Błąd ładowania bazy z kodowaniem utf8: %s\n", mysqli_error($connection));
			exit();
		}else if ($result === false){
			echo '<p>Błąd łączenia z bazą danych</p> <br/> <p>Kod błędu: '.mysqli_connect_errno().' <br/><p>Poinformuj administratora!</p>';
			$connection -> close();
    }else{return $connection;}
}
function pobierz($query){
    $connection = polacz();
    $wynik = mysqli_query($connection, $query);
    
    $row = mysqli_fetch_array($wynik, MYSQLI_ASSOC);
    mysqli_free_result($wynik);
    mysqli_close($connection);
    return $row;
}
function banicja(){
    $banIp = $_SESSION['adres_ip'];
    $connection = polacz();//Sprawdzenie czy odwiedzaj±cy zbanowany czy nie .//.(W przyszło¶ci zastosować EVERCOOKIE!!)
    $wynik = mysqli_query($connection,"SELECT `ip` FROM `ban` WHERE `ip`='".$banIp."'");
    $ile = mysqli_num_rows ($wynik);
    if($ile != 0){
        print "Zostałeś zbanowany";
        die();}
    mysqli_free_result($wynik);
    mysqli_close($connection);
}
function pageConfig(){
    $rows = pobierz("SELECT * FROM config");
    return $rows;
}
function pokaz($string){print $string;}
function theme(){
    $row = pobierz("SELECT theme FROM config");
    return $row["theme"];
}
function page($id){
    $rows = pobierz("SELECT * FROM pages WHERE `id`='".$id."'");
    echo '<pre>';
	print_r($rows);
	echo '</pre>';
}
function menu($nr){
	
	/*
    $rows = pobierzKilka("SELECT title, id FROM pages WHERE `menu`='".$nr."'");
    $n = count($rows);
    for ($i=0;$i<$n; $i++)
    {
        foreach ($rows[$i] as $klucz => $wartosc)
        print '<a href="'.$klucz.'/'.$wartosc.'">'.$wartosc.'</a><br />';
		//echo "tab['".$klucz."'] ==". $wartosc;
    }
	*/
		$connection = polacz();
		$query = "SELECT title, id FROM pages WHERE `menu`='1'";
    $wynik = mysqli_query($connection, $query);
    
    while($row = mysqli_fetch_array($wynik, MYSQLI_ASSOC)){
			print '<li><a href="index.php">'.$row['title'].'</a></li>';
			
		}
	
    mysqli_free_result($wynik);
    mysqli_close($connection);
}
function pobierzKilka($query){
    $i = 0;
    $connection = polacz();
    $wynik = mysqli_query($connection, $query);
    while($rekord = mysqli_fetch_array($wynik,MYSQLI_ASSOC))
    {
        $rows[$i] = $rekord;
        $i++;
    }
    mysqli_free_result($wynik);
    mysqli_close($connection);
    return $rows;
}
function show($string){
	print $string;
}

































?>