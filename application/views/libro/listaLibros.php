<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
  function borrarLibro(id=""){
    if (confirm('¿Esta seguro que quiere borrar el lugar con id '+ id + '?')) {
      location.href = "<?php echo base_url(); ?>index.php/libro/borrarLibro?id=" + id;
    } 
  }
</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url(); ?>index.php/libro/index" method="post" id="formaBusqueda">
    <div class="row">
      <div class="col-md-3 form-group">
        <label for="nombre">Nombre del libro</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo set_value("nombre"); ?>">
        <small class="text-danger"><?php echo form_error("nombre");?></small>
      </div>

      <div class="col-md-3 form-group">
        <label for="latitud">Categoria</label>
        <input type="text" name="categoria" class="form-control" value="<?php echo set_value("categoria"); ?>">
        <small class="text-danger"><?php echo form_error("categoria");?></small>
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

  <?php if (tienePermiso("Libro","altas",$lista_permisos)): ?>
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-3 form-group">
        <button class="form-control btn btn-success" onclick="location.href='<?php echo base_url(); ?>index.php/libro/agregarLibro'">Agregar libro</button>
      </div>

      <div class="col-md-3 form-group">
        <button class="form-control btn btn-warning" onclick="location.href='<?php echo base_url(); ?>index.php/libro/verGrafica'">Graficas</button>
      </div>

      <div class="col-md-3"></div>
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
              <th>Descripción</th>
              <th>Categoria</th>
              <?php if (tienePermiso("Libro","cambios",$lista_permisos) ||
              tienePermiso("Libro","bajas",$lista_permisos)): ?>
              <th colspan="2">Acciones</th>
            <?php endif ?>
          </tr>
        </thead>
        <tbody>
          <?php
          if(isset($libros)){
            foreach ($libros as $libro) {
              echo "
              <tr>
              <th>$libro->libro_id</th>
              <td>$libro->nombre_lib</td>
              <td>$libro->descr_lib</td>
              <td>$libro->categoria_lib</td>";

              if (tienePermiso("Libro","cambios",$lista_permisos))
                echo "
              <td>
              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/libro/editarLibro?id={$libro->libro_id}'\" >Editar</button>
              </td>";

              if (tienePermiso("Libro","bajas",$lista_permisos))
                echo "
              <td>
              <button class='btn btn-dark form-control' onclick=\"borrarLibro('{$libro->libro_id}')\">Borrar</button>
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