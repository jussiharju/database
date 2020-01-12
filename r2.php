<?php

// luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheestä

$y_tiedot = "host=dbstud2.sis.uta.fi port=5432 dbname=tiko2019r8 user=jh424450 password=salasana";

if (!$yhteys = pg_connect($y_tiedot))
   die("Tietokantayhteyden luominen epäonnistui.");

// isset funktiolla jäädään odottamaan syötettä.
// POST on tapa tuoda tietoa lomaketta (tavallaan kutsutaan lomaketta).
// Argumentti tallenna saadaan lomakkeen napin nimestä.

if (isset($_POST['tallenna'])) {
    
    $ts = intval($_POST['suoritus']);
    
    $laskuid = pg_query("SELECT tstunnus FROM lasku WHERE tstunnus = $ts;");
    
    if (pg_fetch_row(pg_query("SELECT tstunnus FROM tyosuoritus WHERE tstunnus = $ts")) && !pg_fetch_row($laskuid)) {
       
       $tyot = pg_query("SELECT tuntimaara,alennusprosentti,hinta,tyyppi FROM tyolista NATURAL JOIN tyohinnasto WHERE tstunnus = $ts");
       $tarv = pg_query("SELECT myyntihinta,maara,alennus,alv,nimike,yksikko FROM tarvike NATURAL JOIN tarvikelista WHERE tstunnus = $ts");
       
       $tyosumma = 0;
       
       $tarvsumma = 0;
       $tarvalv = 0;
       
       $asiakas = pg_fetch_row(pg_query("SELECT animi,aosoite,tknimi,tkosoite FROM asiakas NATURAL JOIN tyokohde NATURAL JOIN tyosuoritus WHERE tstunnus = $ts"));
       
       
       // tulostetaan laskun tiedot
       echo 'Asiakas: ' . $asiakas[0] . '<br />' . ' Osoite: ' . $asiakas[1] . '<br />';
       echo 'Työkohde: ' . $asiakas[2] . '<br />' . ' Osoite: ' . $asiakas[3] . '<br />' . '<br />';
       echo 'Tarvikkeet: <br />';
       while ($rivi = pg_fetch_row($tarv)) {
          echo '-' . $rivi[4] . ', ' . $rivi[1] . ' ' . $rivi[5] . ' -- ' . $rivi[0] * $rivi[2] . '€/' . $rivi[5] . '<br />';
          $tarvsumma += $rivi[0] * $rivi[1] * $rivi[2];
          $tarvalv += $rivi[0] * $rivi[1] * $rivi[2] * $rivi[3] / 100;
       }
       echo 'Yhteensä: ' . $tarvsumma . '€ <br />' . '<br />' . 'Työt:  <br />';
       while ($rivi = pg_fetch_row($tyot)) {
          $tyosumma += $rivi[0] * $rivi[1] * $rivi[2];
          if (trim($rivi[3]) == 'tyo') {
             echo '-Työtunteja: ' . $rivi[0] . ' -- ' . $rivi[2] * $rivi[1] . '€/h <br />';
          }
          if (trim($rivi[3]) == 'aputyo') {
             echo '-Aputyötunteja: ' . $rivi[0] . ' -- ' . $rivi[2] * $rivi[1] . '€/h <br />';
          }
          if (trim($rivi[3]) == 'suunnittelu') {
             echo '-Suunnittelutunteja: ' . $rivi[0] . ' -- ' . $rivi[2] * $rivi[1] . '€/h <br />';
          }
       }
       echo 'Yhteensä: ' . $tyosumma . '€ <br />' . '<br />';
       
       $alvsumma = round($tarvalv + ($tyosumma * 24 / 100), 2);
       $summa = round($tyosumma + $tarvsumma, 2);
       
       echo 'Yhteissumma: ' . $summa . '€, josta kotitalousvähennyskelpoista on ' . $tyosumma . '€. <br />';
       
       // muodostetaan lasku tietokantaan
       
       // valitaan laskulle tunnus (suurin ltunnus + 1)
       $idkysely = "SELECT MAX(ltunnus) FROM lasku;";
       $idtulos = pg_fetch_row(pg_query($idkysely));
       if (strlen($idtulos[0]) == 0) {
          $ltunnus = 501;
       }
       else {
          $ltunnus = intval($idtulos[0]) + 1;
       }
       
       $alvs = $alvsumma / $summa * 100;

       $lis_lasku = "INSERT INTO lasku VALUES ($ltunnus, 1, 1, $alvs, 'valmis', $summa, current_date + 14, current_date, NULL, $tyosumma, $ts, NULL)";
       $lisays = pg_query($lis_lasku);
       
       if (!$lisays) {
          echo pg_last_error();
       }
       else {
          $viesti = 'Lasku muodostettu.';
       }
       
    }
    else {
       $viesti = 'Työsuoritus ei ole tietokannassa tai lasku on jo olemassa.';
    }

}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>


<html>
 <head>
  <title>Seppo Tärsky</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 </head>
 <body>

    <!-- Lomake lähetetään samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="r2.php" method="post">

    <h2>Muodosta lasku</h2>

<p>
    <a href='tslista.php'>Lista työsuorituksista</a>
</p>
    
    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>

<p>
	<!—PHP-ohjelmassa viitataan kenttien nimiin (name) -->
	<table border="0" cellspacing="0" cellpadding="3">
       <tr>
          <td>Työsuoritus</td>
          <td><input type="text" name="suoritus" value="" /></td>
       </tr>
	</table>
</p>

	<!-- hidden-kenttää käytetään varotoimena, esim. IE ei välttämättä
	 lähetä submit-tyyppisen kentän arvoja jos lomake lähetetään
	 enterin painalluksella. Tätä arvoa tarkkailemalla voidaan
   skriptissä helposti päätellä, saavutaanko lomakkeelta. -->
<p>  
   <a href="index.html"><button type="button">Takaisin</button></a>

	<input type="hidden" name="tallenna" value="jep" />
  <input type="submit" value="Tallenna" />
</p>
	</form>

</body>
</html>