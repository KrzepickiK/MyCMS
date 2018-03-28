<?php
function wyswietl_newsy()
{
    global $skorka;
    global $theme_cfg;
    polacz();
        $query = mysql_query("select * from `newsy`");
        while($rekord = mysql_fetch_array($query))
        {   
            $widocznosc             = $rekord['widocznosc'];
            $newsId                 = $rekord['id'];
            $autorId                = $rekord['autorId'];
            $tresc                  = $rekord['tresc'];
            $data                   = data($rekord['data']);
            $komentarze             = $rekord['komentarze'];
            $tytul                  = $rekord['tytul'];
            $rekord['zalacznikId']  = explode('#',$rekord['zalacznik']);
            $rodzicId               = $rekord['rodzicId'];
            if($rodzicId!=0)
            {
                $rodzic = mysql_query("select `link` from `podstrony` where `id` = $rodzicId;");
                $rodzic = mysql_fetch_array($rodzic);
                $rodzicLink = $rodzic['link'];
            }else{$rodzicLink = 'Brak';}
                
            
            $query2             = mysql_query ("select * from `uzytkownicy` where id = $autorId");
            $rekord2            = mysql_fetch_array($query2);
            
            $autor['nick']      = $rekord2['nick'];
            $autor['avatar']    = $rekord2['avatar'];
            $autor['ranga']     = $rekord2['ranga'];
            $autor['email']     = $rekord2['email'];
            
            $policz_newsy       = mysql_query("SELECT COUNT(id) as newsow FROM newsy where autorId = $autorId;");
            $ilosc_newsow       = mysql_fetch_array($policz_newsy);
            $policz_podstrony   = mysql_query("SELECT COUNT(id) as podstron FROM podstrony where autorId = $autorId;");
            $ilosc_podstron     = mysql_fetch_array($policz_podstrony);
            

            $autor['podstron']  = $ilosc_podstron['podstron'];
            $autor['newsow']    = $ilosc_newsow['newsow'];
            
            $ile_zalacznikow    = count($rekord['zalacznikId']);
            $zalacznik          = '';
            $li_zalacznik       = '';
            $zalaczniki = '';
            for ($i=0; $i <$ile_zalacznikow - 1; $i++)
            {
                $zalacznikId    = $rekord['zalacznikId'][$i];
                $query3         = mysql_query("select * from zalaczniki where `id`=$zalacznikId");
                $zalacznik[$i]  = mysql_fetch_array($query3);
                $zalaczniki    .= $zalacznik[$i]['nazwa'].'.'.$zalacznik[$i]['rozszerzenie'].'<br />'; 
            }
            include('theme/'.$skorka.$theme_cfg['news']['wyswietl']);
        }   
        rozlacz();         
}
?>