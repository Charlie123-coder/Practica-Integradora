<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">World Book</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item <?php if($paginaActual=="seguridad") echo "active"; ?>">
            <a class="nav-link" href="<?php echo base_url(); ?>index.php/seguridad">Inicia sesión</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
