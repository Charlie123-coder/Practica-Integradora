<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('selecciona'))
{
	function selecciona($variable,$valor){
		//die("$variable $valor");
		if($variable == $valor){
        	return 'selected="selected"';
    	}
    	return "";
  	}
}

if ( ! function_exists('paginacion'))
{
	function paginacion($url="",$registroActual=0,$numeroRegistrosPorPagina=10,$consulta="",$total=0){
		$linksPaginas = "";

		if($registroActual>$total)
			$registroActual=$total;
	
		if($numeroRegistrosPorPagina<=0)
			$numeroRegistrosPorPagina = 10;
		
		// Siempre se van a mostrar máximo 10 numeros de página	
		$cantidadPaginas  = intval($total)/$numeroRegistrosPorPagina;
		
		$botonInicio = 0;
		$botonFinal = intval($cantidadPaginas)*$numeroRegistrosPorPagina;
		if($botonFinal>=$total)
			$botonFinal = (intval($cantidadPaginas)-1)*$numeroRegistrosPorPagina;
		
		$paginaActual =	$registroActual/$numeroRegistrosPorPagina;
		
		// Siempre se van a mostrar máximo 10 numeros de página		
		$botonAnterior = 0;
		if($paginaActual>1)
			$botonAnterior = $paginaActual*$numeroRegistrosPorPagina-$numeroRegistrosPorPagina;
			
		// Siempre se van a mostrar máximo 10 numeros de página	
		$botonSiguiente = intval($cantidadPaginas)*$numeroRegistrosPorPagina;
		if($paginaActual<intval($cantidadPaginas))
			$botonSiguiente = $paginaActual*$numeroRegistrosPorPagina+$numeroRegistrosPorPagina;
		
		if($botonSiguiente>$botonFinal)
			$botonSiguiente = $botonFinal;
			
		// Siempre se van a mostrar máximo 10 numeros de página	
		if($cantidadPaginas>0 && $cantidadPaginas<=10){
			$inicioFor = 0;
			$finFor = $cantidadPaginas;
		}
		else if($cantidadPaginas>10 && $paginaActual<=5){
			$inicioFor = 0;
			$finFor = 10;
		}
		else if($cantidadPaginas>10 && $paginaActual>5){
			$inicioFor = $paginaActual-5;
			$finFor = $paginaActual+5;
			if($finFor>$cantidadPaginas)
				$finFor = $cantidadPaginas;
		}
		else{
			$inicioFor = 0;
			$finFor = 0;
		}
		/*	
		echo "Inicio for $inicioFor  Fin for $finFor Número de registros por p&aacute;gina $numeroRegistrosPorPagina
		      Total $total Cantidad $cantidadPaginas P&aacute;gina actual $paginaActual<br/>";
		*/	
		if ($cantidadPaginas > 0){  // se verifica que haya mas de una pagina

			$linksPaginas = "
				<ul class='pagination justify-content-center'>
					<li class='page-item'><a class='page-link' href='$url/$botonInicio/$numeroRegistrosPorPagina/$consulta'> Inicio </a></li>
					<li class='page-item'><a class='page-link' href='$url/$botonAnterior/$numeroRegistrosPorPagina/$consulta'> &laquo;</a></li>";

			for($i=$inicioFor; $i<$finFor; $i++){
				if($paginaActual!=$i)
					$linksPaginas .= "<li class='page-item'><a class='page-link' href='$url/".intval($i*$numeroRegistrosPorPagina)."/$numeroRegistrosPorPagina/$consulta'>".intval($i+1)."</a></li>";
				else
					$linksPaginas .= "<li class='page-item active'><a class='page-link' href='$url/".intval($i*$numeroRegistrosPorPagina)."/$numeroRegistrosPorPagina/$consulta'>".intval($i+1)."</a></li>";
			}
				
			$linksPaginas .= "
					<li class='page-item'><a class='page-link' href='$url/$botonSiguiente/$numeroRegistrosPorPagina/$consulta'>  &raquo;</a></li>
					<li class='page-item'><a class='page-link' href='$url/$botonFinal/$numeroRegistrosPorPagina/$consulta'> Fin </a></li>
				</ul>";
		}

		return $linksPaginas;
	}
}

if(! function_exists('tienePermiso')){

	function tienePermiso($modulo,$permiso,$listaPermisos){

		//die(print_r($listaPermisos));

		if(!empty($listaPermisos)){

			foreach ($listaPermisos as $permiso_row){

				$modulo_row = $permiso_row->modulo;

				if($modulo_row == $modulo){
					
					$privilege =  $permiso_row->$permiso;

					if($privilege == 1)
						return TRUE;
				}

			}

		}else
			return FALSE;
	}
}





