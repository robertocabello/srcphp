<?php include('includes/config.php'); ?>

<style type="text/css">
body{font-size: 12px;font-family: 'Calibri', sans-serif;}
</style>

<table>
<?php 
	$sql = "SELECT nombre, rut, domicilio, adulto_responsable, DATE_FORMAT(fecha_nacimiento, '%e %m %Y') as fecha FROM ".$prefix."junta WHERE tipo = 'nina' ORDER BY nombre ASC";
	$result = $conexion->query($sql);
	$existe_registro = $result->num_rows;
	while($data_sql = $result->fetch_array(MYSQLI_BOTH)){ ?>
		<tr>
			<td><?php echo $data_sql['nombre']; ?></td>
			<td><?php echo $data_sql['rut']; ?></td>
			<td><?php echo $data_sql['domicilio']; ?></td>
			<td><?php echo $data_sql['adulto_responsable']; ?></td>
			<td><?php echo $data_sql['fecha']; ?></td>
		</tr>
	<?php  }
?>
</table>