<?php
$changeDensity = $_REQUEST["density"];
$ofCity = $_REQUEST["name"];

header('Content-type: application/json');
$strJsonFileContents = file_get_contents("/geojson/neighborhood.geojson");
$arr = json_decode($strJsonFileContents,true);

for($i = 0 ; $i < count($arr["features"]); $i++){
if ( strcmp($ofCity,$arr["features"]["".$i.""]["properties"]["Name"]) == 0) {
   $arr["features"]["".$i.""]["properties"]["density"] = $changeDensity;
   echo json_encode($arr["features"]["".$i.""]["properties"]["Name"]);
   break;
}
}
$newJsonString = json_encode($arr);
file_put_contents('neighborhood.geojson',$newJsonString);
?>
