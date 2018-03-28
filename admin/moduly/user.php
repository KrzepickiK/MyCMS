<?php
function user_list()
{   global $skorka;
    global $theme_cfg;
     polacz();
     $zapytanie = "SELECT * FROM `uzytkownicy` ORDER BY `nick` ASC";
	 $zapytanie = mysql_query($zapytanie);
	 if(!$zapytanie){echo 'Błąd przy tworzeniu listy';}
	 $ile = mysql_num_rows($zapytanie);
	 if($ile == 0){$li = '';}
	 while($row=mysql_fetch_row($zapytanie))
	 {
		 $id     = $row[0];
		 $nick  = $row[1];
         include('theme/'.$skorka.$theme_cfg['user']['lista']);
     }
     rozlacz();
        
}

?>