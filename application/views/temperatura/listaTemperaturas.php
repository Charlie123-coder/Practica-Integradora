<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
  function borrarTemperatura(lugar_id="",fecha=""){
    if (confirm('¿Esta seguro que quiere borrar la temperatura con fecha '+ fecha + ' y que pertenece al id del lugar ' + lugar_id +'?')) {
        location.href = "<?php echo base_url(); ?>index.php/temperatura/borrarTemperatura?lugar_id=" + lugar_id + "&fecha=" + fecha;
    } 
  }
</script>

<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url(); ?>index.php/temperatura/index" method="post" id="formaBusqueda">
    <div class="row">

      <div class="col-md-4 form-group">
        <label for="lugar_id">Lugar</label>
        <select class="form-control" name="lugar_id">
          <option value="">Seleccione un lugar</option>
          <?php
            if($lugares!=null){
              foreach ($lugares as $lugar) {
                echo "
                <option value='{$lugar->id}' ".set_select('lugar_id',$lugar->id).">{$lugar->nombre}</option>";
              }
            }
          ?>
        </select>
        <small class="text-danger"><?php echo form_error("lugar_id");?></small>
      </div>

      <div class="col-md-4 form-group">
        <label for="fechaInicio">Fecha inicio</label>
        <input type="text" class="form-control datepicker" name="fechaInicio" id="fechaInicio" maxlength="10" value="<?php echo set_value("fechaInicio"); ?>" readonly="readonly" />
        <small class="text-danger"></small>
      </div>

      <div class="col-md-4 form-group">
            <label for="fechaFinal">Fecha final</label>
            <input type="text" class="form-control datepicker" name="fechaFinal" id="fechaFinal" maxlength="10" value="<?php echo set_value("fechaFinal"); ?>" readonly="readonly" />
            <small class="text-danger"></small>
      </div>

    </div>
    <div class="row">

      <div class="col-md-6 form-group">
        <label for="temperaturaInicial">Temperatura inicial</label>
        <input type="number" name="temperaturaInicial" class="form-control" value="<?php echo set_value("temperaturaInicial"); ?>" step="0.01">
        <small class="text-danger"><?php echo form_error("temperaturaInicial");?></small>
      </div>

      <div class="col-md-6 form-group">
        <label for="temperaturaFinal">Temperatura final</label>
        <input type="number" name="temperaturaFinal" class="form-control" value="<?php echo set_value("temperaturaFinal"); ?>" step="0.01">
        <small class="text-danger"><?php echo form_error("temperaturaFinal");?></small>
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

  <?php if (tienePermiso("Temperatura","altas",$lista_permisos)): ?>
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 form-group">
      <button class="form-control btn btn-success" onclick="location.href='<?php echo base_url(); ?>index.php/temperatura/agregarTemperatura'">Agregar temperatura</button>
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
              <th>Id del lugar</th>
              <th>Nombre del lugar</th>
              <th>Fecha</th>
              <th>Temperatura</th>
              <?php if (tienePermiso("Temperatura","cambios",$lista_permisos) ||
                        tienePermiso("Temperatura","bajas",$lista_permisos)): ?>
              <th colspan="2">Acciones</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
          <?php
            if(isset($temperaturas)){
                foreach ($temperaturas as $temperatura) {
                    echo "
                        <tr>
                          <th>$temperatura->lugar_id</th>
                          <td>$temperatura->nombre</td>
                          <td>$temperatura->fecha</td>
                          <td>$temperatura->valor</td>";

                          if (tienePermiso("Temperatura","cambios",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-light form-control' onclick=\"location.href='".base_url()."index.php/temperatura/editarTemperatura?lugar_id={$temperatura->lugar_id}\">Editar</button>
                            </td>";

                          if (tienePermiso("Temperatura","bajas",$lista_permisos))
                            echo "
                            <td>
                              <button class='btn btn-dark form-control' onclick=\"borrarTemperatura('{$temperatura->lugar_id}','{$temperatura->fecha}')\">Borrar</button>
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


<script type="text/javascript">

    $(document).ready(function() {
      $('#fechaInicio').datepicker({
          format: "yyyy-mm-dd",
          language: "es",
          todayBtn: 'linked',
          autoclose: true,
          todayHighlight: true,
          endDate: "0d",
      });
    
      $('#fechaFinal').datepicker({
          format: "yyyy-mm-dd",
          language: "es",
          todayBtn: 'linked',
          autoclose: true,
          todayHighlight: true,
          endDate: "0d",
      });
    });
    
</script>




