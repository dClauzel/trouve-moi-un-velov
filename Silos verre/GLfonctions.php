<?php

require_once '../fonctions.php'; // fonctions génériques

/* fonction de comparaison de 2 stations, pour usort()
 * la comparaison se fait par rapport à la distance de l'utilisateur
 */
function GLCompareDistanceSiloVerre($point1, $point2) {

	global $latitude, $longitude;
	$distance1 = distance($latitude, $longitude, $point1["geometry"]['coordinates'][0], $point1["geometry"]['coordinates'][1]);
	$distance2 = distance($latitude, $longitude, $point2["geometry"]['coordinates'][0], $point2["geometry"]['coordinates'][1]);

	if ($distance1 == $distance2)
		return 0;

	return ($distance1 < $distance2) ? -1 : 1;
}

// affiche en liste les données sur un silo verre
function GLAfficheSiloVerre($station) {
	global $latitude, $longitude;
	$res = "<ul>";
	$res .= "<li>type : " . $station["type"];
	$res .= "<li>gml_id : " . $station["properties"]["gml_id"];
	$res .= "<li>commune : " . $station["properties"]["commune"];
	$res .= "<li>voie : " . $station["properties"]["voie"];
	$res .= "<li>numerodansvoie : " . $station["properties"]["numerodansvoie"];
	$res .= "<li>gestionnaire : " . $station["properties"]["gestionnaire"];
	$res .= "<li>observation : " . $station["properties"]["observation"];
	$res .= "<li>miseajourattributs : " . $station["properties"]["miseajourattributs"];
	$res .= "<li>miseajourgeometrie : " . $station["properties"]["miseajourgeometrie"];
	$res .= "<li>gid : " . $station["properties"]["gid"];
	$res .= "<li>latitude : " . $station["geometry"]["coordinates"][0];
	$res .= "<li>longitude : " . $station["geometry"]["coordinates"][1];
	$res .= "<ul>";
	$res .= "\t<li>distance : " . distance($latitude,$longitude,$station["geometry"]["coordinates"][0],$station["geometry"]["coordinates"][1]);
	$res .= "</ul>";
	$res .= "</ul>";

	return $res;
}

?>
