

<html>
 <head>
  <title>Seppo Tärsky</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 </head>
 <body>

    <!-- Lomake lähetetään samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="r1.php" method="post">

    <h2>Hinta-arvio</h2>

    <p>
      <a href='tklista.php'>Lista työkohteista</a>
    </p>

    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>

  <!—PHP-ohjelmassa viitataan kenttien nimiin (name) -->
<p>
   <table border="0" cellspacing="0" cellpadding="3">
       <tr>
          <td>Hintaan sisältyy: suunnittelua 3 tuntia, asennustyötä 12 tuntia, 3 metriä sähköjohtoa sekä yksi pistorasia.</td>
       </tr>
  </table>
</p>
<p>
	<table border="0" cellspacing="0" cellpadding="3">
       <tr>
          <td>Työkohde</td>
          <td><input type="text" name="kohde" value="" /></td>
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

    $suunh = 3;
    $tyoh = 12;
    $johtom = 3;
    $prkpl = 1;
    $tk = intval($_POST['kohde']);
    
    if (pg_fetch_row(pg_query("SELECT tktunnus FROM tyokohde WHERE tktunnus = $tk"))) {
       
       $tyohinta = pg_fetch_row(pg_query("SELECT hinta FROM tyohinnasto WHERE tyyppi = 'tyo'"));
       $atyohinta = pg_fetch_row(pg_query("SELECT hinta FROM tyohinnasto WHERE tyyppi = 'aputyo'"));
       $suuntyohinta = pg_fetch_row(pg_query("SELECT hinta FROM tyohinnasto WHERE tyyppi = 'suunnittelu'"));
       
       // Pistorasian tunnus on 10005
       $prhinta = pg_fetch_row(pg_query("SELECT myyntihinta FROM tarvike WHERE ttunnus = 10005"));
       // Sähköjohdon tunnus on 10009
       $johtohinta = pg_fetch_row(pg_query("SELECT myyntihinta FROM tarvike WHERE ttunnus = 10009"));
       
       $summa = $suunh * $suuntyohinta[0] + $tyoh * $tyohinta[0] + $johtom * $johtohinta[0] + $prkpl * $prhinta[0];
       echo 'Hinta-arvio: ';
       echo $summa;
       echo '€.';
    }
    else {
       $viesti = 'Työkohde ei ole tietokannassa';
    }

}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>

</body>
</html>