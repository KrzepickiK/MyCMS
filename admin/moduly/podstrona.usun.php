<?php
function usun_podstrone_lista()
{       print '<ul class="menu">';
    $zapytanie = "SELECT `id`, `tytul` FROM `podstrony` ORDER BY `pozycja` ASC";
	 polacz();
	 $zapytanie = mysql_query($zapytanie);
	 if(!$zapytanie)
	 {
		 echo '<li>Błąd przy tworzeniu menu</li>';
	 }
	 $ile = mysql_num_rows($zapytanie);
	 if($ile == 0)
	 {
		 echo '<li>Brak elementów menu</li>';
	 }
	 while($row=mysql_fetch_row($zapytanie))
	 {
		 $id = $row[0];
		 $tytul = $row[1];

            echo '<li><a href="index.php?go=ups&id='.$id.'" title="'.$tytul.'">'.$tytul.'</a></li>';
		 
	 } 
        print '</ul>';
}

function usun_podstrone_submit()
{
    polacz();
            $id = $_GET['id'];
            if ($id == '1')
            {
                print '<h1>Strony głównej nie można usunąć!</h1><br /><h2>Ale można ją zmienić: <a href="index.php?go=epf&id=1">.::EDYTUJ::.</a></h2>';
            }
            else
            {
                
            mysql_query("DELETE FROM `podstrony` WHERE id=$id");
rozlacz();
            print '<h1>Podstronę usunięto!</h1>';
            echo "<script>setTimeout('document.location = \"index.php\"', 3000);</script>"; 
            }   
}

function pytanie_usun()
{
    $tekst = '';
    $id = $_GET['id'];
    $zapytanie = "SELECT `tytul` FROM `podstrony` WHERE `id` = $id";
	 polacz();
	 $zapytanie = mysql_query($zapytanie);
	 if(!$zapytanie)
	 {
		 echo 'Czy na pewno chcesz usunąć podstronę?';
	 }
     else
     {
        $rekord = mysql_fetch_array($zapytanie);
        $tekst .= "<h1>Czy na pewno chcesz usunąć podstronę \"$rekord[0]\"?</h1>";
     rozlacz();
        echo $tekst; 
        echo "<h2><a href=\"index.php?go=ups&pytanie=tak&id=$id\">.::Tak::.</a>";
        echo "<a href=\"index.php?go=ups&pytanie=nie\">.::Nie::.</a></h2>";
               
     } 
}      
       
       
?>