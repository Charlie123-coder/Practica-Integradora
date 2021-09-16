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
  
  <?php
  
  if(isset($categorias)){
    $categories = "";
    $series = "";

    foreach ($categorias as $categoria) {
      $categories .= "'$categoria->categoria_lib', ";
      $series .= "$categoria->cantidad, ";
    }
  }

  ?>

</div>

<script type="text/javascript">
  Highcharts.chart('container', {
    chart: {
      type: 'bar'
    },
    title: {
      text: 'Categorias'
    },
    subtitle: {
      text: 'Grafica'
    },
    xAxis: {
      categories: [<?php echo $categories; ?>]
    },
    yAxis: {
      title: {
        text: 'Cantidad de libros'
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
      name: 'Cantidad',
      data: [<?php echo $series; ?>]
    }]
  });
</script>