<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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


  <div class="row">
    <div class="col-md-12">
      <figure>
        <div id="container"></div>
      </figure>
    </div>
  </div>
  

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
            </tr>
          </thead>
          <tbody>
          <?php
            if(isset($temperaturas)){
                $categories = "";
                $series = "";
                $lugar = "";
                if(count($temperaturas)>0){
                  $lugar = $temperaturas[0]->nombre;
                }
                foreach ($temperaturas as $temperatura) {
                    echo "
                        <tr>
                          <th>$temperatura->lugar_id</th>
                          <td>$temperatura->nombre</td>
                          <td>$temperatura->fecha</td>
                          <td>$temperatura->valor</td>
                        </tr>";
                    $categories .= "'$temperatura->fecha', ";
                    $series .= "$temperatura->valor, ";
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
  Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Temperatura'
    },
    subtitle: {
        text: '<?php echo $lugar; ?>'
    },
    xAxis: {
        categories: [<?php echo $categories; ?>]
    },
    yAxis: {
        title: {
            text: 'Temperature (°C)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: '<?php echo $lugar; ?>',
        data: [<?php echo $series; ?>]
    }]
});
</script>