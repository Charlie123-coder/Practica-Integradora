<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lugar extends PidePassword {

	private $modulo;

	function __construct(){
        parent::__construct();
        $this->modulo = "Lugar";
    }

	public function index($paginaInicio=0,$numeroRegistros=10)
	{	

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("lugar",$error,$error);
			return;
		}

		// Se reciben los datos para la búsqueda
		$nombre = $this->obtenEntrada("nombre");
		$latitud = $this->obtenEntrada("latitud");
		$longitud = $this->obtenEntrada("longitud");
		$radio = $this->obtenEntrada("radio");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'nombre' => $nombre,
								'latitud' => $latitud,
								'longitud' => $longitud,
								'radio' => $radio,
								'paginaInicio' => $paginaInicio,
								'numeroRegistros' => $numeroRegistros
								);

		$this->form_validation->set_data($datosValidar);

		$this->form_validation->set_rules('nombre','nombre','trim|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('latitud','latitud','trim|callback_validaLatitud');
		$this->form_validation->set_rules('longitud','longitud','trim|callback_validaLongitud');
		$this->form_validation->set_rules('radio','radio','trim|numeric|greater_than[0]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');

		if($this->form_validation->run() === FALSE){
			$this->listaLugares($nombre,$latitud,$longitud,$radio,"");
			return;
		}

		$this->load->model('LugarModelo');
		$total = $this->LugarModelo->reporteLugares($nombre,$latitud,$longitud,$radio,$paginaInicio,$numeroRegistros,TRUE);
		$lugares = $this->LugarModelo->reporteLugares($nombre,$latitud,$longitud,$radio,$paginaInicio,$numeroRegistros);
		$consulta = "?nombre=$nombre&latitud=$latitud&longitud=$longitud&radio=$radio";
		$paginacion = paginacion(base_url()."index.php/lugar/index",$paginaInicio,$numeroRegistros,$consulta,$total);

		$this->listaLugares($nombre,$latitud,$longitud,$radio,$paginacion,$lugares);
	}

	private function listaLugares($nombre="",$latitud="",$longitud="",$radio="",$paginacion="",$lugares=array()){
		$datos = array();
		$datos['paginaActual'] = "lugar";
		$datos['titulo'] = "Bibliotecas";
		// La lista de lugares
		$datos['lugares'] = $lugares;
		$datos['paginacion'] = $paginacion;

		// Se envían los datos que se recibieron para la búsqueda
		$datos['nombre'] = $nombre;
		$datos['latitud'] = $latitud;
		$datos['longitud'] = $longitud;
		$datos['radio'] = $radio;
		

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;
	
		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('lugar/listaLugares',$datos);
		$this->load->view('componentes/piePagina');
	}
	

	public function agregarLugar(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"altas") === FALSE){
			$error = "No tiene permisos para agregar";
			$this->mostrarPaginaError("lugar",$error,$error);
			return;
		}

		// Se presionó el botón verde que está donde se muestra la lista de lugares, por lo que viene 
		// por medio de GET
		if(empty($_POST)){
			$this->muestraFormaLugar();
			return;
		}
		
		// Se presionó el botón de la forma y los datos vienen en el arreglo
		// POST
		$nombre = $this->obtenEntrada("nombre");
		$latitud = $this->obtenEntrada("latitud");
		$longitud = $this->obtenEntrada("longitud");

		$this->form_validation->set_rules('nombre','nombre','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('latitud','latitud','trim|required|callback_validaLatitud');
		$this->form_validation->set_rules('longitud','longitud','trim|required|callback_validaLongitud');

		if($this->form_validation->run() === FALSE){
			//$error = "Revise los datos ".validation_errors();
			$error = "";
			$this->muestraFormaLugar("lugar","Agregar lugar",$error);
			return;
		}

		$this->load->model('LugarModelo');
		if($this->LugarModelo->insertaLugar($nombre,$latitud,$longitud)){
			redirect(base_url()."index.php/lugar");
			return;
		}	

		$this->muestraFormaLugar("lugar","Agregar lugar","No se pudo agregar el lugar");
	}


	public function muestraFormaLugar($paginaActual="lugar",$titulo="Agregar lugar",$error="",
									$idLugarAnterior="",$id="",$nombre="",
									$latitud="",$longitud="",$url=""){
		$datos = array();
		$datos['paginaActual'] = $paginaActual;
		$datos['titulo'] = $titulo;
		$datos['url'] = $url;
		$datos['error'] = $error;

		$datos['idLugarAnterior'] = $idLugarAnterior;
		$datos['id'] = $id;
		$datos['nombre'] = $nombre;
		$datos['latitud'] = $latitud;
		$datos['longitud'] = $longitud;

		$this->load->model('PermisosModelo');
		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('lugar/formaLugar',$datos);
		$this->load->view('componentes/piePagina');	
	}


	public function borrarLugar(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"bajas") === FALSE){
			$error = "No tiene permisos para borrar";
			$this->mostrarPaginaError("lugar",$error,$error);
			return;
		}

		$id = $this->obtenEntrada("id");
		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'id' => $id,
								);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('id','id del lugar','trim|required|integer|greater_than[0]');


		if($this->form_validation->run() === FALSE){
			$error = "Error al recibir los datos del lugar con id: $id";
			$this->mostrarPaginaError("lugar",$error,$error);
			return;
		}

		$this->load->model('LugarModelo');
		if($this->LugarModelo->borrarLugar($id)){
			redirect(base_url()."index.php/lugar/");
			return;
		}

		$error = "Error al borrar el lugar con id: $id";
		$this->mostrarPaginaError("modulos",$error,$error);
	}



	public function editarLugar(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
			$error = "No tiene permisos para editar";
			$this->mostrarPaginaError("lugar",$error,$error);
			return;
		}

		// Si está vacío el POST tiene que cargar la forma con los datos del módulo
		if(empty($_POST)){
			$id = $this->obtenEntrada("id");
			// Como vienen los datos por GET se ponen las siguientes dos instrucciones
			$datosValidar = array(
									'id' => $id,
									);

			$this->form_validation->set_data($datosValidar);
			$this->form_validation->set_rules('id','id del lugar','trim|required|integer|greater_than[0]');

			if($this->form_validation->run() === FALSE){
				$error = "Error al editar el lugar con id: $id";
				$this->mostrarPaginaError("modulos",$error,$error);
				return;
			}

			$this->load->model('LugarModelo');
			$objetoLugar = $this->LugarModelo->obtenDatosLugar($id);

			if($objetoLugar === null || $objetoLugar === FALSE){
				$error = "Error al editar el lugar con id: $id, no existe";
				$this->mostrarPaginaError("lugar",$error,$error);
				return;
			}

			$this->muestraFormaLugar("lugar","Editar lugar","",
									$id, $objetoLugar->id, $objetoLugar->nombre,
									$objetoLugar->latitud, $objetoLugar->longitud,
									base_url()."index.php/lugar/editarLugar");
			return;
		}
		
		$idLugarAnterior = $this->obtenEntrada("idLugarAnterior");
		$id = $this->obtenEntrada("id");
		$nombre = $this->obtenEntrada("nombre");
		$latitud = $this->obtenEntrada("latitud");
		$longitud = $this->obtenEntrada("longitud");

		$this->form_validation->set_rules('idLugarAnterior','id del lugar anterior','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('id','id del lugar','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('nombre','nombre','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('latitud','latitud','trim|required|callback_validaLatitud');
		$this->form_validation->set_rules('longitud','longitud','trim|required|callback_validaLongitud');
		
		if($this->form_validation->run() === FALSE){

			$this->muestraFormaLugar("lugar", "Editar lugar", "",
									$idLugarAnterior, $id, $nombre,
									$latitud, $longitud,
									base_url()."index.php/lugar/editarLugar");
			return;
		}

		$this->load->model('LugarModelo');
		$objetoLugar = $this->LugarModelo->obtenDatosLugar($id);

		// if($objetoLugar !== null && $objetoLugar !== FALSE){
		// 	$error = "Error al editar el lugar con id: $id, ya existe";
		// 	$this->muestraFormaLugar("lugar", "Editar lugar", $error,
		// 							$idLugarAnterior, $id, $nombre,
		// 							$latitud, $longitud,
		// 							base_url()."index.php/lugar/editarLugar");
		// 	return;
		// }


		if($this->LugarModelo->editarLugar($idLugarAnterior, $id, $nombre,$latitud, $longitud)){
			redirect(base_url()."index.php/lugar/");
			return;
		}

		$error = "Error al editar el lugar con id: $id";
		$this->mostrarPaginaError("lugar",$error,$error);
	}
	
}