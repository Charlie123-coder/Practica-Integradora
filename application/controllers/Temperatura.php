<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temperatura extends PidePassword {

	private $modulo;

	function __construct(){
		parent::__construct();
		$this->modulo = "Temperatura";
	}

	public function index($paginaInicio=0,$numeroRegistros=10)
	{	

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}
		
		// Se reciben los datos para la búsqueda
		$lugar_id = $this->obtenEntrada("lugar_id");
		$fechaInicio = $this->obtenEntrada("fechaInicio");
		$fechaFinal = $this->obtenEntrada("fechaFinal");
		$temperaturaInicial = $this->obtenEntrada("temperaturaInicial");
		$temperaturaFinal = $this->obtenEntrada("temperaturaFinal");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
			'lugar_id' => $lugar_id,
			'fechaInicio' => $fechaInicio,
			'fechaFinal' => $fechaFinal,
			'temperaturaInicial' => $temperaturaInicial,
			'temperaturaFinal' => $temperaturaFinal,
			'paginaInicio' => $paginaInicio,
			'numeroRegistros' => $numeroRegistros
		);

		$this->form_validation->set_data($datosValidar);

		$this->form_validation->set_rules('lugar_id','id del lugar','trim|integer|greater_than[0]');
		$this->form_validation->set_rules('fechaInicio','fecha de inicio','trim|callback_validaFecha');
		$this->form_validation->set_rules('fechaFinal','fecha final','trim|callback_validaFecha');
		$this->form_validation->set_rules('temperaturaInicial','temperatura inicial','trim|numeric');
		$this->form_validation->set_rules('temperaturaFinal','temperatura final','trim|numeric');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');

		if($this->form_validation->run() === FALSE){
			$this->listaTemperaturas($lugar_id,$fechaInicio,$fechaFinal,$temperaturaInicial,$temperaturaFinal);
			return;
		}

		$this->load->model('TemperaturaModelo');
		$total = $this->TemperaturaModelo->reporteTemperaturas($lugar_id,$fechaInicio,$fechaFinal,$temperaturaInicial,$temperaturaFinal,$paginaInicio,$numeroRegistros,TRUE);
		$temperaturas = $this->TemperaturaModelo->reporteTemperaturas($lugar_id,$fechaInicio,$fechaFinal,$temperaturaInicial,$temperaturaFinal,$paginaInicio,$numeroRegistros);
		$consulta = "?lugar_id=$lugar_id&fechaInicio=$fechaInicio&fechaFinal=$fechaFinal&temperaturaInicial=$temperaturaInicial&temperaturaFinal=$temperaturaFinal";
		$paginacion = paginacion(base_url()."index.php/temperatura/index",$paginaInicio,$numeroRegistros,$consulta,$total);

		$this->listaTemperaturas($lugar_id,$fechaInicio,$fechaFinal,$temperaturaInicial,$temperaturaFinal,$paginacion,$temperaturas);
	}


	private function listaTemperaturas($lugar_id="",$fechaInicio="",$fechaFinal="",$temperaturaInicial="",
		$temperaturaFinal="",$paginacion="",$temperaturas=array()){
		$datos = array();
		$datos['paginaActual'] = "temperatura";
		$datos['titulo'] = "Temperaturas";
		// La lista de lugares
		$datos['temperaturas'] = $temperaturas;
		$datos['paginacion'] = $paginacion;

		// Se envían los datos que se recibieron para la búsqueda
		$datos['lugar_id'] = $lugar_id;
		$datos['fechaInicio'] = $fechaInicio;
		$datos['fechaFinal'] = $fechaFinal;
		$datos['temperaturaInicial'] = $temperaturaInicial;
		$datos['temperaturaFinal'] = $temperaturaFinal;

		$this->load->model('LugarModelo');
		$totalLugares = $this->LugarModelo->reporteLugares("","","","",0,0,TRUE);
		$lugares = $this->LugarModelo->reporteLugares("","","","",0,$totalLugares);
		$datos['lugares'] = $lugares;

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezadoFecha',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('temperatura/listaTemperaturas',$datos);
		$this->load->view('componentes/piePagina');
	}
	

	public function agregarTemperatura(){

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"altas") === FALSE){
			$error = "No tiene permisos para agregar";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}

		// Se presionó el botón verde que está donde se muestra la lista de temperatura, por lo que viene 
		// por medio de GET
		if(empty($_POST)){
			$this->muestraFormaTemperatura();
			return;
		}
		
		// Se presionó el botón de la forma y los datos vienen en el arreglo
		// POST
		$lugar_id = $this->obtenEntrada("lugar_id");
		$fecha = $this->obtenEntrada("fecha");
		$valor = $this->obtenEntrada("valor");

		$this->form_validation->set_rules('lugar_id','id del lugar','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('fecha','fecha y hora','trim|required|callback_validaFechaHora');
		$this->form_validation->set_rules('valor','temperatura','trim|required|numeric');

		if($this->form_validation->run() === FALSE){
			//$error = "Revise los datos ".validation_errors();
			$error = "";
			$this->muestraFormaTemperatura("temperatura","Agregar temperatura",$error);
			return;
		}

		$this->load->model('TemperaturaModelo');
		if($this->TemperaturaModelo->insertaTemperatura($lugar_id,$fecha,$valor)){
			redirect(base_url()."index.php/temperatura");
			return;
		}	

		$this->muestraFormaTemperatura("temperatura","Agregar temperatura","No se pudo agregar la temperatura");
	}


	public function muestraFormaTemperatura($paginaActual="temperatura",$titulo="Agregar temperatura",$error="",
		$fechaAnterior="",$idLugarAnterior="",$lugar_id="",$fecha="",$valor="",$url=""){
		$datos = array();
		$datos['paginaActual'] = $paginaActual;
		$datos['titulo'] = $titulo;
		$datos['url'] = $url;
		$datos['error'] = $error;

		$datos['idLugarAnterior'] = $idLugarAnterior;
		$datos['fechaAnterior'] = $fechaAnterior;
		$datos['lugar_id'] = $lugar_id;
		$datos['fecha'] = $fecha;
		$datos['valor'] = $valor;

		$this->load->model('LugarModelo');
		$totalLugares = $this->LugarModelo->reporteLugares("","","","",0,0,TRUE);
		$lugares = $this->LugarModelo->reporteLugares("","","","",0,$totalLugares);
		$datos['lugares'] = $lugares;

		$this->load->model('PermisosModelo');
		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('temperatura/formaTemperatura',$datos);
		$this->load->view('componentes/piePagina');	
	}


	public function editarTemperatura(){
		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
			$error = "No tiene permisos para editar";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}

		// Si está vacío el POST tiene que cargar la forma con los datos del módulo
		if(empty($_POST)){
			$lugar_id = $this->obtenEntrada("lugar_id");
			$fecha = $this->obtenEntrada("fecha");

			// Como vienen los datos por GET se ponen las siguientes dos instrucciones
			$datosValidar = array(
				'lugar_id' => $lugar_id,
				'fecha' => $fecha,
			);

			$this->form_validation->set_data($datosValidar);
			$this->form_validation->set_rules('lugar_id','id del lugar','trim|required|integer|greater_than[0]');
			$this->form_validation->set_rules('fecha','Fecha','required');

			if($this->form_validation->run() === FALSE){
				$error = "Error al editar la temperatura con id de lugar: $lugar_id";
				$this->mostrarPaginaError("modulos",$error,$error);
				return;
			}

			$this->load->model('TemperaturaModelo');
			$objetoLugar = $this->TemperaturaModelo->obtenDatosTemperatura($lugar_id,$fecha);

			if($objetoLugar === null || $objetoLugar === FALSE){
				$error = "Error al editar la temperatura con el id de lugar: $id, no existe";
				$this->mostrarPaginaError("temperatura",$error,$error);
				return;
			}

			$this->muestraFormaTemperatura("temperatura","Editar temperatura","",
				$fecha,$lugar_id, $lugar_id, $objetoLugar->fecha, $objetoLugar->valor ,
				base_url()."index.php/temperatura/editarTemperatura");
			return;
		}
		
		$idLugarAnterior = $this->obtenEntrada("idLugarAnterior"); //escondido
		$lugar_id = $this->obtenEntrada("lugar_id"); //El que aparece en formulario
		$fechaAnterior = $this->obtenEntrada("fechaAnterior"); //escondido
		$fecha = $this->obtenEntrada("fecha"); //en formulario
		$valor = $this->obtenEntrada("valor"); //en formulario
		
		

		$this->form_validation->set_rules('idLugarAnterior','id del lugar anterior','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('lugar_id', 'lugar_id', 'trim|integer|greater_than[0]');
		$this->form_validation->set_rules('fecha', 'fecha y hora','trim|required|callback_validaFechaHora');
		$this->form_validation->set_rules('fechaAnterior', 'fechaAn', 'trim');
		$this->form_validation->set_rules('valor', 'Temperatura', 'required|numeric');	

		
		
		if($this->form_validation->run() === FALSE){

			$this->muestraFormaTemperatura("temperatura","Editar temperatura","",
				$fechaAnterior,$idLugarAnterior,$lugar_id, $fecha,$valor,
				base_url()."index.php/temperatura/editarTemperatura");
			return;
		}

		$this->load->model('TemperaturaModelo');
		$objetoLugar = $this->TemperaturaModelo->obtenDatosTemperatura($lugar_id, $fecha);


		// if($objetoLugar !== null && $objetoLugar !== FALSE){
		// 	$error = "Error al editar el lugar con id: $lugar_id, ya existe";

		// 	$this->muestraFormaTemperatura("temperatura","Editar temperatura","",
		// 		$fechaAnterior,$idLugarAnterior,$lugar_id, $fecha,$valor,
		// 		base_url()."index.php/temperatura/editarTemperatura");
		// 	print($fechaAnterior);
		// 	return;
		// }
		print($fechaAnterior);


		if($this->TemperaturaModelo->editarTemperatura($idLugarAnterior, $lugar_id, $fecha, $fechaAnterior, $valor)){
			redirect(base_url()."index.php/temperatura/");
			return;
		}

		$error = "Error al editar el lugar con id: $lugar_id";
		$this->mostrarPaginaError("temperatura",$error,$error);
	}

	public function borrarTemperatura(){

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"bajas") === FALSE){
			$error = "No tiene permisos para borrar";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}

		$lugar_id = $this->obtenEntrada("lugar_id");
		$fecha = $this->obtenEntrada("fecha");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
			'lugar_id' => $lugar_id,
			'fecha' => $fecha,
		);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('lugar_id','id del lugar','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('fecha','fecha y hora','trim|required|callback_validaFechaHora');


		if($this->form_validation->run() === FALSE){
			$error = "Error al recibir los datos de la temperatura con id: $lugar_id y fecha: $fecha";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}

		$this->load->model('TemperaturaModelo');
		if($this->TemperaturaModelo->borrarTemperatura($lugar_id,$fecha)){
			redirect(base_url()."index.php/temperatura/");
			return;
		}

		$error = "Error al borrar la temperatura con id: $lugar_id y fecha: $fecha";
		$this->mostrarPaginaError("temperatura",$error,$error);
	}

	public function mostrarTemperaturas($paginaInicio=0,$numeroRegistros=10)
	{	

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("temperatura",$error,$error);
			return;
		}
		
		// Se reciben los datos para la búsqueda
		$lugar_id = $this->obtenEntrada("lugar_id");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
			'lugar_id' => $lugar_id,
			'paginaInicio' => $paginaInicio,
			'numeroRegistros' => $numeroRegistros
		);

		$this->form_validation->set_data($datosValidar);

		$this->form_validation->set_rules('lugar_id','id del lugar','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');

		if($this->form_validation->run() === FALSE){
			$error="Verifique los datos: ".validation_errors();
			$this->mostrarGraficaTemperaturas($lugar_id,"Gráfica temperaturas",$error);
			return;
		}

		$this->load->model('TemperaturaModelo');
		$total = $this->TemperaturaModelo->reporteTemperaturas($lugar_id,"","","","",$paginaInicio,$numeroRegistros,TRUE);
		$temperaturas = $this->TemperaturaModelo->reporteTemperaturas($lugar_id,"","","","",$paginaInicio,$numeroRegistros);
		$consulta = "?lugar_id=$lugar_id";
		$paginacion = paginacion(base_url()."index.php/temperatura/mostrarTemperaturas",$paginaInicio,$numeroRegistros,$consulta,$total);

		$this->mostrarGraficaTemperaturas($lugar_id,"Gráfica temperaturas","",$paginacion,$temperaturas);
	}



	private function mostrarGraficaTemperaturas($lugar_id="",$titulo="Gráfica temperaturas",$error="",
		$paginacion="",$temperaturas=array()){
		$datos = array();
		$datos['paginaActual'] = "temperatura";
		$datos['titulo'] = $titulo;
		$datos['error'] = $error;
		// La lista de lugares
		$datos['temperaturas'] = $temperaturas;
		$datos['paginacion'] = $paginacion;

		// Se envían los datos que se recibieron para la búsqueda
		$datos['lugar_id'] = $lugar_id;

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;
		
		$this->load->view('componentes/encabezadoGrafica',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('temperatura/mostrarGraficaTemperaturas',$datos);
		$this->load->view('componentes/piePagina');
	}
	
}









