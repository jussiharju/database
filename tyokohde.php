<?php

// luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheest�

$y_tiedot = "host=dbstud2.sis.uta.fi port=5432 dbname=tiko2019r8 user=jh424450 password=salasana";

if (!$yhteys = pg_connect($y_tiedot))
   die("Tietokantayhteyden luominen epäonnistui.");



// isset funktiolla j��d��n odottamaan sy�tett�.
// POST on tapa tuoda tietoa lomaketta (tavallaan kutsutaan lomaketta).
// Argumentti tallenna saadaan lomakkeen napin nimest�.

if (isset($_POST['tallenna']))
{
    // suojataan merkkijonot ennen kyselyn suorittamista
    // suojataan merkkijonot ennen kyselyn suorittamista

    $atunnus   = intval($_POST['atunnus']);
    $tknimi   = pg_escape_string($_POST['tknimi']);
    $tkosoite   = pg_escape_string($_POST['tkosoite']);

    // jos kenttiin on sy�tetty jotain, lis�t��n tiedot kantaan

    $tiedot_ok = trim($tknimi) != '' && trim($tkosoite) != '';

    if ($tiedot_ok)
    {
        $idkysely = "SELECT MAX(tktunnus) FROM tyokohde;";
        $idtulos = pg_query($idkysely);
        $maksimi = pg_fetch_row($idtulos);
        if($maksimi[0] == 0) {
          $id = 101;
        } else {
          $id = $maksimi[0] + 1;
        }
        $kysely = "INSERT INTO tyokohde VALUES ($id, '$tknimi', '$tkosoite', '$atunnus');";
        $lisays = pg_query($kysely);

        // asetetaan viesti-muuttuja lis��misen onnistumisen mukaan
	// lis�t��n virheilmoitukseen my�s virheen syy (pg_last_error)

        if ($lisays && (pg_affected_rows($lisays) > 0))
            $viesti = 'Työkohde lisätty!';
        else
            $viesti = 'Työkohdetta ei lisätty: ' . pg_last_error($yhteys);
    }
    else
        $viesti = 'Annetut tiedot puutteelliset - tarkista, ole hyvä!';

}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>

<html>
 <head>
  <title>Työkohde</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 </head>
 <body>

    <!-- Lomake l�hetet��n samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="tyokohde.php" method="post">


    <h2>Työkohteen lisäys</h2>

<p>
    <a href='alista.php'>Lista asiakkaista</a>
</p>

    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>

<p>
	<!-- PHP-ohjelmassa viitataan kenttien nimiin (name) -->
	<table border="0" cellspacing="0" cellpadding="3">
      <tr>
    	    <td>Asiakkaan tunnus</td>
    	    <td><input type="text" name="atunnus" value="" /></td>
	    </tr>
      <tr>
    	    <td>Työkohteen nimi</td>
    	    <td><input type="text" name="tknimi" value="" /></td>
	    </tr>
	    <tr>
    	    <td>Työkohteen osoite</td>
    	    <td><input type="text" name="tkosoite" value="" /></td>
	    </tr>
  </table>
</p>

	<!-- hidden-kentt�� k�ytet��n varotoimena, esim. IE ei v�ltt�m�tt�
	 l�het� submit-tyyppisen kent�n arvoja jos lomake l�hetet��n
	 enterin painalluksella. T�t� arvoa tarkkailemalla voidaan
	 skriptiss� helposti p��tell�, saavutaanko lomakkeelta. -->
<p>
  <a href="index.html"><button type="button">Takaisin</button></a>

	<input type="hidden" name="tallenna" value="jep" />
  <input type="submit" value="Lisää työkohde" />
</p>
  </form>


</body>
</html>