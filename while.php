<?php include('includes/config.php');

$lista = array(
	"1049",
	"1080",
	"1057",
	"1054",
	"1037",
	"1047",
	"1067",
	"1066",
	"1053",
	"1045",
	"1038",
	"1046",
	"1074",
	"1077",
	"1072",
	"1064",
	"1044",
	"1062",
	"1075",
	"1070",
	"1041",
	"1048",
	"1050",
	"1069",
	"596",
	"1031",
	"1030",
	"1224",
	"1269",
	"982",
	"1082",
	"975",
	"1187",
	"1182",
	"1168",
	"1169",
	"1174",
	"1171",
	"1230",
	"974",
	"1255",
	"976",
	"1231",
	"1160",
	"977",
	"1223",
	"1226",
	"1130",
	"1128",
	"1154",
	"1108",
	"1099",
	"1186",
	"1129",
	"1141",
	"1144"
);

$tamanio = count($lista);
for ($x = 0; $x < $tamanio; $x++){
	//echo $lista[$x];

	$sql = "SELECT * FROM ".$prefix."producto WHERE id = '".$lista[$x]."' LIMIT 1";
	$result = $conexion->query($sql);
	$data_sql = $result->fetch_array(MYSQLI_BOTH);

	echo '[ID:'.$lista[$x].' | Producto:'.$data_sql['producto'].' | Respuesta WS:'.$data_sql['wsdl_respuesta'].' | Última fecha ejecución método fnProducto:'.$data_sql['wsdl_fecha'].']<br>';

}
?>