<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>



	function actualizar(username){
		var estatus = document.getElementById(username).checked;

		var datos = username.split("-");

		var accion = datos[0];
		var user = datos[1];
		var modulo = datos[2];
		//enviaAlerta ("titulo",estatus + " " + accion + " " + user + " " + modulo);
		

		$.ajax({
		  url:  '<?php echo base_url(); ?>index.php/permisos/cambiarPermiso/'+ estatus +'/'+ user + '/' + modulo + '/' + accion,
		  async: true,
		  cache: false,
		  type: 'POST',
		  data: {},
		  dataType: 'json',
		  success: function(datos) {
			enviaAlerta(datos.titulo,datos.mensaje);

			if(datos.resultado == "false"){
				if(estatus){
					$( "#"+username ).prop( "checked", false );
				}else{
					$( "#"+username ).prop( "checked", true );
				}
			}

		  },
		  error: function(xhr, status) {
		  	enviaAlerta("error conexión", "Ha ocurrido un error en el sistema");
			
		  }
		});
	

	}

</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>

  <form action="<?php echo base_url(); ?>index.php/permisos/muestraPermisos" method="get" id="formaBusqueda">
    <div class="row">
      <div class="col-md-12 form-group">
        <label for="nombreUsuario">Nombre de usuario</label>
        <input type="text" name="nombreUsuario" class="form-control" value="<?php echo set_value("nombreUsuario"); ?>">
        <small class="text-danger"><?php echo form_error("nombreUsuario");?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2 form-group"></div>
      <div class="col-md-4 form-group">
        <input type="submit" class="form-control btn btn-primary" value="Buscar">
      </div>
      <div class="col-md-4 form-group">
        <input type="button" class="btn btn-danger btn-block" value="Reiniciar forma" id="reiniciar" name="reiniciar" onclick="borrarForma()">
      </div>
      <div class="col-md-2 form-group"></div>
    </div>
  </form>


  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              	<th>Nombre de usuario</th>
				<th>Módulo</th>
			  	<th>Altas</th>
				<th>Bajas</th>
				<th>Consultas</th>
			  	<th>Cambios</th>
			  	<th>Usuario que modificó</th>
              	<th>Fecha de modificación</th>
            </tr>
          </thead>
          <tbody>
          <?php
            if(isset($registros)){
                foreach ($registros as $registro){
                    $altas = ($registro->altas == 1)?"checked":"";
					$bajas = ($registro->bajas == 1)?"checked":"";
					$consultas = ($registro->consultas == 1)?"checked":"";
					$cambios = ($registro->cambios == 1)?"checked":"";

					echo "	
                             <tr class='info'>
                                <td>{$registro->username}</td> 
                                <td>{$registro->modulo}</td>
								<td><div class='form-group'><input type='checkbox' onclick='actualizar(this.id)' id='altas-{$registro->username}-{$registro->modulo}' $altas></div></td>
								<td><div class='form-group'><input type='checkbox' onclick='actualizar(this.id)' id='bajas-{$registro->username}-{$registro->modulo}' $bajas></div></td>
								<td><div class='form-group'><input type='checkbox' onclick='actualizar(this.id)' id='consultas-{$registro->username}-{$registro->modulo}' $consultas></div></td>
								<td><div class='form-group'><input type='checkbox' onclick='actualizar(this.id)' id='cambios-{$registro->username}-{$registro->modulo}' $cambios></div></td>
								<td>{$registro->usernameModificacion}</td>
                          		<td>{$registro->fechaModificacion}</td>
							<tr>";
                }
            }

          ?>
          </tbody>
        </table>
      </div>
      <!-- table-responsive -->
    </div>
    <!-- col-md-12 -->
  </div>
  <!-- /.row -->

  <!-- Pagination -->
  <?php if(isset($paginacion)) echo $paginacion; ?>

</div>
<!-- /.container -->


