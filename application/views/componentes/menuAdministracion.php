<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="http://dtai.uteq.edu.mx/~carlun192/worldBook/index.php/usuario/">World Book</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <?php if (tienePermiso("Usuario","consultas",$lista_permisos) || 
          tienePermiso("Modulo","consultas",$lista_permisos)): ?>

          <li class="nav-item dropdown <?php if($paginaActual=="usuarios") echo "active"; ?>">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Usuarios</a>
            <div class="dropdown-menu" aria-labelledby="dropdownUsuarios">

              <?php if (tienePermiso("Usuario","consultas",$lista_permisos)): ?>
                <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/usuario/">Usuarios</a>
              <?php endif ?>

              <?php if (tienePermiso("Modulo","consultas",$lista_permisos)): ?>
                <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/modulo/">Módulos</a>
              <?php endif ?>

              <?php if (tienePermiso("Graficas","consultas",$lista_permisos)): ?>
                <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/grafica/">Gráficas</a>
              <?php endif ?>
            </div>
          </li>
        <?php endif ?>

        <?php if (tienePermiso("Lugar","consultas",$lista_permisos) || tienePermiso("Temperatura","consultas",$lista_permisos) || tienePermiso("Libro","consultas",$lista_permisos)): ?>

        <li class="nav-item dropdown <?php if($paginaActual=="lugar") echo "active"; ?>">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Consulta</a>
          <div class="dropdown-menu" aria-labelledby="dropdownLugares">

            <?php if (tienePermiso("Libro","consultas",$lista_permisos)): ?>
              <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/libro/">Libros</a>
            <?php endif ?>

            <?php if (tienePermiso("Lugar","consultas",$lista_permisos)): ?>
              <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/lugar/">Bibliotecas</a>
            <?php endif ?>

            <?php if (tienePermiso("Temperatura","consultas",$lista_permisos)): ?>
              <a class="dropdown-item" href="<?php echo base_url(); ?>index.php/temperatura/">Temperaturas</a>
            <?php endif ?>

          </div>
        </li>
      <?php endif ?>

      <li class="nav-item <?php if($paginaActual=="seguridad") echo "active"; ?>">
        <a class="nav-link" href="<?php echo base_url(); ?>index.php/seguridad/salir">Finalizar sesión</a>
      </li>
    </ul>
  </div>
</div>
</nav>














