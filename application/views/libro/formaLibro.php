<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//die("habilitado $habilitado");
?>
<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>

  <?php
   if(isset($error) && $error != ""){
      echo "
      <div class='alert alert-danger'>
        $error
      </div>";
   }
  ?>


  <form action="<?php echo isset($url)?$url:base_url()."index.php/libro/agregarLibro" ?>" method="post">
    <?php
      // Sólo se muestra cuando se edita
      if(isset($idLibroAnterior) && $idLibroAnterior != "")
        echo "<input type='hidden' name='idLibroAnterior' value='$idLibroAnterior'>";
    ?>
    <div class="row">
      <?php
      // Sólo se muestra cuando se edita
      if(isset($idLibroAnterior) && $idLibroAnterior != "") :
      ?>
        <div class="col-md-12 form-group">
          <label for="id">Id del libro</label>
          <input type="number" name="id" class="form-control" value="<?php echo set_value("id",isset($libro_id)?$libro_id:""); ?>"  step="1">
          <small class="text-danger"><?php echo form_error("id");?></small>
        </div>
      <?php
      endif
      ?>

        <div class="col-md-12 form-group">
          <label for="nombre">Nombre del libro</label>
          <input type="text" name="nombre" class="form-control" value="<?php echo set_value("nombre",isset($nombre_lib)?$nombre_lib:""); ?>" >
          <small class="text-danger"><?php echo form_error("nombre");?></small>
        </div>

        <div class="col-md-12 form-group">
          <label for="descripcion">Descripcion</label>
          <input type="text" name="descripcion" class="form-control" value="<?php echo set_value("latitud",isset($descr_lib)?$descr_lib:""); ?>">
          <small class="text-danger"><?php echo form_error("descripcion");?></small>
        </div>

        <div class="col-md-12 form-group">
          <label for="categoria">Categoria</label>
          <input type="text" name="categoria" class="form-control" value="<?php echo set_value("categoria",isset($categoria_lib)?$categoria_lib:""); ?>">
          <small class="text-danger"><?php echo form_error("categoria");?></small>
        </div>

        <div class="col-md-12 form-group">
          <label for="copias">Número de copias</label>
          <input type="number" name="copias" class="form-control" value="<?php echo set_value("copias",isset($copias_lib)?$copias_lib:""); ?>">
          <small class="text-danger"><?php echo form_error("copias");?></small>
        </div>

    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4 form-group">
        <input type="submit" class="form-control btn btn-primary" value="<?php echo $titulo; ?>">
      </div>
      <div class="col-md-4"></div>
    </div>
  </form>


</div>
<!-- /.container -->