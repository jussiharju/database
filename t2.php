<?php

// luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheestä

$y_tiedot = "host=dbstud2.sis.uta.fi port=5432 dbname=tiko2019r8 user=jh424450 password=salasana";

if (!$yhteys = pg_connect($y_tiedot))
   die("Tietokantayhteyden luominen epäonnistui.");



// isset funktiolla jäädään odottamaan syötettä.
// POST on tapa tuoda tietoa lomaketta (tavallaan kutsutaan lomaketta).
// Argumentti tallenna saadaan lomakkeen napin nimestä.

if (isset($_POST['tallenna'])) {
   
    // suojataan merkkijonot ennen kyselyn suorittamista
    // suojataan merkkijonot ennen kyselyn suorittamista

    $tk = intval($_POST['kohde']);
    $ts = intval($_POST['suoritus']);
    $tyo = intval($_POST['tyo']);
    $tyoale = floatval($_POST['tyoale']);
    $aputyo = intval($_POST['aputyo']);
    $aputyoale = floatval($_POST['aputyoale']);
    $suunnittelu = intval($_POST['suunnittelu']);
    $suuntyoale = floatval($_POST['suuntyoale']);
    $tarvikkeet = pg_escape_string($_POST['tarvikkeet']);
    $maarat = pg_escape_string($_POST['maarat']);

    $maarat_lista = explode(",", $maarat);
    $tarv_lista = explode(",", $tarvikkeet);
    
    $flag = TRUE;
    
    $tst = pg_query("SELECT tstunnus FROM tyosuoritus;");
    while ($rivi = pg_fetch_row($tst) && $flag) {
       if ($rivi[0] == $ts) {
          $flag = FALSE;
       }
    } 
    
    if ($flag) {
       $lisaats = pg_query("INSERT INTO tyosuoritus VALUES($ts, $tk, 'sopimus');");
    }
    
    if (count($maarat_lista) == count($tarv_lista) && $flag) {
       
       $n = count($maarat_lista);
       
       if ($tyo > 0) {
          $lis_tyo = "INSERT INTO tyolista VALUES ($tyo, $tyoale, $ts, 'tyo')";
          $lis_tyo_sql = pg_query($lis_tyo);
       }
       if ($aputyo > 0) {
          $lis_aputyo = "INSERT INTO tyolista VALUES ($aputyo, $aputyoale, $ts, 'aputyo')";
          $lis_aputyo_sql = pg_query($lis_aputyo);
       }
       if ($suunnittelu > 0) {
          $lis_suunnittelu = "INSERT INTO tyolista VALUES ($suunnittelu, $suuntyoale, $ts, 'suunnittelu')";
          $lis_suun_sql = pg_query($lis_suunnittelu);
       }
       for ($i = 0; $i < $n; $i++) {
          $lis_tarv = "INSERT INTO tarvikelista VALUES ($ts, $tarv_lista[$i], $maarat_lista[$i], 1)";
          $lis_tarv_sql = pg_query($lis_tarv);
       }
    }
    else {
       $viesti = 'Annetut tiedot virheelliset!';
    }
    
}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>

<html>
 <head>
  <title>Tapahtuma 2</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 </head>
 <body>

    <!-- Lomake lähetetään samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="t2.php" method="post">

    <h2>Päivän töiden/tarvikkeiden kirjaus</h2>

    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>

<p>
    <a href='tklista.php'>Lista työkohteista</a>
</p>
<p>
    <a href='tslista.php'>Lista työsuorituksista</a>
</p>
<p>
    <a href='tlista.php'>Lista tarvikkeista</a>
</p>

<p>
	<!-- PHP-ohjelmassa viitataan kenttien nimiin (name) -->
	<table border="0" cellspacing="0" cellpadding="3">
       <tr>
          <td>Työkohteen tunnus</td>
          <td><input type="text" name="kohde" value="" /></td>
       </tr>
       <tr>
          <td>Työsuorituksen tunnus</td>
          <td><input type="text" name="suoritus" value="" /></td>
       </tr>
	    <tr>
    	    <td>Työtunnit</td>
    	    <td><input type="text" name="tyo" value="" /></td>
          <td>Alennus</td>
          <td><input type="text" name="tyoale" value="" /></td>
	    </tr>
	    <tr>
    	    <td>Aputyötunnit</td>
    	    <td><input type="text" name="aputyo" value="" /></td>
          <td>Alennus</td>
          <td><input type="text" name="aputyoale" value="" /></td>
	    </tr>
	    <tr>
    	    <td>Suunnittelutunnit</td>
    	    <td><input type="text" name="suunnittelu" value="" /></td>
          <td>Alennus</td>
          <td><input type="text" name="suuntyoale" value="" /></td>
	    </tr>
       <tr>
          <td>Tarvikkeet</td>
          <td><input type="text" name="tarvikkeet" "value" /></td>
          <td>Määrät</td>
          <td><input type="text" name="maarat" "value" /></td>
       </tr>
  </table>
</p>
<p>
  Ilmoita tarvikkeet ja määrät pilkulla eroteltuna ilman välilyöntiä (desimaalimerkki on piste (.)) <br />
  Alennus työtunneista ilmoitetaan 1-0.(alennusprosentti), esim 20% alennus on 0.8 (= 1-0.20) <br />
  Jos ei alennusta, kirjoita kenttään 1.
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