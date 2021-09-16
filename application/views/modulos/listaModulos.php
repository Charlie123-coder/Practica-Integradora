<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
  function borrarModulo(modulo=""){
    if (confirm('¿Esta seguro que quiere borrar el modulo '+ modulo + '?')) {
        location.href = "<?php echo base_url(); ?>index.php/modulo/borrarModulo?modulo=" + modulo;
    } 
  }
</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url(); ?>index.php/modulo/index" method="post" id="formaBusqueda">
    <div class="row">
      <div class="col-md-6 form-group">
        <label for="modulo">Módulo</label>
        <input type="text" name="modulo" class="form-control" value="<?php echo set_value("modulo"); ?>">
        <small class="text-danger"><?php echo form_error("modulo");?></small>
      </div>
      <div class="col-md-6 form-group">
        <label for="habilitado">Habilitado</label>
        <select class="form-control" name="habilitado">
          <option value="">Seleccione un valor</option>
          <option value="0" <?php echo  set_select('habilitado', '0'); ?>>Deshabilitado</option>
          <option value="1" <?php echo  set_select('habilitado', '1'); ?>>Habilitado</option>
        </select>
        <small class="text-danger"><?php echo form_error("habilitado");?></small>
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

  <?php if (tienePermiso("Modulo","altas",$lista_permisos)): ?>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 form-group">
      <button class="form-control btn btn-success" onclick="location.href='<?php echo base_url(); ?>index.php/modulo/agregarModulo'">Agregar módulo</button>
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
              <th>Módulo</th>
              <th>Habilitado</th>
              <th>Usuario que modificó</th>
              <th>Fecha de modificación</th>
              <?php if (tienePermiso("Modulo","cambios",$lista_permisos) ||
                        tienePermiso("Modulo","bajas",$lista_permisos)): ?>
              <th colspan="2">Acciones</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
          <?php
            if(isset($modulos)){
                foreach ($modulos as $modulo) {
                    echo "
                        <tr>
                          <th>$modulo->modulo</th>
                          <td>$modulo->habilitado</td>
                          <td>$modulo->usernameModificacion</td>
                          <td>$modulo->fechaModificacion</td>";

                          if (tienePermiso("Modulo","cambios",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/modulo/editarModulo?modulo={$modulo->modulo}'\" >Editar</button>
                            </td>";

                          if (tienePermiso("Modulo","bajas",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-dark form-control' onclick=\"borrarModulo('{$modulo->modulo}')\">Borrar</button>
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





