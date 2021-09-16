<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
  function borrarUsuario(username=""){
    if (confirm('¿Esta seguro que quiere borrar el usuario '+ username + '?')) {
        location.href = "<?php echo base_url(); ?>index.php/usuario/borrarUsuario?username=" + username;
    } 
  }
</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url(); ?>index.php/usuario/index" method="post" id="formaBusqueda">
    <div class="row">
      <div class="col-md-6 form-group">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" class="form-control" value="<?php echo set_value("username"); ?>">
        <small class="text-danger"><?php echo form_error("username");?></small>
      </div>
      <div class="col-md-6 form-group">
        <label for="email">Email</label>
        <input type="text" name="email" class="form-control" value="<?php echo set_value("email"); ?>">
        <small class="text-danger"><?php echo form_error("email");?></small>
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

  <?php if (tienePermiso("Usuario","altas",$lista_permisos)): ?>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 form-group">
      <button class="form-control btn btn-success" onclick="location.href='<?php echo base_url(); ?>index.php/usuario/agregarUsuario'">Agregar usuario</button>
    </div>
    <div class="col-md-4"></div>
  </div>
  <?php endif ?>

  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th>Nombre de usuario</th>
              <th>Correo electrónico</th>
              <th>Usuario que modificó</th>
              <th>Fecha de modificación</th>
              <?php if (tienePermiso("Permisos","consultas",$lista_permisos) ||
                        tienePermiso("Usuario","cambios",$lista_permisos) ||
                        tienePermiso("Usuario","bajas",$lista_permisos)): ?>
              <th colspan="3">Acciones</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
          <?php
            if(isset($usuarios)){
                foreach ($usuarios as $usuario) {
                    echo "
                        <tr>
                          <th>$usuario->username</th>
                          <td>$usuario->email</td>
                          <td>$usuario->usernameModificacion</td>
                          <td>$usuario->fechaModificacion</td>";
                          if (tienePermiso("Permisos","consultas",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-dark form-control' onclick=\"location.href='".base_url()."index.php/permisos/muestraPermisos?nombreUsuario={$usuario->username}'\">Permisos</button>
                            </td>";
                          if (tienePermiso("Usuario","cambios",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/usuario/editarUsuario?username={$usuario->username}'\">Editar</button>
                            </td>";
                          if (tienePermiso("Usuario","bajas",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-dark form-control' onclick=\"borrarUsuario('{$usuario->username}')\">Borrar</button>
                            </td>";
                    echo "
                        </tr>";
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