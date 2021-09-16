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


  <form action="<?php echo isset($url)?$url:base_url()."index.php/modulo/agregarModulo" ?>" method="post">
    <?php
      // Para cuando se edita
      if(isset($moduloAnterior))
        echo "<input type='hidden' name='moduloAnterior' value='$moduloAnterior'>";
    ?>
    <div class="row">
      <div class="col-md-12 form-group">
        <label for="modulo">Nombre del módulo</label>
        <input type="text" name="modulo" class="form-control" value="<?php echo set_value("modulo",isset($modulo)?$modulo:""); ?>" >
        <small class="text-danger"><?php echo form_error("modulo");?></small>
      </div>
      <div class="col-md-12 form-group">
        <label for="habilitado">Habilitado</label>
        <select class="form-control" name="habilitado">
          <option value="">Seleccione un valor</option>
          <option value="0" <?php echo ((isset($habilitado) && $habilitado!='')?selecciona($habilitado,0):set_select("habilitado",0)) ?>>Deshabilitado</option>
          <option value="1" <?php echo ((isset($habilitado) && $habilitado!='')?selecciona($habilitado,1):set_select("habilitado",1)) ?>>Habilitado</option>
        </select>
        <small class="text-danger"><?php echo form_error("habilitado");?></small>
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