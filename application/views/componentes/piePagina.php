<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<!-- alerta -->
	<div id="alerta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloAlerta" aria-hidden="true" style="display: none;">
	</div>

	<!-- Footer -->
  <footer class="py-5 bg-dark">
    <div class="container">
      <p class="m-0 text-center text-white">Copyright &copy; Esaú - José Daniel - Carlos</p>
    </div>
    <!-- /.container -->
  </footer>

  <script type="text/javascript">

  		function borrarForma(){
	      $(':input','#formaBusqueda')
	        .not(':button, :submit, :reset, :hidden')
	        .val('')
	        .prop('checked', false)
	        .prop('selected', false);
	    }
	    
  		function enviaAlerta(titulo="Error",mensaje="Error desconocido"){
			var contenidoAlerta = 	"<div class='modal-dialog modal-dialog-centered' role='document'>" +
										"<div class='modal-content'>" +
											"<div class='modal-header'>" +
												"<h5 class='modal-title' id='tituloAlerta'>"+ titulo +"</h5>" +
												"<button type='button' class='close' data-dismiss='modal' aria-label='Cerrar'>" +
												  "<span aria-hidden='true'>×</span>" +
												"</button>" +
											"</div>" +
											"<div class='modal-body'>" +
												"<p>"+ mensaje +"</p>" +
											"</div>" +
											"<div class='modal-footer'>" +
												"<button type='button' class='btn btn-secondary' data-dismiss='modal'>Ok</button>" +
											"</div>" +
										"</div>" +
									"</div>";

			document.getElementById("alerta").innerHTML = contenidoAlerta;
			$('#alerta').modal('show');
		}
  </script>

</body>

</html>