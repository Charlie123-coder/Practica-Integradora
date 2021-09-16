<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Page Content -->
<div class="container">

  <!-- Page Heading -->
  <h1 class="my-4"><?php echo $titulo; ?></h1>


  <form action="<?php echo (isset($url)?$url:base_url()."index.php/usuario/agregarUsuario") ?>" method="post">
    <?php
      if(isset($usernameAnterior))
        echo "<input type='hidden' name='usernameAnterior' value='$usernameAnterior'>";
    ?>
    <div class="row">
      <div class="col-md-12 form-group">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" class="form-control" value="<?php echo set_value("username",isset($username)?$username:""); ?>">
        <small class="text-danger"><?php echo form_error("username");?></small>
      </div>
      <div class="col-md-12 form-group">
        <label for="email">Email</label>
        <input type="text" name="email" class="form-control" value="<?php echo set_value("email",isset($email)?$email:""); ?>">
        <small class="text-danger"><?php echo form_error("email");?></small>
      </div>
      <div class="col-md-12 form-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" class="form-control" value="<?php echo set_value("password"); ?>">
        <small class="text-danger"><?php echo form_error("password");?></small>
      </div>
      <div class="col-md-12 form-group">
        <label for="password2">Repetir contraseña</label>
        <input type="password" name="password2" class="form-control" value="<?php echo set_value("password2"); ?>">
        <small class="text-danger"><?php echo form_error("password2");?></small>
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