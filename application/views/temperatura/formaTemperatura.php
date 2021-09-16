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


  <form action="<?php echo isset($url)?$url:base_url()."index.php/temperatura/agregarTemperatura" ?>" method="post">
    <?php
      // Sólo se muestra cuando se edita
      if(isset($fechaAnterior) && $fechaAnterior != "")
        echo "<input type='hidden' name='fechaAnterior' value='$fecha'>";
      if(isset($idLugarAnterior) && $idLugarAnterior != "")
        echo "<input type='hidden' name='idLugarAnterior' value='$idLugarAnterior'>";
    ?>
    <div class="row">


        <div class="col-md-12 form-group">
          <label for="lugar_id">Lugar</label>
          <select class="form-control" name="lugar_id">
            <option value="">Seleccione un lugar</option>
            <?php
         
            if($lugares!=null){
              foreach ($lugares as $lugar) {
               if($lugar->id===$lugar_id){
               ?>
              <option value="<?=$lugar->id?>" selected="selected"><?=$lugar->nombre?></option>;
              <?php
              } else {
              ?>
                <option value="<?=$lugar->id?>">
                <?=$lugar->nombre?> </option>
          <?php
              }
            }
          }
          ?>
        </select>
          <small class="text-danger"><?php echo form_error("lugar_id");?></small>
        </div>

        <div class="col-md-12 form-group">
          <label for="fecha">Fecha y hora</label>
          <input type="text" name="fecha" class="form-control" value="<?php echo set_value("fecha",isset($fecha)?$fecha:""); ?>" placeholder="YYYY-MM-DD HH:MM:SS">
          <small class="text-danger"><?php echo form_error("fecha");?></small>
        </div>

        <div class="col-md-12 form-group">
          <label for="valor">Valor de temperatura</label>
          <input type="number" name="valor" class="form-control" value="<?php echo set_value("valor",isset($valor)?$valor:""); ?>" step="0.01">
          <small class="text-danger"><?php echo form_error("valor");?></small>
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