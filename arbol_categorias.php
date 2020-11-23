<?php include('includes/config.php');

echo '<ul>';

$sql = "SELECT * FROM ".$prefix."categorias WHERE id <> '' AND estado = 'activo' ORDER BY codigo ASC ";
$result = $conexion->query($sql);
while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
	echo '<li>';
	echo $data_sql['codigo'].' '.utf8_encode($data_sql['categoria']);
	echo '<ul>';
		$sql2 = "SELECT * FROM ".$prefix."subcategorias WHERE id <> '' AND categoria = '".$data_sql['id']."' ORDER BY codigo ASC ";
		$result2 = $conexion->query($sql2);
		while($data_sql2 = $result2->fetch_array(MYSQLI_BOTH)){
			echo '<li>'.$data_sql2['codigo'].' '.utf8_encode($data_sql2['subcategoria']).'</li>';
		}
	echo '</ul>';
	echo '</li>';
}

echo '</ul>';
?>