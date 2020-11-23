<?php include('includes/config.php');

echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
$sql = "SELECT p.id as id_interno, p.nombre, p.razon_social, p.cuenta_proveedor, p.estado as estado_cuenta, g.giro, g.estado as estado_giro FROM seo_proveedor as p, seo_giros as g WHERE p.giro = g.codigo ORDER BY p.id DESC";
$result = $conexion->query($sql);
while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
	echo '<tr>';
	echo '<td>'.$data_sql['id_interno'].'</td>';
	if($data_sql['cuenta_proveedor'] == 'SI'){
		echo '<td>'.'PROVEEDOR'.'</td>';
	}else{
		echo '<td>'.'CLIENTE'.'</td>';
	}
	echo '<td>'.$data_sql['razon_social'].'</td>';
	echo '<td>'.$data_sql['estado_cuenta'].'</td>';
	echo '<td>'.$data_sql['giro'].'</td>';
	echo '<td>'.$data_sql['estado_giro'].'</td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>