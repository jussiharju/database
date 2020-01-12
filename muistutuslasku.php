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

      $kysely = "SELECT ltunnus, osa, alv, summa, kotitalousvah, tstunnus FROM lasku WHERE tila = 'valmis' AND erapvm < current_date AND numero = 1;";
      $kyselyntulos = pg_query($kysely);
      
      while ($rivi = pg_fetch_row($kyselyntulos)) {
        $idkysely = "SELECT MAX(ltunnus) FROM lasku;";
        $idtulos = pg_query($idkysely);
        $idrivi = pg_fetch_row($idtulos);
        $id = $idrivi[0] + 1;

        $syota = pg_query("INSERT INTO lasku VALUES ($id, 2, $rivi[1], $rivi[2], 'valmis', $rivi[3]+5, current_date + 14, current_date, NULL, $rivi[4], $rivi[5], $rivi[0]);");

      }

        // asetetaan viesti-muuttuja lis��misen onnistumisen mukaan
	// lis�t��n virheilmoitukseen my�s virheen syy (pg_last_error)

        if ($syota && (pg_affected_rows($syota) > 0)) {
           $viesti = 'Muistutuslasku luotu!';
        }
        else {
           $viesti = 'Muistutuslaskua ei luotu. Ei vanhentuneita laskuja.';
        }
}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>

<html>
 <head>
  <title>Muistutuslasku</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 </head>
 <body>

    <!-- Lomake l�hetet��n samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="muistutuslasku.php" method="post">


    <h2>Muistutuslaskun luominen</h2>

    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>

    

	
	<!-- hidden-kentt�� k�ytet��n varotoimena, esim. IE ei v�ltt�m�tt�
	 l�het� submit-tyyppisen kent�n arvoja jos lomake l�hetet��n
	 enterin painalluksella. T�t� arvoa tarkkailemalla voidaan
	 skriptiss� helposti p��tell�, saavutaanko lomakkeelta. -->
<p>  
  <a href="index.html"><button type="button">Takaisin</button></a>

	<input type="hidden" name="tallenna" value="jep" />
  <input type="submit" value="Luo muistutuslasku" />
</p>
	</form>

</body>
</html>