<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo base_url()."index.php/seguridad"; ?>" method="post">
    <div class="row">
      <div class='col-lg-4 mb-4'></div>
      <div class='col-lg-4 mb-4 form-group'>
        <label for="username">Usuario</label>
        <input type="text" name="username" id="username" maxlength="30" class="form-control" value="<?php echo set_value("username",isset($username)?$username:""); ?>">
        <small class="text-danger"><?php echo form_error("username");?></small>
      </div>
      <div class='col-lg4 mb-4'></div>
    </div>
    <!-- /.row -->

    <div class="row">
      <div class='col-lg-4 mb-4'></div>
      <div class='col-lg-4 mb-4 form-group'>
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control">
        <small class="text-danger"><?php echo form_error("password");?></small>
      </div>
      <div class='col-lg4 mb-4'></div>
    </div>
    <!-- /.row -->

    <div class="row">
      <div class='col-lg-4 mb-4'></div>
      <div class='col-lg-4 mb-4 form-group'>
        <input type="submit" class="btn btn-dark form-control" value="Entrar">
      </div>
      <div class='col-lg4 mb-4'></div>
    </div>
    <!-- /.row -->
  </form>

  <?php
    if($error!==FALSE){
        echo "
          <div class='row'>
            <div class='col-lg-4 mb-4'></div>
            <div class='col-lg-4 mb-4'>
              <small class='text-danger'>$error</small>
            </div>
            <div class='col-lg4 mb-4'></div>
          </div>
          <!-- /.row -->";
    }

  ?>
</div>
<!-- /.container -->