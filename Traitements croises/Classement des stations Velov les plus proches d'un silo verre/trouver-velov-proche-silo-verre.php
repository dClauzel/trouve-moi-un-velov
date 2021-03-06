<?php
require_once '../../config.php';
require_once '../../Ressources/fonctionsGénériques.php';
?>
<!doctype html>
<html>
<head>
	<title>Trouver les stations vélo'v les plus proches d'un silo verre</title>
	<meta charset='utf-8'>
	<meta name='viewport' content='initial-scale=1.0, user-scalable=no'>
	<script type='text/javascript' src='//maps.googleapis.com/maps/api/js?key=<?php echo $Google; ?>&sensor=true'></script>
	<link rel=stylesheet href='../../Ressources/style général.css'>
</head>
<body>

<?php 

$dbconn = pg_connect("host=$BD_host dbname=$BD_base user=$BD_user password=$BD_passwd")
	or die('Impossible de se connecter à la base : ' . pg_last_error());

$query = "SELECT
	velovStations.number,
	velovStations.name,
	velovStations.address,
	ST_Y(ST_Transform(velovStations.geom,4326)),
	ST_X(ST_Transform(velovStations.geom,4326)),
	GLSilosVerre.voie,
	GLSilosVerre.numerodansvoie,
	ST_Distance( ST_Transform(velovStations.geom, 4326), ST_Transform(GLSilosVerre.geom, 4326 ) )

FROM
	VelovStations
JOIN
	GLsilosVerre
ON
	-- on restreint la recherche aux silos verre dans un rayon de 500 mètres autour de chaque station vélov 
	ST_Dwithin(ST_Transform(velovStations.geom, 4326), ST_Transform(GLSilosVerre.geom, 4326 ), 500)

ORDER BY ST_Distance
LIMIT 10 ;
";

$resultat = pg_query($dbconn, $query);

?>

<table>
<caption><?php echo "Les ".pg_num_rows($resultat)." stations vélo'v les plus proches d'un silo verre"; ?></caption>
<tr>
	<th>numéro station Vélo'v</th>
	<th>nom station Vélo'v</th>
	<th>adresse station Vélo'v</th>
	<th>latitude, longitude station Vélo'v</th>
	<th>distance entre la station Vélo'v et le silo verre</th>
	<th>voie du silo verre</th>
	<th>numéro dans voie du silo verre</th>
</tr>

<?php
while ($ligne = pg_fetch_array($resultat)) {
	echo "<tr>";
	echo "<td>" .securise($ligne['number']). "</td>";
	echo "<td>" .securise($ligne['name']). "</td>";
	echo "<td>" .securise($ligne['address']). "</td>";
	echo "<td>" .$ligne['st_y']. ", " .$ligne['st_x']. "</td>";
	echo "<td>" .$ligne['st_distance']. "</td>";
	echo "<td>" .securise($ligne['voie']). "</td>";
	echo "<td>" .securise($ligne['numerodansvoie']). "</td>";
	echo "</tr>\n";
}
?>

</table>

	<div id='map-canvas'></div>

<script type='text/javascript'>
function initialize() {

// init de la carte centrée sur ma position
var mapOptions = {
	center: new google.maps.LatLng(<?php echo "$latitude, $longitude"; ?>),
	zoom: 12,
	mapTypeId: google.maps.MapTypeId.HYBRID
}

var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
<?php
pg_result_seek($resultat, 0);
while ($ligne = pg_fetch_array($resultat)) {
	echo "var marker = new google.maps.Marker({
		position: new google.maps.LatLng(" .$ligne['st_y']. "," .$ligne['st_x']. "),
		map: map,
		title:'" .securise($ligne['name']). "'
		});\n";
}
?>
// Marqueur bleu pour dire où on est
blueIcon = '//www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png';

var marker = new google.maps.Marker({
	position: new google.maps.LatLng(<?php echo "$latitude, $longitude"; ?>),
	map: map,
	icon: blueIcon,
	title: 'Je suis ici'
});
} // fin initialize()
google.maps.event.addDomListener(window, 'load', initialize);
</script>

<hr>
<h1>Technique</h1>
<p>Les résultats ont été trouvés par la requête suivante :
<pre><code><?php echo securise($query); ?></code></pre>
<p><small><a href='http://dclauzel.github.io/Trouve-moi-un-truc-a-Lyon/'>Sources sur GitHub</a> — par <a href='http://Damien.Clauzel.eu'>Damien Clauzel</a> — <a href='https://Twitter.com/dClauzel'>@dClauzel</a> — sous licence GPLv3</small>
</body>
</html>
