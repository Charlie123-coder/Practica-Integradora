<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Si se va editar el registro de lugar recibirá la latitud y longitud
if($latitud == "" || $longitud == ""){
  $latitud = LAT_CENTRAL;
  $longitud = LNG_CENTRAL;
}

$coordenadas = "{ lat: $latitud, lng: $longitud }";

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


  <form action="<?php echo isset($url)?$url:base_url()."index.php/lugar/agregarLugar" ?>" method="post">
    <?php
      // Sólo se muestra cuando se edita
    $soloLectura = true;
    if(isset($idLugarAnterior) && $idLugarAnterior != "")
      echo "<input type='hidden' name='idLugarAnterior' value='$idLugarAnterior'>";
    ?>
    <div class="row">
      <?php
      // Sólo se muestra cuando se edita
      if(isset($idLugarAnterior) && $idLugarAnterior != "") :
        ?>
        <div class="col-md-12 form-group">
          <label for="id">Id del lugar</label>
          <input type="number" name="id" class="form-control" value="<?php echo set_value("id",isset($id)?$id:""); ?>" step="1" <?php if($soloLectura) echo "readonly"; ?>>
          <small class="text-danger"><?php echo form_error("id");?></small>
        </div>
        <?php
      endif
      ?>

      <div class="col-md-12 form-group">
        <label for="nombre">Nombre del lugar</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo set_value("nombre",isset($nombre)?$nombre:""); ?>" >
        <small class="text-danger"><?php echo form_error("nombre");?></small>
      </div>

      <div class="col-md-12 form-group">
        <label for="latitid">Latitud</label>
        <input type="number" name="latitud" id="latitud" class="form-control" value="<?php echo set_value("latitud",isset($latitud)?$latitud:""); ?>" step="0.000000001" min="-90" max="90">
        <small class="text-danger"><?php echo form_error("latitud");?></small>
      </div>

      <div class="col-md-12 form-group">
        <label for="longitud">Longitud</label>
        <input type="number" name="longitud" id="longitud"  class="form-control" value="<?php echo set_value("longitud",isset($longitud)?$longitud:""); ?>" step="0.000000001" min="-180" max="180">
        <small class="text-danger"><?php echo form_error("longitud");?></small>
      </div>

    </div>
    <div class="row">
        <div class="col-md-12 form-group">
          <div id="map" style="width: 100%; height: 600px; padding: 10px;"></div>
        </div>
      </div>
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-4 form-group">
        <input type="submit" class="form-control btn btn-primary" value="<?php echo $titulo; ?>">
      </div>
      <div class="col-md-4 form-group">
        <input type="regresar" class="form-control btn btn-success" value="Regresar" onclick="window.history.back();">
      </div>
      <div class="col-md-2"></div>
    </div>
  </form>


</div>
<!-- /.container -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrA8ZrzMgwhaYl1yTVtqptZdvCcq9SAmI"></script>
<script>
  let latCentral = <?php echo $latitud; ?>;
  let longCentral = <?php echo $longitud; ?>;
  let coordenadas = <?php echo $coordenadas; ?>;
  let map;
  let marcador;

  initMap();

  function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
      center: coordenadas,
      zoom: 17,
      mapTypeId: "OSM",
    });

    let punto = new google.maps.LatLng(latCentral,longCentral);
    let titulo = "<b>Coordenadas</b><br>"+latCentral+","+longCentral;
    marcador = crearMarcador(punto,titulo);



    google.maps.event.addListener(map, 'click', function(event) {
      if(marcador){
            marcador.setMap(null); // Borramos el marcador
            marcador = null;
          }

          let latLngActual = event.latLng;
          let lat = latLngActual.lat();
          let lng = latLngActual.lng();

          lat = lat.toFixed(9);
          lng = lng.toFixed(9);

          titulo = "<b>Coordenadas</b><br>"+lat+","+lng;
          marcador = crearMarcador(latLngActual,titulo);

          $("#latitud").val(lat);
          $("#longitud").val(lng);

        });


  }


  function crearMarcador(coordenada,titulo) {
    let marcador = new google.maps.Marker({
      position: coordenada,
      map: map,
      animation: google.maps.Animation.DROP,
    });

    google.maps.event.addListener(marcador, 'click', function() {
      infowindow = new google.maps.InfoWindow({
        size: new google.maps.Size(150,50)
      });
      infowindow.setContent(titulo);
      infowindow.open(map,marcador);
    });

    google.maps.event.trigger(marcador, 'click');

    return marcador;
  }

      //Define OSM map type pointing at the OpenStreetMap tile server
      map.mapTypes.set("OSM", new google.maps.ImageMapType({
        getTileUrl: function(coord, zoom) {
              // "Wrap" x (longitude) at 180th meridian properly
              // NB: Don't touch coord.x: because coord param is by reference, and changing its x property breaks something in Google's lib
              var tilesPerGlobe = 1 << zoom;
              var x = coord.x % tilesPerGlobe;
              if (x < 0) {
                x = tilesPerGlobe+x;
              }
              // Wrap y (latitude) in a like manner if you want to enable vertical infinite scrolling

              return "https://tile.openstreetmap.org/" + zoom + "/" + x + "/" + coord.y + ".png";
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 19
          }));

        </script>