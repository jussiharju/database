<html>
 <head>
  <title>Lista asiakkaista</title>
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
      $y_tiedot = "host=dbstud2.sis.uta.fi port=5432 dbname=tiko2019r8 user=jh424450 password=salasana";

      if (!$yhteys = pg_connect($y_tiedot))
         die("Tietokantayhteyden luominen epäonnistui.");

      $tulos = pg_query("SELECT atunnus, animi FROM asiakas;");
      if (!$tulos) {
        echo "Virhe kyselyss�.\n";
        exit;
      }

      echo "<table>";
      echo "<tr><th>Tunnus</th><th>Nimi</th></tr>";

      while ($rivi = pg_fetch_row($tulos)) {
        echo "<tr><td> $rivi[0] </td><td>$rivi[1]</td></tr>";
      //  echo "<br />\n";
      }
      
      echo "</table>";
      pg_close($yhteys);
  ?>

  </p>
<form>
  <input type="button" value="Takaisin" onclick="history.back()">
</form>

</body>
</html>