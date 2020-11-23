<form method="post" action="enviar_test.php">
	<div>
		<label>Nombre</label>
		<input type="text" name="nombre" autocomplete="off">
	</div>
	<div>
		<label>Rut</label>
		<input type="text" name="rut" autocomplete="off">
	</div>
	<div>
		<label>Domicilio</label>
		<input type="text" name="domicilio" autocomplete="off">
	</div>
	<div>
		<label>Adulto Responsable</label>
		<input type="text" name="adulto_responsable" autocomplete="off">
	</div>
	<div>
		<label>Fecha Nacimiento</label>
		<input type="date" name="fecha_nacimiento">
	</div>
	<div>
		<label>Fecha Nacimiento</label>
		<select name="tipo">
			<option value="nina" selected>Niña</option>
			<option value="nino">Niño</option>
		</select>
	</div>
	<div>
		<input type="submit" value="Enviar">
	</div>
</form>