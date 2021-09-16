<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$tituloImprimir = "";
if(!empty($titulo))
	$tituloImprimir = $titulo;

?><!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $tituloImprimir; ?></title>

  <!-- Bootstrap core CSS -->
  <link href="<?php echo base_url();?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="<?php echo base_url();?>css/shop-homepage.css" rel="stylesheet">

  <!-- Bootstrap core JavaScript -->
  <script src="<?php echo base_url();?>vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url();?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
