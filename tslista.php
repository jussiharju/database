
<html>
 <head>
  <title>Työsuoritukset</title>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <style>
    table, th, td {
      border: 1px solid black
    }
  </style>
 </head>
 <body>

 <p>
<?php

// luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheestä

$y_tiedot = "host=dbstud2.sis.uta.fi port=5432 dbname=tiko2019r8 user=jh424450 password=salasana";

if (!$yhteys = pg_connect($y_tiedot))
   die("Tietokantayhteyden luominen epäonnistui.");

$tulos1 = pg_query("SELECT tstunnus, tktunnus, laatu FROM tyosuoritus;");
if (!$tulos1) {
  echo "Virhe kyselyssä.\n";
  exit;
}

echo "<table>";
echo "<tr><th>Tunnus</th><th>Työkohde</th><th>Tiedot</th></tr>";

while ($rivi1 = pg_fetch_row($tulos1)) {
  echo "<tr><td> $rivi1[0] </td><td>$rivi1[1]</td><td>$rivi1[2]</td></tr>";
}

echo "</table>";
// suljetaan tietokantayhteys

pg_close($yhteys);

?>
</p>
<form>
  <input type="button" value="Takaisin" onclick="history.back()">
</form>


</body>
</html>