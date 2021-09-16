<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
  function borrarLugar(id=""){
    if (confirm('¿Esta seguro que quiere borrar el lugar con id '+ id + '?')) {
      location.href = "<?php echo base_url(); ?>index.php/lugar/borrarLugar?id=" + id;
    } 
  }
</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url(); ?>index.php/lugar/index" method="post" id="formaBusqueda">
    <div class="row">
      <div class="col-md-3 form-group">
        <label for="nombre">Nombre del lugar</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo set_value("nombre"); ?>">
        <small class="text-danger"><?php echo form_error("nombre");?></small>
      </div>

      <div class="col-md-3 form-group">
        <label for="latitud">Latitud</label>
        <input type="text" name="latitud" class="form-control" value="<?php echo set_value("latitud"); ?>">
        <small class="text-danger"><?php echo form_error("latitud");?></small>
      </div>

      <div class="col-md-3 form-group">
        <label for="longitud">Longitud</label>
        <input type="text" name="longitud" class="form-control" value="<?php echo set_value("longitud"); ?>">
        <small class="text-danger"><?php echo form_error("longitud");?></small>
      </div>

      <div class="col-md-3 form-group">
        <label for="radio">Radio</label>
        <input type="text" name="radio" class="form-control" value="<?php echo set_value("radio"); ?>">
        <small class="text-danger"><?php echo form_error("radio");?></small>
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

  <?php if (tienePermiso("Lugar","altas",$lista_permisos)): ?>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4 form-group">
        <button class="form-control btn btn-success" onclick="location.href='<?php echo base_url(); ?>index.php/lugar/agregarLugar'">Agregar lugar</button>
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
              <th>Id</th>
              <th>Nombre</th>
              <th>Latitud</th>
              <th>Longitud</th>
              <?php if (tienePermiso("Lugar","cambios",$lista_permisos) ||
              tienePermiso("Lugar","bajas",$lista_permisos)): ?>
              <th colspan="2">Acciones</th>
            <?php endif ?>
          </tr>
        </thead>
        <tbody>
          <?php
          if(isset($lugares)){
            foreach ($lugares as $lugar) {
              echo "
              <tr>
              <th>$lugar->id</th>
              <td>$lugar->nombre</td>
              <td>$lugar->latitud</td>
              <td>$lugar->longitud</td>";

              if (tienePermiso("Lugar","cambios",$lista_permisos))
                echo "
              <td>
              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/lugar/editarLugar?id={$lugar->id}'\" >Editar</button>
              </td>";

              if (tienePermiso("Lugar","bajas",$lista_permisos))
                echo "
              <td>
              <button class='btn btn-dark form-control' onclick=\"borrarLugar('{$lugar->id}')\">Borrar</button>
              </td>";
              if (tienePermiso("Temperatura","consultas",$lista_permisos))
                echo "
              <td>
              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/temperatura/mostrarTemperaturas?lugar_id={$lugar->id}'\" >Temperaturas</button>
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